<?php

namespace lianlian;

use exception\BaseException;
use lianlian\tools\Http;
use lianlian\tools\Sign;
use think\Config;

/**
 * 连连支付类
 */
class Txn extends LiPay
{
    /**
     * 统一支付创单 [商户在充值/消费交易模式场景下使用，先通过该接口完成支付统一创单，后续根据业务场景调用不同的支付接口完成付款。]
     * @ApiAuthor [Mr.Zhang]
     * @param $param [txn_type:交易类型 user_id:用户ID pay_expire:支付有效期 orderInfo:订单信息数组 [txn_seqno:订单号 txn_time：交易时间 total_amount：交易金额 goods_name:商品名称]]
     */
    public function tradecreate($param)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'user_type' => 'REGISTERED',
            'notify_url' => Config::get('payConfig')['llPay']['pay_notify_url'],  // 异步通知地址
            'return_url' => HOST,
        ];
        $param = array_merge($params,$param);
        $sign = Sign::signData(json_encode($param), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/txn/tradecreate', $param, $sign);
        $result_array = json_decode($result_json, true);
        if ($result_array['ret_code'] != '0000') {
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return $result_array['txn_seqno'];
    }


    /**
     * 余额支付
     * @ApiAuthor [Mr.Zhang]
     */
    public function paymentBalance($order_no,$total_fee,$goods_name,$member_id,$phone,$create_time,$name,$card,$pwd,$random_key)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'txn_seqno' => $order_no,
            'total_amount' => $total_fee,
            'risk_item' => json_encode([
                'frms_client_chnl' => '16',
                'frms_ip_addr' => Request()->ip(),
                'user_auth_flag' => '1',
                'frms_ware_category' => '4007',
                'goods_name' =>$goods_name,
                'user_info_mercht_userno' => $member_id,
                'user_info_bind_phone' => $phone,
                'user_info_dt_register' => date("YmdHis",strtotime($create_time)),
                'user_info_full_name' => $name,
                'user_info_id_type' => 0,
                'user_info_id_no' => $card,
                'user_info_identify_state' => 1,
                'user_info_identify_type' => 3
            ]),
            'payerInfo' => [
                'user_id' => $member_id,
                'password' => $pwd,
                'random_key' => $random_key
            ]
        ];
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/txn/payment-balance', $params, $sign);
        $result_array = json_decode($result_json, true);
        if(isset($result_array['ret_code']) && $result_array['ret_code']=='8888'){
            //需要进行二级短信交易确认
            throw new BaseException(['msg' =>$result_array['token'], 'code' =>8888]);
        }
        if(!isset($result_array['ret_code']) || $result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -10]);
        }
        return [
            'order_sn' => $result_array['txn_seqno'],
        ];
    }


    /**
     * 银行卡支付
     */
    public function paymentBankcard($order_no,$total_fee,$goods_name,$member_id,$phone,$create_time,$name,$card,$password,$random_key,$linked_agrtno)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'txn_seqno' => $order_no,
            'total_amount' => $total_fee,
            'risk_item' => json_encode([
                'frms_client_chnl' => '16',
                'frms_ip_addr' => Request()->ip(),
                'user_auth_flag' => '1',
                'frms_ware_category' => '4007',
                'goods_name' =>$goods_name,
                'user_info_mercht_userno' => $member_id,
                'user_info_bind_phone' => $phone,
                'user_info_dt_register' => date("YmdHis",strtotime($create_time)),
                'user_info_full_name' => $name,
                'user_info_id_type' => 0,
                'user_info_id_no' => $card,
                'user_info_identify_state' => 1,
                'user_info_identify_type' => 3
            ]),
            'directionalpay_flag' => 'N',
            'payerInfo' => [
                'user_id' => $member_id,
                'password' => $password,
                'random_key' => $random_key
            ],
            'bankCardInfo' => [
                'linked_agrtno' => $linked_agrtno,
            ],
            'payMethods' => [
                [
                    'method' => 'AGRT_DEBIT_CARD',
                    'amount' => $total_fee
                ]
            ],
        ];
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/txn/payment-bankcard', $params, $sign);
        $result_array = json_decode($result_json, true);
        if(isset($result_array['ret_code']) && $result_array['ret_code']=='8888'){
            //需要进行二级短信交易确认
            throw new BaseException(['msg' =>$result_array['token'], 'code' =>8888]);
        }
        if(!isset($result_array['ret_code']) || $result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -10]);
        }
        return [
            'order_sn' => $result_array['txn_seqno'],
        ];
    }


    /**
     * 交易的二次确认支付
     */
    public function validationSms($member_id,$order_sn,$total_amount,$token,$verify_code)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'payer_type' => 'USER',
            'payer_id' => $member_id,
            'txn_seqno' => $order_sn,
            'total_amount' => $total_amount,
            'token' => $token,
            'verify_code' => $verify_code
        ];
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/txn/validation-sms', $params, $sign);
        $result_array = json_decode($result_json, true);
        if(isset($result_array['ret_code'])){
            if($result_array['ret_code'] !='0000'){
                if($result_array['ret_code'] =='8889'){
                    return [
                        'order_sn' =>$result_array['txn_seqno'],
                        'ret_msg' => $result_array['ret_msg']
                    ];
                }
                throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -10]);
            }
        }
        return [
            'order_sn' =>$result_array['txn_seqno'],
            'ret_msg' => $result_array['ret_msg']
        ];
    }

}