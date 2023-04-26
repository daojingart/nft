<?php

namespace app\common\library;

use adapay\Pay;
use app\admin\model\Setting;
use app\common\model\Order;
use app\common\model\PaySetting;
use exception\BaseException;

class HfPay
{
    /**
     * 汇付快捷支付
     * @ApiAuthor [Mr.Zhang]
     */
    public function QuickPayment($order_sn, $total_fee, $goods_name, $token_no)
    {
        $order_info = (new Order())->where(['order_no' => $order_sn])->find();
        if (empty($order_info)) {
            throw new BaseException(['msg' => "订单信息错误", 'code' => -1]);
        }
        if ($order_info['pay_status']['value'] != 1) {
            throw new BaseException(['msg' => "订单信息错误", 'code' => -1]);
        }
        $order = Setting::getItem('order');
        $expire_time = date('YmdHis', strtotime($order_info['create_time']) + $order['pay_time'] * 60);
        return (new Pay(PaySetting::getItem('hfPay')))->payments($order_sn, $total_fee, $goods_name, $token_no,$expire_time);
    }

}