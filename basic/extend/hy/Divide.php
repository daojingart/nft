<?php

namespace hy;

/**
 * 汇元分润订单类
 */
class Divide extends Base
{
    /**
     * 申请分润 【分账接口 二级市场使用】
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface ApplyPayShare
     * @Time: 2022/8/20   10:20
     */
    public function ApplyPayShare($params)
    {
        $param = [
            'hy_bill_no' => $params['hy_bill_no'], //分润支付成功的汇元返回的数据
            'out_trade_no' => $params['out_trade_no'], //分润的订单号
            'out_share_no' => $params['out_share_no'], //新的订单号
            'notify_url' => HOST."/notice/Notify/PayShareCallBack",  //分润回调信息
            'return_url' => HOST,  //同步信息
        ];
        //判断下分佣对象 如果是二级的是两个人  如果是一级则全部都是商户的
        if($params['order_type'] == 3){
            $param['pay_amt_fen'] = $this->moneyToSave(bcadd($params['user_amount'],$params['pay_amt_fen'],4));  //分润的总订单金额
            $param['share_info'] = json_encode([
                [
                    'user_uid'=> $params['user_uid'],
                    'share_amt_fen' => $this->moneyToSave($params['user_amount']),  //用户分润的金额
                ],
                [
                    'user_uid'=> $this->config['owerId'],
                    'share_amt_fen' => -100,  //用户分润的金额
                ]
            ]);
        }else{
            $param['pay_amt_fen'] = $this->moneyToSave($params['amountTransaction']);  //分润的总订单金额
            $param['share_info'] = json_encode([
                [
                    'user_uid'=> $this->config['owerId'],
                    'share_amt_fen' => -100,  //用户分润的金额
                ]
            ]);
        }
        $this->publicRequest['api_type'] = 0;
        $this->publicRequest['method'] = 'v1.ApplyPayShare';
        $this->publicRequest['biz_content'] = json_encode($param);
        //对公共参数进行签名
        $sign = Utils::generaterSign($this->publicRequest,$this->config['md5Key']);
        $this->publicRequest["sign"] = $sign;
        $requestString = json_encode($this->publicRequest);
        $result_json = Utils::sendRequest(self::$gateWayUrl,$this->ApplyPayShare,$requestString);
        $result_array = json_decode($result_json,true);
        self::doLogs(array_merge(['out_trade_no'=>$params['out_trade_no']], $result_array),"ApplyPayShare");
    }


    /**
     * 分润退回
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface ApplyPayShare
     * @Time: 2022/8/20   10:20
     */
    public function ShareFullRefund($params)
    {
        $param = [
            'hy_bill_no' => $params['hy_bill_no'], //分润支付成功的汇元返回的数据
            'out_trade_no' => $params['out_trade_no'], //分润的订单号
        ];
        $this->publicRequest['api_type'] = 0;
        $this->publicRequest['method'] = 'v1.ShareFullRefund';
        $this->publicRequest['biz_content'] = json_encode($param);
        //对公共参数进行签名
        $sign = Utils::generaterSign($this->publicRequest,$this->config['md5Key']);
        $this->publicRequest["sign"] = $sign;
        $requestString = json_encode($this->publicRequest);
        $result_json = Utils::sendRequest($this->ShareFullRefund,$requestString);
        $result_array = json_decode($result_json,true);
        self::doLogs(array_merge(['out_trade_no'=>$params['out_trade_no']], $result_array),"ShareFullRefund");
    }


    /**
     * 保存金额
     * @param $value
     * @return string
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2021/12/4 13:49
     */
    public function moneyToSave($value) : string
    {
        $value = floatval($value);
        return bcmul((string)$value, '100',3);
    }



}