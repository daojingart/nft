<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/19   17:00
 * +----------------------------------------------------------------------
 * | className: 汇元支付 统一支付申请
 * +----------------------------------------------------------------------
 */

namespace hy;

use exception\BaseException;
use hy\tools\Utils;
use think\Config;

class Pay extends Base
{

    /**
     * 发起支付请求
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface payment
     * @Time: 2022/8/19   17:01
     * @param $pay_method 2=钱包支付  63=新快捷(授权支付 有短信)
     */
    public function payments($member_id,$total_fee,$order_no,$goods_name,$order_type,$txn_seqno,$pay_method,$expire_time)
    {
        $param = [
            'pay_amt_fen' => moneyToSave($total_fee),
            'out_trade_no' => $order_no,
            'pay_method' => $pay_method,
            'from_type' => '1',
            'user_ip' => Request()->ip(),
            'subject' => $goods_name,
            'notify_url' => Config::get('payConfig')['hyPay']['pay_notify_url'],
            'return_url' => Config::get('payConfig')['hyPay']['pay_return_url'],
            'user_uid' => $member_id,
            'expire_time' =>$expire_time
        ];
        if($pay_method==63){
            $param['pay_option'] = json_encode([
                'is_guarantee' =>1,
                'auth_code' => $txn_seqno,
            ]);
        }else{
            $param['pay_option'] = json_encode([
                'is_guarantee' =>1
            ]);
        }
        $this->publicRequest['api_type'] = 0;
        $this->publicRequest['method'] = 'v1.ApplyPay';
        $this->publicRequest['biz_content'] = json_encode($param);
        //对公共参数进行签名
        $sign = Utils::generaterSign($this->publicRequest,$this->config['md5Key']);
        $this->publicRequest["sign"] = $sign;
        $requestString = json_encode($this->publicRequest);
        $result_json = Utils::sendRequest(self::$gateWayUrl,$this->ApplyPay,$requestString);
        $result_array = json_decode($result_json,true);
        if($result_array['return_code'] != 'SUCCESS'){
            $this->error = $result_array['return_msg'];
            return false;
        }
        if($pay_method == 63){
            return ['hy_bill_no'=>$result_array['hy_bill_no'],'hy_token_id'=>$result_array['hy_token_id']];
        }
        return [
            'redirect_url' => $result_array['redirect_url'],
        ];
    }


    /**
     * 支付交易确认
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getConfirmTrade
     * @Time: 2022/9/1   20:06
     */
    public function getConfirmTrade($verify_code,$hy_bill_no,$hy_token_id)
    {
        $param = [
            'hy_bill_no' => $hy_bill_no,
            'verify_code' => $verify_code,
            'hy_token_id' => $hy_token_id
        ];
        $this->publicRequest['api_type'] = 0;
        $this->publicRequest['method'] = 'V1/ConfirmPay';
        $this->publicRequest['biz_content'] = json_encode($param);
        //对公共参数进行签名
        $sign = Utils::generaterSign($this->publicRequest,$this->config['md5Key']);
        $this->publicRequest["sign"] = $sign;
        $requestString = json_encode($this->publicRequest);
        $result_json = Utils::sendRequest(self::$gateWayUrl,$this->ConfirmTrade,$requestString);
        $result_array = json_decode($result_json,true);
        if($result_array['return_code'] != 'SUCCESS'){
            $this->error = $result_array['return_msg'];
            return false;
        }
        return true;
    }


    /**
     * 查询订单接口或者支付状态 [暂未对接使用]
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getBillInfo
     * @Time: 2022/8/29   18:27
     */
    public function getBillInfo($params)
    {
        $param = [
            'out_trade_no' => $params['order_no'],
            'trade_type' => '5'
        ];
        $this->publicRequest['api_type'] = 0;
        $this->publicRequest['method'] = 'v1.BillInfo';
        $this->publicRequest['biz_content'] = json_encode($param);
        //对公共参数进行签名
        $sign = Utils::generaterSign($this->publicRequest,$this->config['md5Key']);
        $this->publicRequest["sign"] = $sign;
        $requestString = json_encode($this->publicRequest);
        $result_json = Utils::sendRequest($this->BillInfo,$requestString);
        $result_array = json_decode($result_json,true);
        return $result_array;
    }


}