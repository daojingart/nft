<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/10/10   18:32
 * +----------------------------------------------------------------------
 * | className: 支付
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\yeepay;

use app\api\model\order\Order as OrderModel;
use app\common\components\helpers\RedisUtils;
use app\common\extend\hfpay\Tools;
use app\common\model\MemberReal;
use app\notice\model\Order as noticeOrderModel;
use exception\BaseException;
use think\Request;

class Payment extends Base
{
    /**
     * 交易下单
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface tradeOrder
     * @Time: 2022/10/10   18:33['yeepay_walletUserNo']
     */
    public function tradeOrder($order_sn,$amount,$goods_info,$member_info,$order_type)
    {
        //判断是否实名认证
        $memberRealInfo = (new MemberReal())->where(['member_id' => $member_info['member_id']])->find();
        if(empty($memberRealInfo)){
            throw new BaseException(['msg' => '请先实名认证', 'code' =>0]);
        }
        $marketType = "首发";
        $request = new \YopRequest($this->config['appKey'], $this->config['private_key']);
        $request->addParam("parentMerchantNo", $this->config['merchantNo']);                                            //发起方商户编号
        $request->addParam("merchantNo", $this->config['merchantNo']);//收款商户编号
        $request->addParam("orderId",$order_sn);
        $request->addParam("orderAmount",$amount);
        $request->addParam("goodsName",$goods_info['goods_name']);
        $request->addParam("notifyUrl",HOST."/notice/Notifyyeepay/notifyPay");
        $request->addParam("redirectUrl",HOST);
        $request->addParam("directPayType",'WALLET_PAY');
        // $request->addParam("userType","USER_ID");
        if($order_type == 3 || $order_type == 11){
            $marketType = "转卖";
            $request->addParam("fundProcessType","DELAY_SETTLE");
        }
        $request->addParam("businessInfo",json_encode([
            'collectionSeries' => $goods_info['goods_name'],   //藏品系列
            'collectionName' => $goods_info['goods_name'],  //藏品名称
            'collectionId' => $goods_info['goods_id'], //藏品编号
            'marketType' => $marketType, //市场交易类型
            'userRegisterMobile' => $member_info['phone'], //注册手机号
            'registTime' => $member_info['create_time'], //注册时间
            'registIp' => Request()->ip(), //注册IP
            'userRegisterIdNo' => $memberRealInfo['card'], //证件号码
            'registId' => $memberRealInfo['member_id'], //用户ID
        ]));
        //提交Post请求，第一个参数为手册上的接口地址
        $response = \YopRsaClient::post("/rest/v1.0/trade/order", $request);
        $responseArray = json_decode($response, true);
        if ($responseArray['state'] == 'SUCCESS') {
            if (isset($responseArray['result']['code']) && $responseArray['result']['code'] !='OPR00000') {
                if($responseArray['result']['code'] == 'MB02001'){
                    return true;
                }
                throw new BaseException(['msg' => $responseArray['result']['message'], 'code' =>0]);
            }
            //拼接 支付链接
            return $this->getPayUrl($responseArray['result']['token'],$member_info['yeepay_walletUserNo']);
        }else{
            throw new BaseException(['msg' => "未知错误", 'code' =>0]);
        }
    }

    /**
     * 拼接支付链接
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getPayUrl
     * @Time: 2022/10/10   18:45
     */
    public function getPayUrl($token,$member_id)
    {
        $date=date_create();
        $dara_request = array(
            "appKey"=> $this->config['appKey'],
            "merchantNo" => $this->config['merchantNo'],
            "token" => $token,
            "timestamp" => date_timestamp_get($date),
            "directPayType" => "",
            "cardType" => "",
            "userNo" => "",
            "userType" => "",
            "ext" => json_encode([
                'walletMemberNo' => $member_id,
                'limitPayType' => 'WALLET_PAY',
            ]),
        );
        $getUrl = $this->getUrl($dara_request, $this->config['private_key']);
        $url = "https://cash.yeepay.com/cashier/std?" . $getUrl;
        return htmlspecialchars($url);
    }

    /**
     * 拼装URL
     * @param $response
     * @param $private_key
     * @return string
     * @Time: 2022/10/10   18:49
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getUrl
     */
    public function  getUrl($response,$private_key)
    {
        $content = $this->toString($response);
        $sign= \YopSignUtils::signRsa($content,$private_key);
        $url = $content."&sign=".$sign;
        return  $url;
    }

    public function toString($arraydata){
        $Str="";
        foreach ($arraydata as $k=>$v){
            $Str .= strlen($Str) == 0 ? "" : "&";
            $Str.=$k."=".$v;
        }
        return $Str;
    }


    /**
     * 开户回调
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface notify
     * @Time: 2022/10/11   17:30
     */
    public function notify($param)
    {
        self::doLogs($param,'Paymentnotify');
        $paramDecode = urldecode($param);
        $paramDecode = explode('&', $paramDecode);
        $paramDecode = substr($paramDecode[0],9);
        $data = \YopSignUtils::decrypt($paramDecode,$this->config['private_key'],$this->config['public_key']);
        $result = json_decode($data,true);
        self::doLogs($result,'Paymentnotify');
        if($result['status'] != 'SUCCESS'){
            //处理回调的问题
            echo "SUCCESS";die;
        }
        //处理回调信息
        $Order_info = (new OrderModel)->where(['order_no'=>$result['orderId']])->find();
        if (empty($Order_info)) {
            echo "SUCCESS";die;
        }
        if ($Order_info['pay_status']['value'] == 2) {
            echo "SUCCESS";die;
        }
        $arr = [
            'transaction_id' => $result['uniqueOrderNo'],
            'out_trade_no' => $result['orderId'],
        ];
        //回调成功 删除这个元素值
        $lockKey = "returnyeePay_notifyUrl:{$result['orderId']}";
        if (RedisUtils::lock($lockKey, 10)) {
            Tools::doLogs("{$result['orderId']}--进入多次被锁");
            echo "SUCCESS";die;
        }
        try {
            if((new noticeOrderModel())->callBack($arr)){
                echo "SUCCESS";die;
                RedisUtils::unlock($lockKey);
            }
        } catch (\Exception $e) {
            self::doLogs($e->getMessage(),'Paymentnotify');
            RedisUtils::unlock($lockKey);
        }
        RedisUtils::unlock($lockKey);
    }

}