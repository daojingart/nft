<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/7/4   14:01
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\notice\model;

use app\admin\model\Setting as SettingModel;
use app\common\model\Member;
use app\common\model\OrderGoods;
use app\common\model\Goods as GoodsModel;
use app\common\model\Order as OrderModel;
use app\common\model\ProductOrder;
use app\notice\service\BoxGoods;
use app\notice\service\HyOrder;
use app\notice\service\SystemPay;
use app\notice\service\YeepayOrder;
use app\queue\controller\OrderJob;
use think\Queue;
use think\Db;
use Exception;
use think\db\Query;
use app\common\model\Glory;

class Order extends OrderModel
{
      /**
     * 回调统一处理支付的问题
     * @throws \exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/7/4   14:02
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface callBack
     */
    public function callBack($arr)
    {
        $sub_str = substr($arr['out_trade_no'],0,5);
        if($sub_str=='GOODS'){
            $product_info = (new ProductOrder())->where(['order_no'=>$arr['out_trade_no']])->find();
            if (empty($product_info)) {
                return false;
            }
            if ($product_info['order_status']!=0) {
                return false;
            }
            (new ProductOrder())->where(['order_id' => $product_info['order_id']])->update([
                'order_status' => 2,
                'pay_time' => time(),
            ]);
            return true;
        }else{
            Queue::push(OrderJob::class, $arr,'order');
            return true;
        }
        return false;
    }

    /**
     * 处理具体的任务逻辑
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2023/2/13   10:09
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface processTasks
     */
    public function processTasksJob($arr)
    {
        //处理业务逻辑 修改订单状态 增加 我的藏品
        $Order_info = (new OrderModel())->where(['order_no'=>$arr['out_trade_no']])->find();
        if (empty($Order_info)) {
            write_log($arr['out_trade_no'].'--订单不存在',RUNTIME_PATH."/callBack");
            return false;
        }
        if ($Order_info['pay_status']['value'] != 1) {
            write_log($arr['out_trade_no'].'--订单无效,无法支付',RUNTIME_PATH."/callBack");
            return false;
        }
        if ($Order_info['pay_status']['value'] == 2) {
            write_log($arr['out_trade_no'].'--订单已存在',RUNTIME_PATH."/callBack");
            return false;
        }
        if ($Order_info['order_status']['value'] != 1) {
            write_log("订单已取消无法进行修改支付状态--订单号--{$arr['out_trade_no']}",RUNTIME_PATH."/callBack");
            return false;
        }
        Db::startTrans();
        try{
            if (empty($Order_info)) {
                write_log('订单信息不存在',RUNTIME_PATH."/callBack");
                return false;
            }
            $member_info = Member::detail(['member_id'=>$Order_info['member_id']]);
            //更新订单信息
            $res = (new OrderModel())->where(['order_id' => $Order_info['order_id']])->update([
                'pay_status' => 2,
                'pay_time' => time(),
                'order_status' => 2,
                'third_no' => $arr['transaction_id']
            ]);
            if (!$res) {
                write_log('订单更新失败'.$arr['out_trade_no'],RUNTIME_PATH."/callBack");
                return false;
            }
            $orderGoodsInfo = (new OrderGoods())->where(['order_id'=>$Order_info['order_id']])->find();
            if (empty($orderGoodsInfo)) {
                write_log('订单商品信息不存在'.$arr['out_trade_no'],RUNTIME_PATH."/callBack");
                return false;
            }
            $goodsInfo = (new GoodsModel())::detail(['goods_id' => $orderGoodsInfo['goods_id']]);
            if (empty($goodsInfo)) {
                write_log('商品信息不存在'.$arr['out_trade_no'],RUNTIME_PATH."/callBack");
                return false;
            }
            //拉新排行榜
            $this->inviteLeaderboards($Order_info,$member_info);
            //消费排行榜
            $this->consumptionLeaderboard($Order_info);
            //更新限购次数
            $this->increaseNumberAdd($member_info);
            if($goodsInfo['product_types'] !=3){ //购买的藏品
                $member_goods_insertId = (new \app\notice\service\Goods)->joinAddGoods($Order_info,$orderGoodsInfo,$goodsInfo,$member_info);
            }else if($goodsInfo['product_types'] ==3){ //购买的盲盒
                (new BoxGoods())->joinBoxGood($Order_info,$orderGoodsInfo);
            }
            //藏品增加实际销量
            if(in_array($goodsInfo['product_types'], ['1','3','4'])){
                (new GoodsModel())->where(['goods_id'=>$orderGoodsInfo['goods_id']])->setInc("sales_actual",1);
            }
            //处理上链
            if(in_array($Order_info['order_type'], ['1','5','6','12'])){
                //加入铸造藏品的队列
                $this->castingQuestionList($member_goods_insertId,$goodsInfo['goods_id'],$Order_info['member_id'],"casting_question_list");
            }
            //处理藏品的二级交易市场
            if($Order_info['order_type'] ==3 && $goodsInfo['product_types'] !=3){
                //易宝支付 分润处理
                (new YeepayOrder())->profitsharingOrder($Order_info,$arr);
                //汇元支付  分润处理
                (new HyOrder())->profitsharingOrder($Order_info,$arr);
                $memberGoodsModelInfo = (new \app\notice\service\Goods())->insertFlowRecord($Order_info,$member_goods_insertId,$orderGoodsInfo);
                if(!in_array($Order_info['pay_type']['value'], ['9','12','13','14','15','16'])){
                    (new SystemPay())->profitsharingOrder($Order_info);
                }
                $this->deleteLocalRedis($Order_info['member_id']);
                //转增的生产队列
                $this->increaseQuestionList($member_goods_insertId,$memberGoodsModelInfo['asset_id'], $memberGoodsModelInfo['shard_id'], $memberGoodsModelInfo['member_id'], $Order_info['member_id'],"increase_question_list");
            }
            //处理盲盒寄售订单
            if($Order_info['order_type'] == 11 && $goodsInfo['product_types'] ==3){
                (new BoxGoods())->updateBoxStatus($Order_info['sale_goods_id']);
                if(!in_array($Order_info['pay_type']['value'], ['9','12','13','14','15','16'])){
                    (new SystemPay())->profitsharingOrder($Order_info);
                }
               $this->deleteLocalRedis($Order_info['member_id']);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            $this->error = $e->getMessage();
            write_log($e,RUNTIME_PATH."/callBack");
            Db::rollback();
            return false;
        }
    }

    /**
     * 删除存储的唯一键
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface deleteLocalRedis
     * @Time: 2023/2/13   16:30
     */
    public function deleteLocalRedis($member_id)
    {
        $locakReidsOrder = 'local_redis_member_id_'.$member_id;
        $redis = initRedis();
        $redis->del($locakReidsOrder);
    }


    /**
     * 更新邀请排行榜
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface inviteLeaderboards
     * @Time: 2023/2/13   14:40
     */
    public function inviteLeaderboards($Order_info,$member_info)
    {
        $redis = initRedis();
        //查询本周 本月 累计 邀请的有效人数进行更新计算 查询这个会员的订单是否大于1  大于1则判断已经加过了不在统计邀请人数
        $count = $this->where(['member_id'=>$Order_info['member_id'],'pay_status'=>2,'order_type'=>['in',['1','2','3']]])->count();
        $new_member_info = (new Member())->where(['member_id'=>$Order_info['member_id']])->field("create_time,member_id")->find();
        $redis_key = "resetLeaderboardData";
        $now_time = '0';
        if($redis->exists($redis_key)){
            $now_time = $redis->get($redis_key);
        }
        $values_setting = SettingModel::getItem("todaytask");
        $condition_task = "1";
        if(isset($values_setting['condition_task'])){
            $condition_task = $values_setting['condition_task'];
        }
        if($count==1 && $member_info['p_id']>0 && strtotime($new_member_info['create_time'])>$now_time && $condition_task==1){
            (new Member())->where(['member_id'=>$member_info['p_id']])->setInc("invitations_number",1);
        }
    }

    /**
     * 消费排行榜
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface consumptionLeaderboard
     * @Time: 2023/2/13   14:45
     */
    public function consumptionLeaderboard($Order_info)
    {
        if($Order_info['order_type']==3 || $Order_info['order_type'] == 11){
            (new Member())->where(['member_id'=>$Order_info['member_id']])->setInc("amount_spent",$Order_info['pay_price']);
        }
    }

    /**
     * 更新额外的购买次数
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface increaseNumberAdd
     * @Time: 2023/2/13   14:47
     */
    public function increaseNumberAdd($member_info)
    {
        $values = SettingModel::getItem("collection");
        if($member_info['p_id']>0 && isset($values['additional_open']) && $values['additional_open']==10){
            //邀请人数+1
            (new Member())->where(['member_id'=>$member_info['p_id']])->setInc('purchase_sum',1);
            $purchase_sum = (new Member())->where(['member_id'=>$member_info['p_id']])->value('purchase_sum');
            if($purchase_sum == 3){
                //增加200荣誉值
                $integralData = [
                    'member_id' => $member_info['p_id'],
                    'type' => 1,
                    'amount' => 200,
                    'remark' => '完成洗髓丹*2任务奖励'
                ];
                (new Glory())->allowField(true)->save($integralData);
            }elseif ($purchase_sum == 5){
                //增加300荣誉值
                $integralData = [
                    'member_id' => $member_info['p_id'],
                    'type' => 1,
                    'amount' => 300,
                    'remark' => '完成洗髓丹*3任务奖励'
                ];
                (new Glory())->allowField(true)->save($integralData);
                //(new Member())->where(['member_id'=>$member_info['p_id']])->setInc('glory',300);
            }elseif ($purchase_sum == 10){
                //增加3000荣誉值
                $integralData = [
                    'member_id' => $member_info['p_id'],
                    'type' => 1,
                    'amount' => 3000,
                    'remark' => '完成申公豹*1＋灵电黑豹*1任务奖励'
                ];
                (new Glory())->allowField(true)->save($integralData);
                //(new Member())->where(['member_id'=>$member_info['p_id']])->setInc('glory',3000);
            }elseif ($purchase_sum == 30){
                //增加3000荣誉值
                $integralData = [
                    'member_id' => $member_info['p_id'],
                    'type' => 1,
                    'amount' => 3000,
                    'remark' => '完成妲己*1＋叱风犬*1奖励'
                ];
                (new Glory())->allowField(true)->save($integralData);
                //(new Member())->where(['member_id'=>$member_info['p_id']])->setInc('glory',3000);
            }elseif ($purchase_sum == 50){
                //增加4000荣誉值
                $integralData = [
                    'member_id' => $member_info['p_id'],
                    'type' => 1,
                    'amount' => 4000,
                    'remark' => '完成灵狐*1＋金眼神鹰*1奖励'
                ];
                (new Glory())->allowField(true)->save($integralData);
                //(new Member())->where(['member_id'=>$member_info['p_id']])->setInc('glory',4000);
            }elseif ($purchase_sum == 100){
                //增加20000
                $integralData = [
                    'member_id' => $member_info['p_id'],
                    'type' => 1,
                    'amount' => 20000,
                    'remark' => '完成纣王*1 + 四只神兽一套*1奖励'
                ];
                (new Glory())->allowField(true)->save($integralData);
                //(new Member())->where(['member_id'=>$member_info['p_id']])->setInc('glory',20000);
            }
        }
    }



    /**
     * 铸造上链队列
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface pushExecute
     * @Time: 2022/7/7   13:54
     */
    public function castingQuestionList($member_goods_insertId,$goods_id,$member_id,$queue_name)
    {
        $redis = initRedis();
        $redis->lpush($queue_name,json_encode([
            'member_goods_id' => $member_goods_insertId,
            'goods_id' => $goods_id,
            'member_id' => $member_id
        ]));
        return true;
    }

    /**
     * 转增队列
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface pushExecute
     * @Time: 2022/7/7   13:54
     * $res = (new Task())->addCollection($model['asset_id'], $model['shard_id'], $model['member_id'], $Order_info['member_id']);
     */
    public function increaseQuestionList($member_goods_insertId,$asset_id,$goods_id,$member_id,$to_member_id,$queue_name)
    {
        $redis = initRedis();
        $redis->lpush($queue_name,json_encode([
            'asset_id' => $asset_id,
            'shard_id' => $goods_id,
            'member_id' => $member_id,
            'to_member_id' => $to_member_id,
            'member_goods_id' =>$member_goods_insertId
        ]));
        return true;
    }

    /**
     * 销毁队列
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface pushExecute
     * @Time: 2022/7/7   13:54
     * $res = (new Task())->addCollection($model['asset_id'], $model['shard_id'], $model['member_id'], $Order_info['member_id']);
     */
    public function destroyQuestionList($asset_id,$shard_id,$member_id)
    {
        $redis = initRedis();
        $redis->lpush("destroyList",json_encode([
            'asset_id' => $asset_id,
            'shard_id' => $shard_id,
            'member_id' => $member_id,
        ]));
        return true;
    }
    
      /**
     * @Notes: 记录日志
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doLogs($values)
    {
        return write_log($values,__DIR__);
    }
    
}