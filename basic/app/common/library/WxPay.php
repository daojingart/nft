<?php

namespace app\common\library;

use app\common\extend\wx\AppPay;
use app\common\extend\wx\WeAppPay;
use app\common\extend\wx\WxH5Pay;
use app\common\model\Order;
use app\common\model\PaySetting;
use app\common\extend\wx\WxPay as  wxPayChat;

/**
 * 微信支付类
 */
class WxPay
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
        $values = PaySetting::getItem("wxPay");
        if($values['open_status'] == 20){
            $this->errorMessage = "支付通道关闭;无法支付";
            return false;
        }
        $this->config = $values;
    }

    /**
     * 公众号支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function weChatPay($order_sn,$title,$total,$openid)
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
        if(!$openid){
            $this->errorMessage = "openid不能为空";
            return false;
        }
        $wxPayment = new wxPayChat();
        return $wxPayment->unifiedorder($order_sn, $openid, $total,$title,$this->config);
    }


    /**
     * 微信APP支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function weAppPay($order_sn,$title,$total,$openid)
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
        if(!$openid){
            $this->errorMessage = "openid不能为空";
            return false;
        }
        $wxPayment = new AppPay();
        return $wxPayment->unifiedorder($order_sn, $openid, $total,$title,$this->config);
    }



    /**
     * 微信APP支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function weH5Pay($order_sn,$title,$total)
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
        $wxPayment = new WxH5Pay();
        return $wxPayment->unifiedorder($order_sn, $total,$title,$this->config);
    }





    /**
     * 获取错误信息
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}