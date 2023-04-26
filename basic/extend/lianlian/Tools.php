<?php

namespace lianlian;

use exception\BaseException;
use lianlian\tools\Http;
use lianlian\tools\Sign;

class Tools extends LiPay
{
    /**
     * 随机因子获取
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function getAcctmgrRandom($member_id,$flag_chnl)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'user_id' => $member_id,
            'flag_chnl' => $flag_chnl,
        ];
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/get-random', $params, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code']!='0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return [
            'license' => $result_array['license'],
            'map_arr' => $result_array['map_arr'],
            'random_key' => $result_array['random_key'],
            'random_value' => $result_array['random_value'],
            'rsa_public_content' => $result_array['rsa_public_content'],
            'oid_partner' => $result_array['oid_partner'],
            'user_id' => $member_id,
        ];
    }

    /**
     * 申请密码控件Token
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function applyPasswordElementToken($member_id,$passwordScene,$flag_chnl,$amount=0,$txn_seqno='')
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'user_id' => $member_id,
            'txn_seqno' =>$this->orderNo(),
            'password_scene' => $passwordScene,
            'flagChnl' => $flag_chnl
        ];
        //判断传值的是否是
        if($passwordScene == 'pay_password'){
            $params['amount'] = $amount;
            $params['txn_seqno'] = $txn_seqno;
        }
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->basePageUrl . '/v1/acctmgr/apply-password-element', $params, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code']!='0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return $result_array;
    }

}