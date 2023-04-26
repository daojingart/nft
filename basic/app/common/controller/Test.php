<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/17   5:57 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 支付总控
 * +----------------------------------------------------------------------
 */

namespace app\common\controller;


use app\admin\model\order\Order as OrderModel;
use app\api\model\collection\Goods;
use app\common\model\Glory;
use app\common\model\Member;
use app\common\model\MemberBox;
use app\common\model\MemberChain;
use app\common\model\MemberGoods;
use app\common\model\MemberReal;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\Setting;
use app\common\model\Writer;
use app\common\model\WxSetting;

class Test extends \think\Controller
{
    public function index()
    {
        $redis = initRedis();
        //查询应该发放藏品的用户 查询订单  然后查询用户是否扣除这个
        $order_list = (new Order())->where(['pay_status'=>2,'order_type'=>5])->whereTime('create_time', 'today')->field("order_id,member_id,total_price,order_no")->select()->toArray();
        foreach ($order_list as $key=>$value){
            //查询会员收藏表是否存在 存在则取消这个订单  并进行退款 增加荣誉值
            $memberGoods = (new MemberGoods())->where(['member_id'=>$value['member_id'],'goods_id'=>'26'])->select()->toArray();
            if(empty($memberGoods)){
                self::doLogs("订单上链---订单ID{$value['order_id']}");
                //增加藏品表 进行上链 创建
                $orderGoods = (new OrderGoods())->where(['order_id'=>$value['order_id']])->find();
                $member_info = Member::detail(['member_id'=>$value['member_id']]);
                $goodsInfo = (new Goods())::detail($orderGoods['goods_id']);
                $Writer_info = (new Writer())->where(['id'=>$goodsInfo['writer_id']])->find();
                //添加藏品信息
                $goodsData = [
                    'member_id' => $value['member_id'],
                    'order_id' => $value['order_id'],
                    'order_no' => $value['order_no'],
                    'goods_id' => $orderGoods['goods_id'],
                    'phone' => $member_info['phone'],
                    'nickname' => $member_info['name'],
                    'goods_no' => $goodsInfo['goods_no'],
                    'goods_name' => $goodsInfo['goods_name'],
                    'goods_thumb' => $goodsInfo['goods_thumb'],
                    'goods_price' => $goodsInfo['goods_price'],
                    'total_num' => 1,
                    'writer_id' => $goodsInfo['writer_id'],
                    'writer_name' => $Writer_info['name'] ?? '',
                    'asset_id' => '',
                    'shard_id' => '',
                    'operation_id' => '',
                    'hash_url' => '',
                    'source_type' =>8,
                    'app_id' => '10001',
                ];
                (new MemberGoods())->add($goodsData);
                $member_goods_insertId = (new MemberGoods())->getLastInsID();
                $redis->lpush('creat_queue',json_encode([
                    'member_goods_id' => $member_goods_insertId,
                    'goods_id' => $orderGoods['goods_id'],
                    'member_id' => $value['member_id']
                ]));
            }else{
                self::doLogs("订单退款---订单ID{$value['order_id']}");
                //取消订单  并将支付的荣誉值进行退回
                //添加积分记录
                $integralData = [
                    'member_id' => $value['member_id'],
                    'type' => 3,
                    'amount' => $value['total_price'],
                    'remark' => '兑换藏品失败,荣誉值退还'
                ];
                (new Glory())->allowField(true)->save($integralData);
                (new Order())->where(['order_id'=>$value['order_id']])->update([
                    'pay_status'=>1,
                    'order_status' => 5,
                    'refund_status' => 3
                ]);

            }

        }
    }

    /**
     * 执行上链创建
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface queueExecute
     * @Time: 2022/7/6   23:36
     * common/test/index
     */
    public function queueExecute()
    {
        $redis = initRedis();
        $values = $redis->rpop('creat_queue');
        $values_array = json_decode($values,true);
        $asset_id = '';
        $shard_id = '';
        $operation_id = '';
        if(!empty($values_array)){
            //执行创建上链  上链成功后修改数据库数据 然后
            $goodsInfo = (new Goods())::detail($values_array['goods_id']);
            $Writer_info = (new Writer())->where(['id'=>$goodsInfo['writer_id']])->find();
            $res = (new Task())->createAssets($goodsInfo['goods_thumb'],$goodsInfo['goods_name'],$goodsInfo['stock_num'], $goodsInfo['goods_price'],$goodsInfo['goods_name'],$values_array['member_id'],$goodsInfo['goods_id']);
            self::doLogs(array_merge(['log_title'=>'创建藏品日志'], $res));
            if (1 == $res['code']) {
                $asset_id = $res['data']['asset_id'];
                $operation_id = $res['data']['operation_id'];
                (new MemberGoods())->where(['id'=>$values_array['member_goods_id']])->update([
                    'asset_id' => $asset_id,
                    'operation_id' => $operation_id
                ]);
            }else{
                self::doLogs(array_merge(['log_title'=>'上链失败日志'], $values_array));
                $redis->lpush('creat_queue',json_encode([
                    'member_goods_id' => $values_array['member_goods_id'],
                    'goods_id' => $values_array['goods_id'],
                    'member_id' => $values_array['member_id']
                ]));
            }
        }
    }

    /**
     * 上链发行获取hash
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface repairUpdate
     * @Time: 2022/7/11   00:42
     * common/test/addOnline
     */
    public function addOnline()
    {
        $redis = initRedis();
        $meberGoodsList = (new MemberGoods())->where(['cast_status'=>1,'asset_id'=>['<>','']])->select()->toArray();
        foreach ($meberGoodsList as $key=>$val){
            $redis->lpush('details_question_list',json_encode([
                'member_goods_id' => $val['id'],
                'asset_id' => $val['asset_id'],
                'operation_id' => $val['operation_id'],
            ]));
        }
    }

    /**
     * 修复没有铸造成功的重新写入铸造
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface repairUpdate
     * @Time: 2022/7/11   00:42
     * common/test/repairUpdate
     */
    public function repairUpdate()
    {
        $redis = initRedis();
        $meberGoodsList = (new MemberGoods())->where(['cast_status'=>1,'asset_id'=>['=','']])->select()->toArray();
        foreach ($meberGoodsList as $key=>$val){
            $redis->lpush('casting_question_list',json_encode([
                'member_goods_id' => $val['id'],
                'goods_id' => $val['goods_id'],
                'member_id' => $val['member_id']
            ]));
        }
    }


    /**
     * 计算统计拉新排行榜 总榜
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface calculateLeaderboard
     * @Time: 2022/7/15   10:30
     * common/test/calculateLeaderboard
     */
    public function calculateLeaderboard()
    {
        $model = new Member();
        $member_list = $model->where(['status'=>1])->field("member_id,p_id")->select();
        foreach ($member_list as $key=>$val){
            $count = $model->alias("a")->join("snake_order b","a.member_id = b.member_id")->group("a.member_id")->where(['a.p_id'=>$val['member_id'],'b.pay_status'=>2,'b.order_type'=>['in',['1','2','3']]])->count();
            echo "-----正在执行---".$val['member_id'].'-------计算拉新人数'.$count;
            echo "\r\n";
            $model->where(['member_id'=>$val['member_id']])->update(['invitations_number'=>$count]);
        }
        echo  "执行完毕";
    }




    /**
     * 清理会员数据
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface cleanUpMember
     * @Time: 2022/8/3   15:46
     * common/test/cleanUpMember
     */
    public function cleanUpMember()
    {
        //查询出来所有的会员列表 然后根据这个会员 是否实名认证 认证过 下级没有就直接删除
        $member_list = (new Member())->where(['real_status'=>2])->field("member_id,p_id")->select();
        $count_number = count($member_list);
        echo "---执行开始---累计总数--{$count_number}";
        $nbumber = 0;
        foreach ($member_list as $key=>$val){
            echo "\n";
            echo "---正在执行第--{$nbumber}--次";
            $member_rel_info = (new MemberReal())->where(['member_id'=>$val['member_id']])->find();
            if(empty($member_rel_info)){
                self::doLogs("---删除的会员ID---{$val['member_id']}");
                //删除这个下面的会员 找到

            }
            $nbumber++;
        }
        echo "---执行完毕---";

        //获取到现在实名认证的信息  做筛选 根据身份证晒筛选 进行删除
        //获取到现在实名认证的信息  做筛选 根据身份证晒筛选 进行删除
        $member_rel_list = (new MemberReal())->field("id,member_id,card,create_time")->order("id desc")->group("card")->select();
        $count_number = count($member_rel_list);
        echo "---执行开始---累计总数--{$count_number}";
        $nbumber = 0;
        foreach ($member_rel_list as $key=>$val){
            echo "\n";
            echo "---正在执行第--{$nbumber}--次";
            $count = (new MemberReal())->where(['card'=>$val['card']])->count();
            if($count>1){
                //删除剩下的实名信息 //获取需要删除的会员ID
                $ids = (new MemberReal())->where(['card'=>$val['card']])->column('id');
                $ids_string_y = implode(',',$ids);
                unset($ids[0]);
                sort($ids);
                (new MemberReal())->where(['id'=>['in',$ids]])->delete();
                $ids_string = implode(',',$ids);
                self::doLogs("---会员ID---{$val['member_id']}----身份证号码----{$val['card']}---出现次数--{$count}---认证时间--{$val['create_time']}---原有ID--{$ids_string_y}--删除会员id---{$ids_string}");
            }
            $nbumber++;
        }
        echo "---执行完毕---";
    }

    /**
     * 重新编排用户账号
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface againNumbering
     * @Time: 2022/8/18   11:58
     */
    public function againNumbering()
    {
        //执行清空这个藏品的编号
        (new MemberGoods())->where(['goods_id'=>'42'])->update(['collection_number'=>'']);
        $member_goods_list = (new MemberGoods())->where(['goods_id'=>'42','goods_status'=>['in',['0','1','2']],'is_donation'=>0])->select();
        $count_number = count($member_goods_list);
        foreach ($member_goods_list as $key=>$val){
            //查询藏品是否参与转增  是否有转售行为
            $goods_no = createNumbering($val['goods_id'],$val);
        }
    }

    /**
     * 同步盲盒数据
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface synchronizedBlindBoxData
     * @Time: 2022/9/2   19:30
     * common/test/synchronizedBlindBoxData
     */
    public function synchronizedBlindBoxData()
    {
        $order_list = (new Order())->alias("a")->join("order_goods b","a.order_id = b.order_id")->where(['a.order_type'=>['in',['2','7','9','10']],'pay_status'=>2])->select();
        foreach ($order_list as $key=>$value){
            $memberBoxInfo = (new MemberBox())->where(['order_id'=>$value['order_id']])->find();
            if(empty($memberBoxInfo)){
                (new MemberBox())->insertData([
                    'member_id' => $value['member_id'],
                    'order_id' => $value['order_id'],
                    'order_sn' => $value['order_no'],
                    'is_open' => $value['is_open'],
                    'box_status' => 10,
                    'goods_id' => $value['goods_id'],
                    'goods_name' => $value['goods_name'],
                    'goods_thumb' => $value['goods_image'],
                ]);
            }

        }
    }

    /**
     * 批量脚本创建账户
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface AccountCreation
     * @Time: 2022/9/7   17:16
     * common/test/AccountCreation
     */
    public function AccountCreation()
    {
        $member_list = (new Member())->where(['real_status'=>2])->field("member_id")->select();
        $count_number = count($member_list);
        echo "---执行开始---累计总数--{$count_number}";
        $nbumber = 0;
        foreach ($member_list as $key=>$value){
            $memberChina = (new MemberChain())->field("member_id,t_address")->where(['member_id'=>$value['member_id']])->find();
            echo "\n";
            echo "---正在执行第--{$nbumber}--次";
            if(empty($memberChina['t_address'])){
                (new Task())->createAccount($value['member_id']);
            }
            $nbumber++;
        }
        echo "---执行完毕---";
    }

    /**
     * 压测创建订单
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createOrder
     * @Time: 2022/9/16   14:42
     * common/test/createOrder
     */
    public function createOrder()
    {
        $member_list = (new Member())->where(['real_status'=>2])->field("member_id")->select();
        foreach ($member_list as $key=>$value){
            (new Sdpay())->getmemberStatus($value['member_id']);
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
    protected static function doLogs($values)
    {
        return write_log($values,RUNTIME_PATH.'/manual');
    }



    /**
     * 汇元分佣
     * @throws \RedisException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/9/20   17:10
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface fr
     */
    public function fr()
    {
        $order_sn = "";
        $hy_order_sn = "";
        $pay_method_array = "";
        $order_sns = explode(PHP_EOL, $order_sn);
        $hy_order_sns = explode(PHP_EOL, $hy_order_sn);
        $pay_method_array_new = explode(PHP_EOL, $pay_method_array);
        $hy_pay = WxSetting::detail();
        foreach ($order_sns as $key=>$val){
            $order_info = (new OrderModel())->where(['order_no'=>$val])->find();
            $data['order_type'] = $order_info['order_type'];
            $data['user_uid'] = 0;
            $hy_order_sn = $this->orderSplitAccountsNo();
            if($pay_method_array_new[$key] == '账户余额'){
                $handling_fee = $hy_pay['hy_wallet_handling'];
            }else{
                $handling_fee = $hy_pay['hy_fast_handling'];
            }
            $handling_fee = bcdiv($handling_fee,100,2);
            //减去扣除的手续费 得到汇元扣除的手续费
            $h_amount = bcmul($order_info['pay_price'],$handling_fee,4); //汇元的手续费
            if($h_amount<0.1){
                $h_amount = 0.1;
            }
            $amountTransaction = bcsub($order_info['total_price'],$h_amount,4); //剩余的钱 再去计算平台的收益
            //如果是 二级市场的订单 需要计算会员的分润 如果不是则不用计算
            $deduct_values = 0;
            if ($order_info['order_type'] == 3) {
                $member_info = (new Member())->where(['member_id'=>$order_info['sale_member_id']])->find();
                $data['user_uid'] =$member_info['user_uid'];
                //获取需要扣除的金额
                $serviceValues = Setting::getItem("service");
                //获取总的服务费比例：
                $sum_serviceValues =  bcadd($serviceValues['service_fee'],$serviceValues['creator_fee'],2);
                $sum_serviceValues_percentage = bcdiv($sum_serviceValues,100,2);
                //计算需要扣除的金额
                $deduct_values = bcmul($amountTransaction,$sum_serviceValues_percentage,4);
            }
            //分佣的金额
            $sellerAmount = bcsub($amountTransaction,$deduct_values,4);
            (new \app\common\extend\hy\Pay())->ApplyPayShare([
                'hy_bill_no' => $hy_order_sns[$key],
                'out_trade_no' => $val,
                'out_share_no' => $hy_order_sn,
                'deduct_values' => $deduct_values, //平台的佣金手续费
                'user_uid' => $data['user_uid'],
                'user_amount' => $sellerAmount, //卖家的佣金,
                'amountTransaction' => $amountTransaction, //除去汇元的剩余
                'order_type' => $order_info['order_type']
            ]);
        }
        echo "---执行完毕---";
    }

    /**
     * 解锁订单操作
     */
    public function unlockOrder()
    {
        $order = \app\admin\model\Setting::getItem('order');
        $deadlineTime = time() - ((int)$order['pay_time'] * 60);
        $filter = [
            'pay_status' => 1,
            'order_status' => 1,
            'create_time' => ['<', $deadlineTime],
            'pay_type' => 15,
            'order_type'=>3
        ];
        $order_list = (new OrderModel())->where($filter)->select();
        foreach ($order_list as $key=>$value){
            $goods_info = (new MemberGoods())->where(['id'=>$value['sale_goods_id']])->find();
            if($goods_info['goods_status']==2){
                (new MemberGoods())->where(['id'=>$value['sale_goods_id']])->update(['goods_status'=>1]);
                (new OrderModel())->where(['order_id'=>$value['order_id']])->update(['order_status'=>5]);
            }
        }
    }

    /**
     * 重新编排用户账号
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface againNumbering
     * @Time: 2022/8/18   11:58
     * common/test/againClickNumbering
     */
    public function againClickNumbering()
    {
        //执行清空这个藏品的编号
        $goods_id = "2";
        $member_goods_list = (new MemberGoods())->where(['goods_id'=>$goods_id,'collection_number'=>NULL,'cast_status'=>2])->select()->toArray();
        foreach ($member_goods_list as $key=>$val){
            //查询藏品是否参与转增  是否有转售行为
            $member_goods_info = (new MemberGoods())->where(['goods_id'=>$goods_id,'cast_status'=>2])->order("collection_number desc")->find();
            $collection_number = explode("#",$member_goods_info['collection_number']);
            $collection_number = explode("/",$collection_number[1]);
            $goods_no_number = bcadd($collection_number[0], 1);
            $goods_no = $val['goods_no'] . "#" . $goods_no_number . '/' . "510";
            (new MemberGoods())->where(['id' => $val['id']])->update(['collection_number' => $goods_no]);

        }
    }



    /**
     * 生成订单号
     */
    protected function orderSplitAccountsNo()
    {
        return 'FZ'.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }


    public function publicKey()
    {
        $member_list = (new MemberChain())->where(['member_id'=>['<=',8129]])->select();
        foreach ($member_list as $key=>$value){
            $t_userKey = md5(($value['member_id'] . 'Ti@CreateAddress'));
            (new MemberChain())->where(['member_id'=>$value['member_id']])->update(['t_userKey'=>$t_userKey]);
        }
        echo "已结束";
    }


    /**
     * 执行计算消费的排行榜的金额
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getCalculateConsumption
     * @Time: 2023/1/5   10:43
     */
    public function getCalculateConsumption()
    {
        $member_list = (new Member())->where(['real_status'=>2])->field("member_id")->select();
        foreach ($member_list as $key=>$value){
            $sum_price = (new Order())->where(['member_id'=>$value['member_id'],'pay_status'=>2,'order_type'=>['in',['3','11']],'pay_time'=>['>','1672502400']])->sum("pay_price");
            (new Member())->where(['member_id'=>$value['member_id']])->update(['amount_spent'=>$sum_price]);
        }
        echo "已结束";
    }
}