<?php

namespace app\api\controller\member;

use app\common\components\helpers\RedisUtils;
use app\common\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\common\model\Appointment;
use app\common\model\Purchase;


/**
 * 订单管理
 */
class Order extends Controller
{

    /**
     * 订单列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.order/orderList)
     * @ApiParams   (name="type", type="string", required=true, description="类型 1=全部 2=待付款 3=已完成 4=已取消")
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="order_status", type="string", description="1 待付款 2 已支付 3进行中 4已完成 5已取消")
     * @ApiReturnParams   (name="pay_status", type="string", description="'付款状态(1未付款 2已付款)")
     */
    public function orderList()
    {
        $type = $this->request->post('type');
        $page = $this->request->post('page');
        if(!$type ||  !$page){
            $this->error('参数错误');
        }
        $model = new OrderModel;
        $list  = $model->getList($this->auth->member_id, $type, $page);
        $this->success(compact('list'));
    }

    /**
     * 订单详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.order/getOrderDetails)
     * @ApiParams   (name="order_id", type="string", required=true, description="订单ID")
     */
    public function getOrderDetails()
    {
        $order_id = $this->request->post('order_id');
        if(!$order_id){
            $this->error('参数错误');
        }
        $order = OrderModel::getUserOrderDetail($order_id, $this->auth->member_id);
        $this->success("获取成功",$order);
    }

    /**
     * 订单取消
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.order/cancel)
     * @ApiParams   (name="order_id", type="string", required=true, description="订单ID")
     */
    public function cancel()
    {
        $order_id = $this->request->post('order_id');
        $lockKey = "cancel_order_".$order_id;
        if (RedisUtils::lock($lockKey, 10)) {
            $this->error('请求频繁,请重试');
        }
        $model = OrderModel::getUserOrderDetail($order_id, $this->auth->member_id);
        try {
            $result = $model->cancel();
        } catch (\Exception $e) {
            RedisUtils::unlock($lockKey);
            $this->error($e->getMessage());
        }
        RedisUtils::unlock($lockKey);
        if ($result) {
            $this->success("订单取消成功");
        }
        $this->error($model->getError());
    }


    /**
     * 删除订单
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.order/removeOrder)
     * @ApiParams   (name="order_id", type="string", required=true, description="订单ID")
     */
    public function removeOrder()
    {
        $model = OrderModel::getUserOrderDetail($this->request->post('order_id'), $this->auth->member_id);
        if ($model->deleteOrder()) {
            $this->success("订单删除成功");
        }
        $this->error($model->getError());
    }


    /**
     * 抽签订单
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.order/getDrawList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiParams   (name="status_type", type="string", required=true, description="状态 1=全部 2=已中签 3=未中签 4=待开奖")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function getDrawList()
    {
        $page = $this->request->post('page')?:1;
        $status_type = $this->request->post('status_type')?:1;
        if(!$page || !$status_type){
            $this->error('参数错误');
        }
        $where = [];
        $where['member_id'] = $this->auth->member_id;
        switch ($status_type)
        {
			case "1":
				$where['status'] = ['not in',['4','3']];
				break;
            case "2":
                $where['status'] = '1';
                break;
            case "3":
                $where['status'] = 2;
                break;
            case "4":
                $where['status'] = 0;
                break;
        }
        $list = (new Appointment())->where($where)->field("id,goods_id,create_time,status,member_id")->page($page)->select()->toArray();
        foreach ($list as $key=>$val){
            $goods_info = \app\common\model\Goods::detail($val['goods_id']);
            $list[$key]['goods_name'] = $goods_info['goods_name'];
            $list[$key]['goods_thumb'] = $goods_info['goods_thumb'];
            $list[$key]['goods_price'] = $goods_info['goods_price'];
            $list[$key]['issue_name'] = $goods_info['issue_name'];
            if($val['status']==1){
                $order_info = (new OrderModel())->alias('o')
                    ->join('order_goods og', 'o.order_id = og.order_id', 'left')
                    ->where(['og.goods_id' => $val['goods_id'], 'o.member_id' => $val['member_id'],'o.order_type' => 12])
                    ->find();
                if($order_info){
                    if($order_info['pay_status']['value'] == 2){
                        (new Appointment())->where(['id'=>$val['id']])->update(['status'=>3]);
                        $list[$key]['status'] = 3;
                    }
                    if($order_info['order_status']['value'] == 5){
                        (new Appointment())->where(['id'=>$val['id']])->update(['status'=>4]);
                        $list[$key]['status'] = 4;
                    }

                }
            }
        }
        $this->success("获取成功",$list);
    }


    /**
     * 提交预约
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.order/toAppointment)
     * @ApiParams   (name="goods_id", type="string", required=true, description="藏品ID")
     */
    public function toAppointment()
    {
        // 取出Redis存储藏品
        $goods_id = $this->request->param('goods_id');
        if(!$goods_id){
            $this->error('参数错误');
        }
        $detail = $this->redis->hGetAll("collection:goods_id:$goods_id");
        if(!$detail){
            $this->error('藏品不存在');
        }
        // 判断此藏品类型是否支持预约
        if($detail['product_types'] != '4'){
            $this->error('此藏品不支持预约');
        }
        // 获取预约时间
        $purchase_info = (new Purchase())->where(['goods_id' => $detail['goods_id']])->find()->toArray();
        $new_time = time();
        if($new_time < $purchase_info['appointment_start_time']){
            $this->error('预约未开始');
        }
        if($new_time > $purchase_info['appointment_end_time']){
            $this->error('预约已结束');
        }
        // 判断是否预约过
        $model = new Appointment();
        $isHave = $model->where(['member_id' => $this->auth->member_id,'goods_id' => $goods_id])->find();
        if($isHave){
            $this->error('已预约');
        }
        if($model->toAppointment($detail,$this->auth->member_id)){
            $this->success('预约成功');
        }
        $this->error('预约失败');
    }

}