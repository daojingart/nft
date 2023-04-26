<?php

namespace app\task\behavior;

use app\common\components\helpers\RedisUtils;
use app\common\components\helpers\StockUtils;
use app\common\model\Member;
use app\common\model\MemberBox;
use app\common\model\MemberGoods;
use app\common\model\OrderGoods;
use think\Db;
use app\admin\model\Setting;
use app\task\model\Order as OrderModel;

/**
 * 订单行为管理
 * Class Order
 * @package app\task\behavior
 */
class Order
{
    /* @var \app\task\model\Order $model */
    private $model;

    /**
     * 执行函数
     * @param $model
     * @return bool
     */
    public function run($model)
    {
        if (!$model instanceof OrderModel) {
            return new OrderModel and false;
        }
        $this->model = $model;
        $redis_lock = '__task_space__order__' . $model::$app_id;
        if (!RedisUtils::lock($redis_lock, 30)) {
            try {
                Db::startTrans();
                $order = Setting::getItem('order');
                // 未支付订单自动关闭
                $this->close($order['pay_time']);
                Db::commit();
            } catch (\Exception $e) {
                $this->dologs($e);
                Db::rollback();
                return false;
            }
        }
        return true;
    }

    /**
     * 未支付订单自动关闭
     * @param $close_days
     * @return $this|bool
     */
    private function close($close_days)
    {
        $redis = initRedis();
        // 取消n天以前的的未付款订单
        if ($close_days < 0) {
            return false;
        }
        // 截止时间
        $deadlineTime = time() - ((int)$close_days * 60);
        // 条件
        $filter = [
            'pay_status' => 1,
            'order_status' => 1,
            'create_time' => ['<', $deadlineTime],
            'pay_type' => ['<>','15']
        ];
        // 查询截止时间未支付的订单
        $orderIds = $this->model->where($filter)->column('order_id');
        //获取Redis 列表的值 进行屏蔽 删除处理
        $sd_order_list = $redis->Lrange("sdOrderNoList",0,-1);
        $sd_cloud_order_list = $redis->Lrange("sdCloudOrderNoList",0,-1);
        // 记录日志
        $this->dologs('close-进入', [
            'close_days' => (int)$close_days,
            'deadline_time' => $deadlineTime,
            'orderIds' => json_encode($orderIds),
        ]);
        // 直接更新
        $sd_order_ids = [];
        $sd_cloud_order_ids = [];
        if (!empty($orderIds)) {
            foreach ($orderIds as $key=>$val){
                //判断下取消订单的问题
                $order_info = $this->model->where(['order_id'=>$val])->field("order_id,order_type,sale_goods_id,is_limit,member_id,pay_type,order_no")->find();
                //订单锁
                memberOrderLock($order_info['member_id']);
                if(in_array($order_info['order_no'],$sd_order_list)){
                    $sd_order_ids[] = $val;
                    continue;
                }
                if(in_array($order_info['order_no'],$sd_cloud_order_list)){
                    $sd_cloud_order_ids[] = $val;
                    continue;
                }
                if($order_info['order_type']==3){
                    (new MemberGoods())->where(['id'=>$order_info['sale_goods_id']])->update(['goods_status'=>1]);
                }else if($order_info['order_type']==11){
                    (new MemberBox())->where(['id'=>$order_info['sale_goods_id']])->update(['box_status'=>20]);
                }else{
                    $lockKey = "order_id_".$val;
                    if (!RedisUtils::lock($lockKey, 30)) {
                        $this->dologs('回归库存----', [
                            'close_days' => (int)$close_days,
                            'deadline_time' => $deadlineTime,
                            'orderIds' =>$val,
                        ]);
                        $order_goods_info = (new OrderGoods())->where(['order_id'=>$val])->field("goods_id")->find();
                        if(StockUtils::addSoldStock($order_goods_info['goods_id'], -1)>=0){
                            StockUtils::increaseStock($order_goods_info['goods_id'], 1);
                        }
                        if($order_info['is_limit']==20){
                            (new Member())->where(['member_id'=>$order_info['member_id']])->setInc('purchase_limit',1);
                        }
                    }
                }
            }
            //过滤订单
            $orderIds = array_diff($orderIds, $sd_order_ids,$sd_cloud_order_ids);
            //更改订单的状态 取消订单
            return $this->model->isUpdate(true)->save(['order_status' => 5], ['order_id' => ['in', $orderIds]]);
        }
        return false;
    }



    /**
     * 记录日志
     * @param $method
     * @param array $params
     * @return bool|int
     */
    private function dologs($method, $params = [])
    {
        $value = 'Order --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return write_log($value, __DIR__);
    }

}
