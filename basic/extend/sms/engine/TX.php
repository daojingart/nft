<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/14   11:12 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 腾讯短信驱动服务类
 * +----------------------------------------------------------------------
 */

namespace sms\engine;


class TX extends Server
{
    private $config;
    private $smsConfig;

    public function __construct($config,$sms_config)
    {
        parent::__construct();
        $this->config = $config;
        $this->smsConfig = $sms_config;
    }

    /**
     * 发送短信
     * @return array|bool
     */
    public function sendSms()
    {
        return $this->bindRequestBody();
    }

    /**
     * @Notes: 封装请求体
     * @Interface bindRequestBody
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/14   9:34 下午
     */
    public function bindRequestBody()
    {
        try {
            //开启防止刷短信机制
            $this->sms_safe($this->smsConfig);
            $result = $this->sendWithParam();  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $resultArray = json_decode($result,true);
            if($resultArray['result'] != '0'){
                $this->error = $resultArray['errmsg'];
                return false;
            }
            return true;
        } catch(\Exception $e) {
            $this->error =$e->getMessage();
            return false;
        }
    }


    /**
     * 发送请求体
     * @param string $appid       应用的APPID
     * @param string $appkey      应用的APPkey
     * @param array  $params      模板参数列表，如模板 {1}...{2}...{3}，那么需要带三个参数
     * @param string $sign        签名，如果填空串，系统会使用默认签名
     * @param string $extend      扩展码，可填空串
     * @param string $ext         服务端原样返回的参数，可填空串
     * @return string 应答json字符串，详细内容参见腾讯云协议文档
     */
    public function sendWithParam($extend = "", $ext = "")
    {
        $params[] = $this->smsCode;
        $curTime = time();
        $wholeUrl = "https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid=" . $this->config['access_key'] . "&random=" . $this->smsCode;
        // 按照协议组织 post 包体
        $data = new \stdClass();
        $tel = new \stdClass();
        $tel->nationcode = "".'86';
        $tel->mobile = "".$this->phone;
        $data->tel = $tel;
        $data->sig = $this->calculateSigForTempl($this->config['secret_key'], $this->smsCode,
            $curTime, $this->phone);
        $data->tpl_id = $this->config['Template_ID'];
        $data->params = $params;
        $data->sign = $this->config['signature'];
        $data->time = $curTime;
        $data->extend = $extend;
        $data->ext = $ext;
        return $this->sendCurlPost($wholeUrl,$data);
    }


    /**
     * 生成签名
     * @param string $appkey        sdkappid对应的appkey
     * @param string $random        随机正整数
     * @param string $curTime       当前时间
     * @param array  $phoneNumber   手机号码
     * @return string  签名结果
     */
    public function calculateSigForTempl($appkey,$random, $curTime, $phoneNumber)
    {
        $phoneNumbers = array($phoneNumber);
        $phoneNumbersString = $phoneNumbers[0];
        for ($i = 1; $i < count($phoneNumbers); $i++) {
            $phoneNumbersString .= ("," . $phoneNumbers[$i]);
        }
        return hash("sha256", "appkey=".$appkey."&random=".$random
            ."&time=".$curTime."&mobile=".$phoneNumbersString);
    }

    /**
     * 发送请求
     * @param string $url      请求地址
     * @param array  $dataObj  请求内容
     * @return string 应答json字符串
     */
    public function sendCurlPost($url, $dataObj)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataObj));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec($curl);
        if (false == $ret) {
            // curl_exec failed
            $result = "{ \"result\":" . -2 . ",\"errmsg\":\"" . curl_error($curl) . "\"}";
        } else {
            $rsp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "{ \"result\":" . -1 . ",\"errmsg\":\"". $rsp
                    . " " . curl_error($curl) ."\"}";
            } else {
                $result = $ret;
            }
        }
        curl_close($curl);
        return $result;
    }




}