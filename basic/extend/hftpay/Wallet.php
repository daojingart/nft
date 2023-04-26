<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/9/22   10:41
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace hftpay;


use think\Config;

class Wallet extends Base
{
    /**
     * 开户申请
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface openWallet
     * @Time: 2022/9/22   10:42
     */
    public function openWallet($param)
    {
        $time = date("Ymd",time());
        $params = [
            'version' => 10,
            'mer_cust_id' => $this->config['mer_cust_id'],
            'order_date' => $time,
            'order_id' => $this->orderNo(),
            'user_name' => $param['user_name'],
            'market_type' => 2,
            'acct_usage_type' => 'wallet',
            'id_card' => $param['id_card'],
            'user_id' => $param['member_id'],
            'ret_url' => Config::get('return_url'),
            'bg_ret_url' => Config::get('payConfig')['hytPay']['openWallet_notify_url'],
        ];
        //进行加密数据
        $requestData = Tools::requestData($this->gateWayUrl."/hfpwallet/w00003",Tools::makeSign(self::$sign_param,$params)['check_value'],$this->config['mer_cust_id']);
        $result = Tools::verifySign(array_merge(self::$sign_verifySign, ['check_value'=>json_decode($requestData['body'],1)['check_value']]));
        if($result['resp_code'] != 'C00000'){
            $this->error = $result['resp_desc'];
            return false;
        }
        return $result['redirect_url'];
    }

    /**
     * 钱包查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface queryWallet
     * @Time: 2022/9/23   15:16
     */
    public function queryWallet($member_id)
    {
        $params = [
            'version' => 10,
            'mer_cust_id' => $this->config['mer_cust_id'],
            'user_id' => $member_id
        ];
        //进行加密数据
        $requestData = Tools::requestData($this->gateWayUrl."/alse/qry016",Tools::makeSign(self::$sign_param,$params)['check_value'],$this->config['mer_cust_id']);
		return Tools::verifySign(array_merge(self::$sign_verifySign, ['check_value'=>json_decode($requestData['body'],1)['check_value']]));
    }

    /**
     * 钱包查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface queryWallet
     * @Time: 2022/9/23   15:16
     */
    public function getWalletDetails($member_id)
    {
        $time = date("Ymd",time());
        $params = [
            'version' => 10,
            'mer_cust_id' => $this->config['mer_cust_id'],
            'user_id' => $member_id,
            'order_id' => $this->orderNo(),
            'order_date' => $time
        ];
        //进行加密数据
        $requestData = Tools::requestData($this->gateWayUrl."/hfpwallet/w00004",Tools::makeSign(self::$sign_param,$params)['check_value'],$this->config['mer_cust_id']);
        return Tools::verifySign(array_merge(self::$sign_verifySign, ['check_value'=>json_decode($requestData['body'],1)['check_value']]));
    }





}