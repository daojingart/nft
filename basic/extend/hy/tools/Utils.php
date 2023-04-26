<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/8   14:03
 * +----------------------------------------------------------------------
 * | className: 汇元银联支付 工具
 * +----------------------------------------------------------------------
 */

namespace hy\tools;


class Utils
{
    //region 获取时间字符串
    /**
     * 获取时间字符串 20201031095743
     * @return false|string
     */
    static public function getTimestampString()
    {
        //获取时间字符串
        date_default_timezone_set('PRC');
        $timestamp = date("YmdHis");
        return $timestamp;
    }


    /**
     * 3des加密
     * @param $encryptParams 需要加密的参数集合
     * @param $desKey 3des加密key
     */
    static public function des3Encrypt($encryptParams, $desKey)
    {
        //1. 拼接字符串
        $stringToBeEncrypt = "";
        $i = 0;
        foreach ($encryptParams as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                if ($i == 0) {
                    $stringToBeEncrypt .= "$k" . "=" . "$v";
                } else {
                    $stringToBeEncrypt .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        //2. 加密
        $result = openssl_encrypt($stringToBeEncrypt, "DES-EDE3", $desKey);
        //3. 默认加密后为base64字符串，需要转成hex字符串。
        $result = self::toHexString(base64_decode($result));
        return strtoupper($result);
    }

    /**
     * 3des解密
     * @param $decryptParams   汇元接口返回加密的16进制字符串。
     * @param $desKey
     */
    static public function des3Decrypt($encryptHexString, $desKey)
    {
        //1. 对16进制字符串转base64 字符串
        $base64String = base64_encode(hex2bin($encryptHexString));
        //2. 对base64字符串进行解密。
        return openssl_decrypt($base64String,"DES-EDE3",$desKey);
    }

    /**
     * 转16进制字符串
     * @param $string
     * @return string
     */
    static private function toHexString($string)
    {
        $buf = "";
        $max_length  = strlen($string);
        for ($i = 0; $i < $max_length ; $i++) {
            $val = dechex(ord($string{$i}));
            if (strlen($val) < 2)
                $val = "0" . $val;
            $buf .= $val;
        }
        return $buf;
    }


    //endregion

    //region 签名
    /**
     * md5签名
     * @return string
     */
    static public function generaterSign($params, $key)
    {
        $rawString = self::getSignContent($params);
        return strtoupper(md5($rawString . "&key=" . $key));
    }

    /**
     * 排序签名参数，组织签名字符串。
     * @param $params
     * @return string
     */
    static public function getSignContent($bizParams)
    {
        ksort($bizParams);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($bizParams as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }


    /**
     * 发行请求URL
     * @param $method
     * @param $jsonStr
     * @return array
     * @Time: 2022/8/19   10:59
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface sendRequest
     */
    static public function sendRequest($gateWayUrl,$method, $jsonStr)
    {
        $url = $gateWayUrl. $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }

    /**
     * 生成订单号
     */
    public static function orderNo()
    {
        return 'hy'.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }

}


