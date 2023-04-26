<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/7/18   14:53
 * +----------------------------------------------------------------------
 * | className: 提现打款
 * +----------------------------------------------------------------------
 */

namespace lianlian;

use app\admin\model\finance\Withdraw;
use app\common\extend\llp\Base;
use app\common\model\MemberCard;
use app\common\model\MemberReal;
use exception\BaseException;
use lianlian\tools\Http;
use lianlian\tools\Sign;
use think\Config;

class Withdrawal extends LiPay
{
    /**
     * 提现打款
     * @ApiAuthor [Mr.Zhang]
     */
    public function payment($member_id,$linked_agrtno,$order_sn,$time,$price,$password,$random_key,$actual_amount)
    {
        $member_real_info = (new MemberReal())->where(['member_id'=>$member_id])->find();
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['app_id'],
            'notify_url' => Config::get('payConfig')['llPay']['withdrawal_notify_url'],  // 异步通知地址
            'risk_item' => json_encode([
                'frms_client_chnl' => '16',
                'frms_ip_addr' => Request()->ip(),
                'user_auth_flag' => '1',
                'frms_ware_category' => '4007',
                'goods_name' => "用户提现打款" . $member_id,
                'user_info_mercht_userno' => $member_id,
                'user_info_bind_phone' => $member_real_info['phone'],
                'user_info_dt_register' => date("YmdHis", strtotime($member_real_info['create_time'])),
                'user_info_full_name' => $member_real_info['name'],
                'user_info_id_type' => 0,
                'user_info_id_no' => $member_real_info['card'],
                'user_info_identify_state' => 1,
                'user_info_identify_type' => 3
            ]),
            'linked_agrtno' => $linked_agrtno,
            'funds_flag' => 'N',
            'check_flag' => 'Y',
            'pay_time_type' => 'TRANS_THIS_TIME',
            'orderInfo' => [
                'txn_seqno' => $order_sn,
                'txn_time' => $time,
                'total_amount' => $price,
            ],
            'payerInfo' => [
                'payer_type' => 'USER',
                'payer_id' => $member_id,
                'payer_accttype' => 'USEROWN',
                'password' => $password,
                'random_key' => $random_key
            ],
        ];
        if ($actual_amount > 0) {
            $params['orderInfo']['fee_amount'] = $actual_amount;
        }
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/txn/withdrawal', $params, $sign);
        return json_decode($result_json, true);
    }


    /**
     * 提现二次确认
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface withdrawalCheck
     * @Time: 2022/8/1   15:16
     */
    public function withdrawalCheck($param)
    {
        $params = [
            'timestamp' => $this->getTimestamp(),
            'oid_partner' => $this->config['llp_app_id'],
            'orderInfo' => [
                'txn_seqno' => $param['txn_seqno'],
                'total_amount' =>  $param['total_amount'],
                'fee_amount' => $param['fee_amount'],
            ],
            'checkInfo' => [
                'check_result' => $param['check_result'],
                'check_reason' => $param['check_reason']
            ],
        ];
        $sign = Sign::signData(json_encode($params), $this->config['llp_rsa_private_key']);
        $result_json = Http::getHttpResponseJSON($this->baseUrl . '/v1/txn/withdrawal-check', $params, $sign);
        return json_decode($result_json, true);
    }

}