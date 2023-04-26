<?php

namespace lianlian;

use exception\BaseException;
use lianlian\tools\Http;
use lianlian\tools\Sign;
use think\Config;

/**
 * 连连开户类
 */
class Acctmgr extends LiPay
{
    /**
     * 用户开户【页面接入】
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function openacctApply($data)
    {
        $param = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'user_id' => $data['member_id'],
            'txn_seqno' => $this->orderNo(),
            'txn_time' => $this->getTimestamp(),
            'flag_chnl' => 'H5',
            'notify_url' => Config::get('payConfig')['llPay']['openacct_notify_url'],
            'return_url' => Config::get('payConfig')['llPay']['openacct_return_url'],
            'user_type' => 'INNERUSER',
            'basicInfo' => [
                'reg_phone' => $data['phone'],
                'user_name' => $data['real_name'],
                'id_type' => "ID_CARD",
                'id_no' => $data['id_card'],
            ],
            'accountInfo' => [
                'account_type' => 'PERSONAL_PAYMENT_ACCOUNT',
                'account_need_level' => 'V3'
            ]
        ];
        $sign = Sign::signData(json_encode($param), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->basePageUrl . '/v1/acctmgr/openacct-apply', $param, $sign);
        $result_array = json_decode($result_json, true);
        if ($result_array['ret_code'] != '0000') {
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return $result_array['gateway_url'];
    }

    /**
     * 用户信息查询
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function getQueryUserinfo($member_id)
    {
        $param = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'user_id' => $member_id,
        ];
        $sign = Sign::signData(json_encode($param), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/query-userinfo', $param, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        if($result_array['user_status'] == 'NORMAL' && $result_array['bank_open_flag']==1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 修改密码申请
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function editPasswordApply($member_id,$linked_acctno)
    {
        $param = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'user_id' => $member_id,
        ];
        if($linked_acctno){
            $param['linked_acctno'] = $linked_acctno;
        }
        $sign = Sign::signData(json_encode($param), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/find-password-apply', $param, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return ['token'=>$result_array['token'],'reg_phone' => $result_array['reg_phone']];
    }

    /**
     * 修改密码确认
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function findPasswordVerify($params)
    {
        $param = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
        ];
        $param = array_merge($params,$param);
        $sign = Sign::signData(json_encode($param), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/find-password-apply', $param, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return true;
    }

    /**
     * 账户信息查询
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function queryAcctinfo($member_id)
    {
        $param = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'user_id' => $member_id,
            'user_type' => 'INNERUSER'
        ];
        $sign = Sign::signData(json_encode($param), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/query-acctinfo', $param, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        if($result_array['acctinfo_list'][0]['acct_type']=='USEROWN_AVAILABLE'){ //用户自有可用账户
            $pending_account = $result_array['acctinfo_list'][0]['amt_balaval'];
            $available_amount = $result_array['acctinfo_list'][1]['amt_balaval'];
        }else{
            $pending_account = $result_array['acctinfo_list'][1]['amt_balaval'];
            $available_amount = $result_array['acctinfo_list'][0]['amt_balaval'];
        }
        return ['pending_account'=>$pending_account,'available_amount'=>$available_amount];
    }

    /**
     * 账户流水查询
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function queryAcctserial($params)
    {
        $param = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'user_type' => 'INNERUSER',
            'acct_type' => 'USEROWN_AVAILABLE',
            'date_end' => $this->getTimestamp()-60,
            'page_size' => 10,
            'sort_type' => 'DESC'
        ];
        $param = array_merge($params,$param);
        if($param['flag_dc'] == 'ALL'){
            unset($param['flag_dc']);
        }
        $sign = Sign::signData(json_encode($param), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/acctmgr/query-acctserial', $param, $sign);
        $result_array = json_decode($result_json, true);
        if($result_array['ret_code'] != '0000'){
            throw new BaseException(['msg' => $result_array['ret_msg'], 'code' => -1]);
        }
        return $result_array;
    }


}