<?php

namespace lianlian;

use exception\BaseException;
use lianlian\tools\Http;
use lianlian\tools\Sign;
use think\Config;

/**
 * 连连银行卡类
 */
class BankCard extends LiPay
{
    /**
     * 绑卡申请
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function bindcardApply($param)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'txn_seqno' =>$this->orderNo(),
            'txn_time' => $this->getTimestamp(),
            'notify_url' => Config::get('payConfig')['llPay']['bindcard_notify_url'],  // 异步通知地址
        ];
        $params = array_merge($params, $param);
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/individual-bindcard-apply', $params, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return ['token'=>$result_array['token'],'txn_seqno'=>$result_array['txn_seqno']];
    }


    /**
     * 绑卡验证
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function individualBindCardVerify($param)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
        ];
        $params = array_merge($params, $param);
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/individual-bindcard-verify', $params, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return $result_array['linked_agrtno'];
    }


    /**
     * 绑卡列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function queryLinkedAcct($param)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
        ];
        $params = array_merge($params, $param);
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/query-linkedacct', $params, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return $result_array['linked_acctlist'];
    }

    /**
     * 解绑银行卡
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function unlinkedAcctIndApply($param)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'txn_seqno' => $this->orderNo(),
            'txn_time' => $this->getTimestamp(),
            'notify_url' => Config::get('payConfig')['llPay']['unset_bindcard_notify_url'],  // 异步通知地址
        ];
        $params = array_merge($params, $param);
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/query-linkedacct', $params, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return true;
    }

}