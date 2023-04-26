<?php

namespace app\api\controller\member;

use app\api\validate\OrderValidate;
use app\common\controller\Controller;
use app\api\model\order\Product as ProductOrderModel;
use app\common\controller\Pay;
use app\common\library\BalancePay;
use app\common\model\ProductOrderGoods;


/**
 * 实物商城订单
 */
class Shoporder extends Controller
{
    /**
     * 商城订单列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Shoporder/getDataList)
     * @ApiParams   (name="type", type="string", required=true, description="1全部 2待付款 3代发货 4待收货 5已完成")
     * @ApiReturnParams   (name="order_id", type="string", description="订单ID")
     * @ApiReturnParams   (name="order_no", type="string", description="订单编号")
     * @ApiReturnParams   (name="total_price", type="string", description="订单支付价格")
     * @ApiReturnParams   (name="order_type", type="string", description="1=积分兑换订单 2=实物商城订单")
     * @ApiReturnParams   (name="goods_name", type="string", description="商品名称")
     * @ApiReturnParams   (name="goods_image", type="string", description="商品封面图")
     * @ApiReturnParams   (name="goods_attr", type="string", description="属性")
     * @ApiReturnParams   (name="status_text", type="string", description="状态文字")
     * @ApiReturnParams   (name="status_values", type="string", description="1=代发货 2=待收货 3=已完成 4=待付款")
     */
    public function getDataList()
    {
        (new OrderValidate())->goCheck('toObtainListOrder');
        $data = (new ProductOrderModel())->toObtainListOrder($this->auth->getUser());
        $this->success("获取信息成功",$data);
    }


    /**
     * 提醒发货
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Shoporder/remindDelivery)
     * @ApiParams   (name="order_id", type="string", required=true, description="订单ID")
     */
    public function remindDelivery()
    {
        (new OrderValidate())->goCheck('remindDelivery');
        (new ProductOrderModel())->remindDelivery($this->auth->getUser());
        $this->success("提醒成功");
    }

    /**
     * 确认收货
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Shoporder/confirmGoods)
     * @ApiParams   (name="order_id", type="string", required=true, description="订单ID")
     */
    public function confirmGoods()
    {
        (new OrderValidate())->goCheck('confirmGoods');
        (new ProductOrderModel())->confirmGoods($this->auth->getUser());
        $this->success("确认收货成功");
    }

    /**
     * 获取订单详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Shoporder/orderDetails)
     * @ApiParams   (name="order_id", type="string", required=true, description="订单ID")
     */
    public function orderDetails()
    {
        (new OrderValidate())->goCheck('confirmGoods');
        $data = (new ProductOrderModel())->orderDetails($this->auth->getUser());
        if ($data){
            $this->success("订单获取成功",$data);
        }
        $this->error("订单获取失败");
    }


    /**
     * 订单前去付款
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Shoporder/toPay)
     * @ApiParams   (name="order_id", type="string", required=true, description="订单ID")
     * @ApiParams   (name="pay_type", type="string", required=true, description="支付方式")
     * @ApiParams   (name="pwd", type="string", required=true, description="余额支付输入支付密码")
     */
    public function toPay()
    {
        $order_id = $this->request->post('order_id');
        $pay_type = $this->request->post('pay_type');
        $pwd = $this->request->post('pwd');
        if(!$order_id || !$pay_type){
            $this->error('参数错误');
        }
        //查询订单详情
        $balancePay = new BalancePay();
        $model = (new ProductOrderModel())->where(['order_id'=>$order_id])->find();
        $product_info = (new ProductOrderGoods())->where(['order_id'=>$model['order_id']])->find();
        switch ($pay_type){
            case "1": //公众号支付
                $this->success("公众号支付",[
                    'payment' => (new Pay())->wxPay($model['order_no'], $this->auth->open_id
                        , $model['pay_price'],$product_info['goods_name']),
                    'order_id' => $model['order_id']
                ]);
                break;
            case "3": //微信APP支付
                $this->success("APP支付",[
                    'payment' => (new Pay())->appPay($model['order_no'],$model['pay_price'],$product_info['goods_name']),
                    'order_id' => $model['order_id']
                ]);
                break;
            case "7": //微信H5支付
                $this->success("维信H5支付",[
                    'payment' => (new Pay())->wxH5Pay($model['order_no'],$model['pay_price'],$product_info['goods_name']),
                    'order_id' => $model['order_id']
                ]);
                break;
            case "4": //支付宝H5支付
                $this->success("支付宝H5",[
                    'payment' => (new Pay())->aliPay($model['order_no'],$model['pay_price'],$product_info['goods_name']),
                    'order_id' => $model['order_id']
                ]);
                break;
            case "8": //支付宝APP
                $this->success("支付宝APP",[
                    'payment' => (new Pay())->aliAppPay($model['order_no'],$model['pay_price'],$product_info['goods_name']),
                    'order_id' => $model['order_id']
                ]);
                break;
            case "5": //余额支付
                if(!$pwd){
                    $this->error("操作密码错误");
                }
                if (!$this->auth->checkOperationPwd($this->auth->member_id, $pwd)) {
                    $this->error('操作密码不正确,请重新输入');
                }
                $result = $balancePay->balancePayment($model,$this->auth->member_id,$this->auth->account);
                if($result){
                    $this->success("支付成功",$result);
                }
                $this->error($balancePay->getError());
                break;
            }
    }



}