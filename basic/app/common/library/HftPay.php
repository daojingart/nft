<?php

namespace app\common\library;

use app\common\model\Order;
use app\common\model\PaySetting;
use app\common\model\Setting;
use hftpay\Payment;

/**
 * 汇付通支付类
 */
class HftPay
{
    public $errorMsg;

    /**
     * 汇付通支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function PayPayment($member_id,$order_sn,$total_fee,$goods_name,$order_type,$user_cust_id)
    {
        $order_info = (new Order())->where(['order_no' => $order_sn])->find();
        if (empty($order_info)) {
            $this->errorMsg = "订单信息错误";
            return false;
        }
        if ($order_info['pay_status']['value'] != 1) {
            $this->errorMsg = "订单信息错误";
            return false;
        }
        $order = Setting::getItem('order');
        $expire_time = strtotime($order_info['create_time']) + $order['pay_time'] * 60;
        if(time()>$expire_time){
            $this->errorMsg = "订单过期，无法发起支付";
            return false;
        }
        $payment = new Payment(PaySetting::getItem('hftPay'));
        $result = $payment->payments($member_id,$order_sn,$total_fee,$goods_name,$order_type,$expire_time,$user_cust_id);
        if($result){
            return $result;
        }
        $this->errorMsg = $payment->getError()?:"获取信息失败！";
        return false;
    }

    /**
     * 获取错误提示
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function getError(){
        return $this->errorMsg;
    }

}