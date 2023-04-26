<?php

namespace sd;

use sd\tools\Sign;
use think\Config;

/**
 * 杉德快捷类目
 */
class Quick extends SdPay
{
    /**
     * 银行卡快捷支付
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface paymentBalance
     * @Time: 2022/8/5   17:00
     */
    public function paymentQuick($order_sn,$expire_time,$total_fee,$goods_name,$member_id,$name,$card)
    {
        //获取订单过期时间
        $data = [
            'version' => 10,
            'mer_no' =>  $this->config['merchant_id'],
            'mer_order_no' => $order_sn, //商户唯一订单号
            'create_time' => $this->getTimestamp(),
            'expire_time' => $expire_time,
            'order_amt' => $total_fee, //订单支付金额
            'notify_url' => Config::get('payConfig')['sdPay']['quick_notify_url'],  // 异步通知地址
            'return_url' => Config::get('payConfig')['sdPay']['return_url'], //订单前端页面跳转地址
            'create_ip' => str_replace(".","_",\request()->ip()),
            'goods_name' => $goods_name,
            'store_id' => '000000',
            'product_code' => '06030003',
            'clear_cycle' => '3',
            //pay_extra参考语雀文档4.3
            'accsplit_flag' => 'NO',
            'jump_scheme' => 'sandcash://scpay',
            'meta_option' => json_encode([["s" => "Android","n" => "wxDemo","id" => "com.pay.paytypetest","sc" => "com.pay.paytypetest"]]),
            'sign_type' => 'RSA',
            'pay_extra' => json_encode([
                'userId' => $this->prefix.$member_id,
                'userName' => $name,
                'idCard' => $card,
            ]),
        ];
        $temp = $data;
        unset($temp['goods_name']);
        unset($temp['jump_scheme']);
        unset($temp['expire_time']);
        unset($temp['product_code']);
        unset($temp['clear_cycle']);
        unset($temp['meta_option']);
        $str = Sign::getSignContent($temp);
        $sign = Sign::createSign($str,$this->config['privatePfx_path'],$this->config['privatePfxPwd']);
        $data['sign'] = $sign;
        $query = http_build_query($data);
        $request_url = $this->quicktopupUrl.$query;
        return [
            'jump_url' => $request_url
        ];
    }

}