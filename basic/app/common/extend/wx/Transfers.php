<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/7   6:12 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 企业付款到零钱
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\wx;


use app\common\model\Member;
use exception\BaseException;

class Transfers extends Wx
{
    /**
     * @Notes: 请求发起打款操作
     * @Interface requestPromotionTransfers
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/7   6:14 下午
     */
    public  function requestPromotionTransfers($amount,$member_id,$order_type,$text)
    {
        $member_info = (new Member())->where(['member_id' => $member_id])->find();
        $openid = '';
        $app_id = '';
        switch ($order_type)
        {
            case "1": //公众号
                $openid =  $member_info['open_id'];
                $app_id = $this->config['wx_app_id'];
                break;
            case "2": //小程序
                $openid =  $member_info['wxapp_open_id'];
                $app_id = $this->config['wxapp_app_id'];
                break;
            case "3": //APP
                $openid =  $member_info['app_open_id'];
                $app_id = $this->config['wxapp_id'];
                break;
        }
        if(!$openid){
            throw new BaseException(['msg' => "非微信用户无法使用企业付款到零钱功能,请在系统管理-->提现配置处修改付款方式"]);
        }
        //构建请求体
        $requestParam = [
            'mch_appid' => $app_id, //申请商户号的appid或商户号绑定的appid
            'mchid' => $this->config['mch_id'], //微信支付分配的商户号
            'nonce_str' => md5(time()), //随机字符串
            'partner_trade_no' => $this->orderNo(), //商户订单号，需保持唯一性
            'openid' => $openid, //商户appid下，某用户的openid
            'check_name' => 'NO_CHECK',
//            'amount' => 30,
            'amount' => sprintf("%.2f", $amount)*100,
            'desc' => $text,
            'spbill_create_ip' => request()->ip(),
        ];
        $requestParam['sign'] = $this->makeSign($requestParam);
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $result = $this->postXmlSSLCurl($this->toXml($requestParam),$url);
        $prepay = $this->fromXml($result);
        self::doLogs($prepay);
        if(($prepay['return_code']=='SUCCESS') && ($prepay['result_code']=='SUCCESS')){
           //打款成功 需要修改订单号信息等
           return ['code'=>'1','data'=>$prepay['payment_no']];
        }else if(($prepay['return_code']=='FAIL') || ($prepay['result_code']=='FAIL')){
            //打款失败 处理对应的业务逻辑
            return ['code'=>'-1','data'=>'','msg'=>$prepay['err_code_des']];
        }else{
            return ['code'=>'-1','data'=>'','msg'=>$prepay['err_code_des']];
        }
    }


    /**
     * 将xml转为array
     * @param $xml
     * @return mixed
     */
    private function fromXml($xml)
    {
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }


    /**
     * 需要使用证书的请求
     */
    public  function postXmlSSLCurl($xml,$url,$second=30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT, CERT_LOG_PATH);
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,KEY_LOG_PATH);
        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);

        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            curl_close($ch);
            return false;
        }
    }

    /**
     * 生成签名
     * @param $values
     * @return string 本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    private function makeSign($values)
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->toUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $this->config['pay_secret'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     * @param $values
     * @return string
     */
    private function toUrlParams($values)
    {
        $buff = '';
        foreach ($values as $k => $v) {
            if ($k != 'sign' && $v != '' && !is_array($v)) {
                $buff .= $k . '=' . $v . '&';
            }
        }
        return trim($buff, '&');
    }

    /**
     * 输出xml字符
     * @param $values
     * @return bool|string
     */
    private function toXml($values)
    {
        if (!is_array($values)
            || count($values) <= 0
        ) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 生成退款订单号
     */
    protected function orderNo()
    {
        return "out".date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

}