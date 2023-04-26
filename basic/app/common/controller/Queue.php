<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/7/7   14:26
 * +----------------------------------------------------------------------
 * | className: 处理队列
 * +----------------------------------------------------------------------
 */

namespace app\common\controller;

use app\api\model\order\Order as OrderModel;
use app\common\components\helpers\RedisUtils;
use app\common\components\helpers\StockUtils;
use app\common\extend\hfpay\Payment;
use app\common\extend\yeepay\Divide;
use app\common\model\Blindbox;
use app\common\model\ChainThList;
use app\common\model\DivideOrder;
use app\common\model\Goods;
use app\common\model\Member;
use app\common\model\MemberBox;
use app\common\model\MemberChain;
use app\common\model\MemberGoods;
use app\common\model\MemberLabel;
use app\common\model\MemberLabelList;
use app\common\model\OrderGoods;
use app\common\model\Setting;
use app\common\model\Writer;
use app\notice\model\Order;
use app\notice\model\Order as noticeOrderModel;
use think\Controller;
use think\Exception;

class Queue extends Controller
{
    /**
     * 执行铸造
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface castingQueueExecute
     * @Time: 2022/7/7   14:27
     * common/Queue/castingQueueExecute
     */
    public function castingQueueExecute()
    {
        $redis = initRedis();
        $values = $redis->rpop('casting_question_list');
        $values_array = json_decode($values, true);
       
        if (!empty($values_array)) {
            $goodsInfo = (new Goods())::detail($values_array['goods_id']);
            if($goodsInfo['goods_price']==0){
                $goodsInfo['goods_price'] = 0.1;
            }
            $blockchain = Setting::getItem('blockchain');
            switch ($blockchain['default'])
            {
                case "BD": //百度链铸造
                    $result = (new Task())->grantShards($goodsInfo['asset_id'], $values_array['member_id'], $goodsInfo['goods_price']);
                    self::doLogs(array_merge(['log_title' => '百度铸造藏品日志--castingQueueExecute'], $result));
                    if (1 == $result['code']) {
                        $asset_id = $result['data']['asset_id'];
                        $shard_id = $result['data']['shard_id'];
                        (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                            'asset_id' => $asset_id,
                            'shard_id' => $shard_id
                        ]);
                        //写入查询队列  进行执行 查询队列的上链
                        $redis->lpush('details_question_list', json_encode([
                            'member_goods_id' => $values_array['member_goods_id'],
                            'asset_id' => $asset_id,
                            'operation_id' => $shard_id,
                        ]));
                    } else {
                        self::doLogs(array_merge(['log_title' => '百度上链失败日志--castingQueueExecute'], $values_array));
                        $redis->lpush('casting_question_list', json_encode([
                            'member_goods_id' => $values_array['member_goods_id'],
                            'goods_id' => $values_array['goods_id'],
                            'member_id' => $values_array['member_id']
                        ]));
                    }
                    break;
                case "WC": //文昌链模式
                    $res = (new Task())->createAssets($goodsInfo['goods_thumb'], $goodsInfo['goods_name'], $goodsInfo['stock_num'], $goodsInfo['goods_price'], $goodsInfo['goods_name'], $values_array['member_id'], $goodsInfo['goods_id']);
                    self::doLogs(array_merge(['log_title' => '文昌铸造藏品日志--castingQueueExecute'], $res));
                    if (1 == $res['code']) {
                        $asset_id = $res['data']['asset_id'];
                        $operation_id = $res['data']['operation_id'];
                        (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                            'asset_id' => $asset_id,
                            'operation_id' => $operation_id
                        ]);
                        //写入查询队列  进行执行 查询队列的上链
                        $redis->lpush('details_question_list', json_encode([
                            'member_goods_id' => $values_array['member_goods_id'],
                            'asset_id' => $asset_id,
                            'operation_id' => $operation_id,
                        ]));
                    } else {
                        self::doLogs(array_merge(['log_title' => '文昌上链失败日志'], $values_array));
                        $redis->lpush('casting_question_list', json_encode([
                            'member_goods_id' => $values_array['member_goods_id'],
                            'goods_id' => $values_array['goods_id'],
                            'member_id' => $values_array['member_id']
                        ]));
                    }
                    break;
                case "TH": //发行铸造 用户调用铸造 铸造成功后直接进行地址转移 然后使用查询接口调用查询方法进行查询
                    $member_chain_info = MemberChain::details(['member_id'=>$values_array['member_id']]);
                    if(!empty($member_chain_info['t_address'])){
                        $contract_address = '';
                        $base_token_id = '';
                        if(!empty($goodsInfo['asset_id'])){
                            $contract_address = $goodsInfo['asset_id'];
                            $base_token_id = $goodsInfo['hash'];
                        }
                        $resultTh = (new Task())->createPublish($goodsInfo['goods_name'], 1, $goodsInfo['goods_price'], $values_array['member_id'], $goodsInfo['goods_id'],$contract_address,$base_token_id);
                        if(!empty($resultTh)){
                            //完善系统的合约地址 藏品表的合约地址 以及藏品表的hash
                            $asset_id = $resultTh['contractAddress'];
                            $operation_id = $resultTh['products'][0]['tokenId'];
                            if($resultTh['products'][0]['tokenId']!=-1){
                                //标识上链成功可以不用走查询 直接进行铸造成功
                                if(empty($goodsInfo['asset_id'])){
                                    (new Goods())->where(['goods_id'=>$values_array['goods_id']])->update([
                                        'asset_id'=>$resultTh['contractAddress'],
                                        'hash'=>$resultTh['products'][0]['tokenId']
                                    ]);
                                }else{
                                    (new Goods())->where(['goods_id'=>$values_array['goods_id']])->update([
                                        'hash'=>$resultTh['products'][0]['tokenId']
                                    ]);
                                }
                                //然后写入member_goods表处理铸造成功
                                $detail = (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->field("collection_number,id,goods_id,goods_no")->find();
                                $collection_number = createNumbering($detail['goods_id'],$detail);
                                (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                                    'asset_id' => $asset_id,
                                    'operation_id' => $operation_id,
                                    'cast_status' => 2,
                                    'cast_time' => time(),
                                    'hash_url' => $resultTh['products'][0]['transactionHash'],
                                    'collection_number' => $collection_number,
                                    'shard_id' =>$operation_id
                                ]);
                            }else{
                                (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                                    'asset_id' => $asset_id,
                                    'operation_id' => $operation_id,
                                    'shard_id' =>$operation_id
                                ]);
                                //写入查询队列  进行执行 查询队列的上链
                                $redis->lpush('details_question_list', json_encode([
                                    'member_goods_id' => $values_array['member_goods_id'],
                                    'asset_id' => $asset_id,
                                    'operation_id' => $operation_id,
                                    'methodName' => 'mint',
                                    'transactionHash' => $resultTh['products'][0]['transactionHash']
                                ]));
                            }
                        }
                    }
                    break;
                default: //不上链模式
                    $detail = (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->field("collection_number,id,goods_id,goods_no")->find();
                    $collection_number = createNumbering($detail['goods_id'],$detail);
                    (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                        'asset_id' => getGuidV4(),
                        'operation_id' => getNicknameGuidV4(),
                        'hash_url' => get_hash(),
                        'cast_status' => 2,
                        'cast_time' => time(),
                        'collection_number' => $collection_number
                    ]);
                    break;
            }
        }
    }


    /**
     * 执行转增
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface castingQueueExecute
     * @Time: 2022/7/7   14:27 47.114.117.89
     * common/Queue/increaseQueueExecute
     */
    public function increaseQueueExecute()
    {
        $redis = initRedis();
        $values = $redis->rpop('increase_question_list');
        $values_array = json_decode($values, true);
        if (!empty($values_array)) {
            $blockchain = Setting::getItem('blockchain');
            switch ($blockchain['default'])
            {
                case "WC":
                    $res = (new Task())->addCollection($values_array['asset_id'], $values_array['shard_id'], $values_array['member_id'], $values_array['to_member_id']);
                    self::doLogs(array_merge(['log_title' => '转增藏品--increaseQueueExecute'], $res));
                    if (1 == $res['code']) {
                        $asset_id = $res['data']['asset_id'];
                        $operation_id = $res['data']['shard_id'];
                        (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                            'asset_id' => $asset_id,
                            'operation_id' => $operation_id,
                            'shard_id' => $operation_id,
                        ]);
                        $redis->lpush('details_question_list', json_encode([
                            'member_goods_id' => $values_array['member_goods_id'],
                            'asset_id' => $asset_id,
                            'operation_id' => $operation_id,
                        ]));
                    } else {
                        self::doLogs(array_merge(['log_title' => '转增藏品失败日志'], $values_array));
                        $redis->lpush('increase_question_list', json_encode($values_array));
                    }
                    break;
                case "BD":
                    $result = (new Task())->addCollection($values_array['asset_id'], $values_array['shard_id'], $values_array['member_id'], $values_array['to_member_id']);
                    self::doLogs(array_merge(['log_title' => '转增藏品--increaseQueueExecute'], $result));
                    if (1 == $result['code']) {
                        $asset_id = $result['data']['asset_id'];
                        $operation_id = $result['data']['shard_id'];
                        (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                            'asset_id' => $asset_id,
                            'operation_id' => $operation_id,
                            'shard_id' => $operation_id,
                        ]);
                        $redis->lpush('details_question_list', json_encode([
                            'member_goods_id' => $values_array['member_goods_id'],
                            'asset_id' => $asset_id,
                            'operation_id' => $operation_id,
                        ]));
                    } else {
                        self::doLogs(array_merge(['log_title' => '转增藏品失败日志'], $values_array));
                        $redis->lpush('increase_question_list', json_encode($values_array));
                    }
                    break;
                case "TH":
                    $res = (new Task())->grantTransferTh($values_array['member_id'],$values_array['to_member_id'],$values_array['shard_id'],$values_array['asset_id']);
                    self::doLogs(array_merge(['log_title' => '转增藏品--increaseQueueExecute'], $res));
                    if (isset($res['transactionHash']) && !empty($res['transactionHash'])) {
                        (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                            'asset_id' => $values_array['asset_id'],
                            'operation_id' => $values_array['shard_id'],
                            'shard_id' => $values_array['shard_id'],
                            'hash_url' => $res['transactionHash'],
                            'cast_status' => 2,
                            'cast_time' => time(),
                        ]);
                    } else {
                        self::doLogs(array_merge(['log_title' => '转增藏品失败日志'], $values_array));
                        $redis->lpush('increase_question_list', json_encode($values_array));
                    }
                    break;
                default:
                    (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                        'asset_id' => getGuidV4(),
                        'operation_id' => getNicknameGuidV4(),
                        'hash_url' => get_hash(),
                        'cast_status' => 2,
                        'cast_time' => time()
                    ]);
                    break;

            }
        }
    }


    /**
     * 执行查询藏品详情
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getDetailsExecute
     * @Time: 2022/7/8   00:06
     */
    public function getDetailsExecute()
    {
        $redis = initRedis();
        $values = $redis->rpop('details_question_list');
        $values_array = json_decode($values, true);
        $blockchain = Setting::getItem('blockchain');
        if (!empty($values_array)) {
            switch ($blockchain['default'])
            {
                case "BD":
                    $res = (new Task())->querysds($values_array['asset_id'], $values_array['operation_id']);
                    self::doLogs(array_merge(['log_title' => '百度查询上链状态--getDetailsExecute'], $res));
                    if ($res['code'] == 1 && $res['data']['status'] == 0) {
                        //这个地方执行下 生成上链的编号
                        $detail = (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->field("collection_number,id,goods_id,goods_no")->find();
                        $collection_number = createNumbering($detail['goods_id'],$detail);
                        (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                            'hash_url' => $res['data']['tx_id'],
                            'cast_status' => 2,
                            'cast_time' => time(),
                            'collection_number' => $collection_number
                        ]);
                    } else {
                        $redis->lpush('details_question_list', json_encode($values_array));
                    }
                    break;
                case "WC":
                    $res = (new Task())->getWindUpDetails($values_array['operation_id']);
                    self::doLogs(array_merge(['log_title' => '文昌查询上链状态--getDetailsExecute'], $res));
                    if ($res['code'] == 1 && $res['data']['status'] == 1) {
                        $detail = (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->field("collection_number,id,goods_id,goods_no")->find();
                        $collection_number = createNumbering($detail['goods_id'],$detail);
                        (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                            'hash_url' => $res['data']['tx_id'],
                            'shard_id' => $res['data']['nft_id'],
                            'cast_status' => 2,
                            'cast_time' => time(),
                            'collection_number' => $collection_number
                        ]);
                    } else {
                        $redis->lpush('details_question_list', json_encode($values_array));
                    }
                    break;
                case "TH":
                    $res = (new Task())->getTransactionDetail($values_array['transactionHash'],$values_array['methodName'],$values_array['member_id']);
                    self::doLogs(array_merge(['log_title' => 'Th--getDetailsExecute'], $res));
                    if(isset($res['tokenId']) && $res['tokenId']!=-1){
                        //执行藏品的交易逻辑
                        $detail = (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->field("collection_number,id,goods_id,goods_no")->find();
                        $collection_number = createNumbering($detail['goods_id'],$detail);
                        (new MemberGoods())->where(['id' => $values_array['member_goods_id']])->update([
                            'cast_status' => 2,
                            'cast_time' => time(),
                            'collection_number' => $collection_number,
                            'hash_url' =>$values_array['asset_id']
                        ]);
                    }
                    break;

            }
        }
    }


    /**
     * 执行销毁
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface destroyExecute
     * (new Task())->consumeWrite($val['asset_id'], $val['shard_id'], $val['member_id']);
     * @Time: 2022/7/19   21:07
     */
    public function destroyExecute()
    {
        $redis = initRedis();
        $values = $redis->rpop('destroyList');
        $values_array = json_decode($values, true);
        if (!empty($values_array)) {
            $blockchain = Setting::getItem('blockchain');
            if(in_array($blockchain['default'], ['WC','BD'])){
                $res = (new Task())->consumeWrite($values_array['asset_id'], $values_array['shard_id'], $values_array['member_id']);
                self::doLogs(array_merge(['log_title' => '销毁上链--destroyExecute'], $res));
                if ($res['code'] != 1) {
                    $redis->lpush('destroyList', json_encode($values_array));
                }
            }else if($blockchain['default'] == 'TH'){
                $res = (new Task())->destroyDigitalCollections($values_array['member_id'],$values_array['asset_id'], $values_array['shard_id']);
                self::doLogs(array_merge(['log_title' => '销毁上链--destroyExecute'], $res));
            }

        }
    }


    /**
     * 定时器  每隔30分钟自动提交数据库的藏品 上链
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface againSubmit
     * @Time: 2022/8/10   01:13
     */
    public function againSubmit()
    {
       $redis = initRedis();
       if(!$redis->lLen("casting_question_list")){
           $meberGoodsList = (new MemberGoods())->where(['cast_status'=>1,'asset_id'=>['=','']])->select()->toArray();
           foreach ($meberGoodsList as $key=>$val){
               $redis->lpush('casting_question_list',json_encode([
                   'member_goods_id' => $val['id'],
                   'goods_id' => $val['goods_id'],
                   'member_id' => $val['member_id']
               ]));
           }
       }

    }

    /**
     * 定时器  每隔30分钟自动提交数据库的藏品 上链
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface againSubmit
     * @Time: 2022/8/10   01:13
     */
    public function againAddOnlineSubmit()
    {
        $redis = initRedis();
        if(!$redis->lLen("details_question_list")){
            $meberGoodsList = (new MemberGoods())->where(['cast_status'=>1,'asset_id'=>['<>','']])->select()->toArray();
            $blockchain = Setting::getItem('blockchain');
            foreach ($meberGoodsList as $key=>$val){
                //因为涉及到不同的链 需要获取不同的参数填写
                if($blockchain['default'] != 'TH'){
                    $redis->lpush('details_question_list',json_encode([
                        'member_goods_id' => $val['id'],
                        'asset_id' => $val['asset_id'],
                        'operation_id' => $val['operation_id'],
                    ]));
                }
            }
        }
    }

    /**
     * 异步同步库存、销量
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface synchronizeGoods
     * @Time: 2022/8/10   02:18
     */
    public function synchronizeGoods()
    {
        //循环正在售卖的藏品
        $goods_ids = (new Goods())->where(['is_del'=>0])->field("goods_id,product_types")->select();
        foreach ($goods_ids as $key=>$val){
            $stock_num_values = StockUtils::getStock($val['goods_id']);
            $num_values = StockUtils::getSoldStock($val['goods_id']);
            if($val['product_types']==5){
                (new Blindbox())->where(['goods_id'=>$val['goods_id']])->update(['stock_num'=>$stock_num_values,'sales_actual'=>$num_values]);
            }
            (new Goods())->where(['goods_id'=>$val['goods_id']])->update(['stock_num'=>$stock_num_values,'sales_actual'=>$num_values]);
        }
    }

    /**
     * 自动打标签
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface automaticLabeling
     * @Time: 2022/8/14   17:31
     * common/Queue/automaticLabeling
     */
    public function automaticLabeling()
    {
        //获取当前需要打标签的ID
        $label_type_id = (new MemberLabel())->where(['label_type'=>2])->field("id,goods_id")->select();
        foreach ($label_type_id as $key=>$val){
            (new MemberLabelList())->automatic($val);
        }
    }

    /**
     * 获取支付状态
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface hfGetPayStatus
     * @Time: 2022/10/4   15:57
     * common/Queue/hfGetPayStatus
     */
    public function hfGetPayStatus()
    {
        $redis = initRedis();
        //每隔2分钟批量处理一次订单 判断订单的取消时间进行批处理订单
        $hf_order_list = $redis->Lrange("hfOrderNoList",0,-1);
        foreach ($hf_order_list as $key=>$val){
            $values = json_decode($val,true);
            $result = (new Payment())->getPayStatus($values['order_no'], $values['date']);
            if(isset($result['trans_stat']) && $result['trans_stat']!='S' && $result['trans_stat']!='P' && $result['trans_stat']!='I'){
                //关闭这个订单  解锁藏品锁定的状态  队列移除这个数据  //判断订单类型
                $log_title = "订单ID{$values['order_no']}--执行数据订单回归取消订单--状态-{$result['trans_stat']}--描述--{$result['trans_resp_desc']}";
                $order_info = (new Order())->where(['order_no'=>$values['order_no']])->field("order_id,order_type,sale_goods_id,is_limit,member_id,pay_type,order_status")->find();
                if ($order_info['order_status']['value'] == 5) { //说明订单已经取消
                    $redis->lrem("hfOrderNoList",$val,0);
                    continue;
                }
                if($order_info['order_type']==3){
                    (new MemberGoods())->where(['id'=>$order_info['sale_goods_id']])->update(['goods_status'=>1]);
                }else if($order_info['order_type']==11){
                    (new MemberBox())->where(['id'=>$order_info['sale_goods_id']])->update(['box_status'=>20]);
                }else{
                    $Order_lock_key = "order_id_".$order_info['order_id'];
                    if(!$redis->exists($Order_lock_key)){
                        $redis->set($Order_lock_key, $order_info['order_id'],60);
                        //回归库存 直接redis 库存加1 锁单库存-1
                        $order_goods_info = (new OrderGoods())->where(['order_id'=>$order_info['order_id']])->field("goods_id")->find();
                        StockUtils::increaseStock($order_goods_info['goods_id'], 1);
                        StockUtils::addSoldStock($order_goods_info['goods_id'], -1);
                        if($order_info['is_limit']==20){
                            (new Member())->where(['member_id'=>$order_info['member_id']])->setInc('purchase_limit',1);
                        }
                    }
                }
                $redis->lrem("hfOrderNoList",$val,0);
                (new Order())->where(['order_id' => $order_info['order_id']])->update(['order_status' => 5]);
                self::doLogshf($log_title);
            }else if(isset($result['trans_stat']) && $result['trans_stat']=='S'){
                //获取到信息后 调用回调信息进行支付 状态修改
                $Order_info = (new OrderModel)->where(['order_no'=>$result['order_id']])->find();
                if (empty($Order_info)) {
                    $redis->lrem("hfOrderNoList",$val,0);
                    continue;
                }
                if ($Order_info['pay_status']['value'] == 2) {
                    $redis->lrem("hfOrderNoList",$val,0);
                    continue;
                }
                $arr = [
                    'transaction_id' => $result['platform_seq_id'],
                    'out_trade_no' => $result['order_id'],
                ];
                (new noticeOrderModel())->callBack($arr);
               $redis->lrem("hfOrderNoList",$val,0);
            }
        }
    }


    /**
     * 杉德快捷查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface hfGetPayStatus
     * @Time: 2022/10/4   15:57
     * common/Queue/sdGetPayStatus
     */
    public function sdGetPayStatus()
    {
        $redis = initRedis();
        //每隔2分钟批量处理一次订单 判断订单的取消时间进行批处理订单
        $hf_order_list = $redis->Lrange("sdOrderNoList",0,-1);
        if(empty($hf_order_list)){
            return true;
        }
        foreach ($hf_order_list as $key=>$val){
            $rem_key = "order_list_number:".$val;
            $result = (new \app\common\extend\sd\Order())->getQuickOrderDetails($val);
            self::doSdLogs(['log_title'=>"杉德快捷","content"=>$result,'order_sn'=>$val]);

            if(!empty($result['body']) && $result['body']['orderStatus'] == '02'){ //订单支付失败  取消订单锁
                $redis->lrem("sdOrderNoList",$val,0);
                $redis->del($rem_key);
                continue;
            }else if(!empty($result['body']) && $result['body']['orderStatus'] == '00'){
                //查询订单是否支付成功  成功也删除下订单
                $Order_info = (new OrderModel)->where(['order_no'=>$result['body']['order_id']])->find();
                if (empty($Order_info)) {
                    $redis->lrem("sdOrderNoList",$val,0);
                    $redis->del($rem_key);
                    continue;
                }
                if ($Order_info['pay_status']['value'] == 2) {
                    $redis->lrem("sdOrderNoList",$val,0);
                    $redis->del($rem_key);
                    continue;
                }
            }
            if($result['head']['respCode'] == '030005'){
                $redis->lrem("sdOrderNoList",$val,0);
                continue;
            }
            if($result['head']['respCode'] == '030098'){
                if($redis->get($rem_key)>=20){
                    $redis->lrem("sdOrderNoList",$val,0);
                    $redis->del($rem_key);
                    continue;
                }
                $redis->INCRBY($rem_key,1);
                continue;
            }
        }
    }


    /**
     * 杉德云账户查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface hfGetPayStatus
     * @Time: 2022/10/4   15:57
     * common/Queue/sdCloudAccount
     */
    public function sdCloudAccount()
    {
        $redis = initRedis();
        //每隔2分钟批量处理一次订单 判断订单的取消时间进行批处理订单
        $hf_order_list = $redis->Lrange("sdCloudOrderNoList",0,-1);
        if(empty($hf_order_list)){
            return true;
        }
        foreach ($hf_order_list as $key=>$val){
            $result = (new \app\common\extend\sd\Order())->getOrderDetails($val);
            self::doSdLogs($result);
            if($result['responseCode'] == '00000' && $result['orderStatus']=='02'){
                $redis->lrem("sdCloudOrderNoList",$val,0);
                continue;
            }else if($result['responseCode'] == '00000' && $result['orderStatus']=='00'){
                //查询订单是否支付成功  成功也删除下订单
                $Order_info = (new OrderModel)->where(['order_no'=>$result['order_id']])->find();
                if (empty($Order_info)) {
                    $redis->lrem("sdCloudOrderNoList",$val,0);
                    continue;
                }
                if ($Order_info['pay_status']['value'] == 2) {
                    $redis->lrem("sdCloudOrderNoList",$val,0);
                    continue;
                }
            }else if($result['responseCode'] == '05005'){
                $redis->lrem("sdCloudOrderNoList",$val,0);
            }
        }
    }

    /**
     * 易宝批处理 分润
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface batchAllYeePayDivide
     * @Time: 2022/10/14   14:39
     * common/queue/batchAllYeePayDivide  使用计划任务执行
     */
    public function batchAllYeePayDivide()
    {
        $list = (new DivideOrder())->where(['divideStatus'=>10])->limit(0,30)->select();
        foreach ($list as $key=>$value){
            (new Divide())->divideApply($value);
        }
    }


    /**
     * @Notes: 记录日志错误日志的
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doLogshf($values)
    {
        return write_log($values,RUNTIME_PATH.'/hfqueue');
    }

    /**
     * @Notes: 记录日志错误日志的
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doLogs($values)
    {
        return write_log($values,RUNTIME_PATH.'/queue');
    }

    /**
     * @Notes: 记录日志错误日志的
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doSdLogs($values)
    {
        return write_log($values,RUNTIME_PATH.'/sdqueue');
    }


    /**
     * 同步支付
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-09-15 17:21
     */
    public function synchronous()
    {
        (new \app\common\extend\ad\Pay())->synchronous();
    }

}