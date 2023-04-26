<?php

namespace sd;

use sd\tools\Sign;
use think\Config;

/**
 * 云账户支付类
 */
class Cloud extends SdPay
{
    /**
     * 云账户支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function paymentCloud($order_sn,$expire_time,$total_fee,$goods_name,$member_id,$name,$sale_member_id,$order_type,$deduct_values)
    {
        $body = [
            'version' => 10,
            'mer_no' =>  $this->config['merchant_id'],
            'mer_order_no' => $order_sn, //商户唯一订单号
            'create_time' => $this->getTimestamp(),
            'expire_time' => $expire_time,
            'order_amt' => $total_fee, //订单支付金额
            'return_url' => Config::get('payConfig')['sdPay']['return_url'], //订单前端页面跳转地址
            'create_ip' => str_replace(".","_",\request()->ip()),
            'goods_name' => $goods_name,
            'store_id' => '000000',
            'product_code' => '04010001',
            'clear_cycle' => '3',
            //pay_extra参考语雀文档4.3
            'pay_extra' => json_encode(["userId"=> $this->prefix.$member_id ,"nickName"=>$name],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
            'accsplit_flag' => 'NO',
            'jump_scheme' => 'sandcash://scpay',
            'meta_option' => json_encode([["s" => "Android","n" => "wxDemo","id" => "com.pay.paytypetest","sc" => "com.pay.paytypetest"]]),
            'sign_type' => 'RSA',
        ];
        if($order_type ==3 || $order_type==11){
            //计算卖家的佣金
            unset($body['product_code']);
            unset($body['pay_extra']);
            $body['accsplit_flag'] = "YES";
            $body['product_code'] = "04010003";
            $body['pay_extra'] = json_encode([
                'operationType' => "1",
                'recvUserId' => $this->prefix.$sale_member_id,
                'bizType' => "2",
                'payUserId' => $this->prefix.$member_id,
                'userFeeAmt' => $deduct_values,
            ],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            //C2C
            $body['notify_url'] = Config::get('payConfig')['sdPay']['c2c_notify_url'];  // 异步通知地址
        }else{
            //用户消费的钱进入商家的账户
            $body['notify_url'] = Config::get('payConfig')['sdPay']['c2b_notify_url'];  // 异步通知地址
        }
        $temp = $body;
        unset($temp['goods_name']);
        unset($temp['jump_scheme']);
        unset($temp['expire_time']);
        unset($temp['product_code']);
        unset($temp['clear_cycle']);
        unset($temp['meta_option']);
        $str = Sign::getSignContent($temp);
        $sign = Sign::createSign($str,$this->config['privatePfx_path'],$this->config['privatePfxPwd']);
        $body['sign'] = $sign;
        $query = http_build_query($body);
        $request_url = $this->baseUrl.$query;
        return [
            'jump_url' => $request_url
        ];
    }


}