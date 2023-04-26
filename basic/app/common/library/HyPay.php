<?php

namespace app\common\library;


use app\common\model\Order;
use app\common\model\PaySetting;
use app\common\model\Setting;
use exception\BaseException;
use hy\Pay;
use hy\Wallet;

/**
 * 汇元 支付
 */
class HyPay
{
    public $errorMsg;

    /**
     * 汇元快捷支付钱包支付
     * @ApiAuthor [Mr.Zhang]
     * @param $pay_method 2=钱包支付  63=快捷支付 [需要再次调用确认接口完成支付]
     */
    public function QuickPayment($order_sn, $total_fee, $goods_name, $card_token, $pay_method = 63,$member_id)
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
        $pay = new Pay(PaySetting::getItem('hyPay'));
        $expire_time = bcdiv(bcsub($expire_time,time(),2),60);
        $result = $pay->payments($member_id,$total_fee,$order_sn,$goods_name,$order_info['order_type'],$card_token,$pay_method,$expire_time);
        if($result){
            return $result;
        }
        $this->errorMsg = $pay->getError()?:"支付失败";
        return false;
    }


    /**
     * 获取开户信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function getUserInfo($member_id)
    {
        $wallet = new Wallet(PaySetting::getItem('hyPay'));
        $result = $wallet->getUserInfo($member_id);
        if($result){
            return $result;
        }
        $this->errorMsg = $wallet->getError()?:"获取信息失败！";
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