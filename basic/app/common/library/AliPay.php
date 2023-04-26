<?php

namespace app\common\library;

use app\common\extend\alipay\AliAppPay;
use app\common\model\Order;
use app\common\model\PaySetting;
use app\common\extend\alipay\AliPay as AliPayment;

class AliPay
{
    protected $config = [];

    protected $pay;

    protected $errorMessage = '';

    /**
     * 构造函数
     * @throws ContainerException
     */
    public function __construct()
    {
        $values = PaySetting::getItem("aliPay");
        if($values['open_status'] == 20){
            $this->errorMessage = "支付通道关闭;无法支付";
            return false;
        }
        $this->config = $values;
    }

    /**
     * 支付宝支付 H5
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function aliPayH5($order_sn, $pay_price,$goods_name)
    {
        $order_info = (new Order())->where(['order_no' => $order_sn])->find();
        if (empty($order_info)) {
            $this->errorMessage = "订单信息错误";
            return false;
        }
        if ($order_info['pay_status']['value'] != 1) {
            $this->errorMessage = "订单信息错误";
            return false;
        }
        $aliPayment = new AliPayment();
        return $aliPayment->unifiedOrder($order_sn, $pay_price,$goods_name,$this->config);
    }


    /**
     * 支付宝支付 APP
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function aliPayApp($order_sn, $pay_price,$goods_name)
    {
        $order_info = (new Order())->where(['order_no' => $order_sn])->find();
        if (empty($order_info)) {
            $this->errorMessage = "订单信息错误";
            return false;
        }
        if ($order_info['pay_status']['value'] != 1) {
            $this->errorMessage = "订单信息错误";
            return false;
        }
        $aliPayment = new AliAppPay();
        return $aliPayment->unifiedOrder($order_sn, $pay_price,$goods_name,$this->config);
    }



    /**
     * 获取错误信息
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

}