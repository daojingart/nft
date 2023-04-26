<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/10/9   19:12
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\yeepay;


use app\common\model\Member;
use exception\BaseException;

class Wallet extends Base
{

    /**
     * 获取钱包的登录注册
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getWalletUrl
     * @Time: 2022/10/9   19:13
     */
    public function getWalletUrl($member_info)
    {
        $request = new \YopRequest($this->config['appKey'], $this->config['private_key']);
        $request->addParam("parentMerchantNo", $this->config['merchantNo']);                                            //发起方商户编号
        $request->addParam("merchantNo", $this->config['merchantNo']);                //收款商户编号
        $request->addParam("merchantUserNo", $member_info['member_id']);                            //订单号
        $request->addParam("returnUrl", HOST);                        //商品信息
        $request->addParam("notifyUrl", HOST."/notice/Notifyyeepay/notifywallet");                                            //回调通知地址
        $request->addParam("mobile", $member_info['phone']);                                            //回调通知地址
        $request->addParam("requestNo", "openWallet" . mt_rand(100, 999) . mt_rand(100, 999) . mt_rand(100, 999));                            //订单号
        //提交Post请求，第一个参数为手册上的接口地址
        $response = \YopRsaClient::post("/rest/v2.0/m-wallet/wallet/index", $request);
        $responseArray = json_decode($response, true);
        if ($responseArray['state'] == 'SUCCESS') {
            if (isset($responseArray['result']['code'])) {
                throw new BaseException(['msg' => $responseArray['result']['message'], 'code' =>0]);
            }
            return $responseArray['result']['url'];
        }else{
            throw new BaseException(['msg' => "未知错误", 'code' =>0]);
        }
    }


    /**
     * 获取钱包账户信息查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getWalletQuery
     * @Time: 2022/10/10   18:10
     */
    public function getWalletQuery($member_info)
    {
        $request = new \YopRequest($this->config['appKey'], $this->config['private_key']);
        $request->addParam("parentMerchantNo", $this->config['merchantNo']);                                            //发起方商户编号
        $request->addParam("merchantNo", $this->config['merchantNo']);                //收款商户编号
        $request->addParam("externalUserId", $member_info['member_id']);                            //订单号
        //提交Post请求，第一个参数为手册上的接口地址
        $response = \YopRsaClient::get("/rest/v1.0/m-wallet/member/query", $request);
        $responseArray = json_decode($response, true);
        if ($responseArray['state'] == 'SUCCESS') {
            if (isset($responseArray['result']['code']) && $responseArray['result']['code'] !='000000') {
                if($responseArray['result']['code'] == 'MB02001'){
                    return true;
                }
                throw new BaseException(['msg' => $responseArray['result']['message'], 'code' =>0]);
            }
            (new Member())->where(['member_id'=>$member_info['member_id']])->update(['yeepay_walletUserNo'=>$responseArray['result']['memberNo']]);
            unset($responseArray['result']['code']);
            unset($responseArray['result']['message']);
            unset($responseArray['result']['memberNo']);
            unset($responseArray['result']['externalUserId']);
            return $responseArray['result'];
        }else{
            throw new BaseException(['msg' => "未知错误", 'code' =>0]);
        }
    }

    /**
     * 开户回调
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface notify
     * @Time: 2022/10/11   17:30
     */
    public function notify($param)
    {
        self::doLogs($param,'Walletnotify');
        $paramDecode = urldecode($param);
        $paramDecode = explode('&', $paramDecode);
        $paramDecode = substr($paramDecode[0],9);
        $data = \YopSignUtils::decrypt($paramDecode,$this->config['private_key'],$this->config['public_key']);
        $result = json_decode($data,true);
        self::doLogs($result,'Walletnotify');
        (new Member())->where(['member_id'=>$result['merchantUserNo']])->update(['yeepay_walletUserNo'=>$result['walletUserNo']]);
        echo "SUCCESS";
    }



}