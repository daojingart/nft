<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/14   10:42 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 阿里云短信驱动服务类
 * +----------------------------------------------------------------------
 */

namespace sms\engine;


class AL extends Server
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
        $param = [
            'PhoneNumbers' => $this->phone,
            'SignName' => $this->config['signature'],
            'TemplateCode' => $this->config['Template_ID'],
            'TemplateParam' => [
                'code' => $this->smsCode
            ]
        ];
        if(!empty($param["TemplateParam"]) && is_array($param["TemplateParam"])) {
            $param["TemplateParam"] = json_encode($param["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        try{
            $this->sms_safe($this->smsConfig);
            // 此处可能会抛出异常，注意catch
            $content = $this->requestUrl(
                $this->config['access_key'],
                $this->config['secret_key'],
                "dysmsapi.aliyuncs.com",
                array_merge($param, array(
                    "RegionId" => "cn-hangzhou",
                    "Action" => "SendSms",
                    "Version" => "2017-05-25",
                ))
            );
            if($content->Message!='OK'){
                $this->error = $content->Message;
                return false;
            }
            return true;
        }catch(\Exception $e){
            $this->error =  $e->getMessage();
            return false;
        }
    }


    /**
     * 阿里云生成签名并发起请求
     * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
     * @param $accessKeySecret string AccessKeySecret
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @param $security boolean 使用https
     * @param $method boolean 使用GET或POST方法请求，VPC仅支持POST
     * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
     */
    public function requestUrl($accessKeyId, $accessKeySecret, $domain, $params, $security=false, $method='POST') {
        $apiParams = array_merge(array(
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0,0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $accessKeyId,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
        ), $params);
        ksort($apiParams);
        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "${method}&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&",true));
        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http')."://{$domain}/";

        try {
            $content = $this->fetchContent($url, $method, "Signature={$signature}{$sortedQueryStringTmp}");
            return json_decode($content);
        } catch( \Exception $e) {
            return false;
        }
    }

    /**
     * @Notes: 处理数据
     * @Interface encode
     * @param $str
     * @return array|string|string[]|null
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/14   11:01 下午
     */
    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    /**
     * @Notes: 发送请求的CURL
     * @Interface fetchContent
     * @param $url
     * @param $method
     * @param $body
     * @return bool|mixed|string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/14   11:01 下午
     */
    private function fetchContent($url, $method, $body) {
        $ch = curl_init();

        if($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            $url .= '?'.$body;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));

        if(substr($url, 0,5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);
        if($rtn === false) {
            // 大多由设置等原因引起，一般无法保障后续逻辑正常执行，
            // 所以这里触发的是E_USER_ERROR，会终止脚本执行，无法被try...catch捕获，需要用户排查环境、网络等故障
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }



}