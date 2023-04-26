<?php

namespace  lianlian\tools;

use pay\lianlianpay\tools\指定URL完整路径地址;
use pay\lianlianpay\tools\请求的数据;

/**
 * 连连支付http请求类
 */
class Http
{
    /**
     * 远程获取数据，POST模式
     * 注意：
     * @param $url 指定URL完整路径地址
     * @param $para 请求的数据
     * return 远程输出的数据
     */
    public static function getHttpResponseJSON($url, $data,$sign)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if(!$data){
            return 'data is null';
        }
        if(is_array($data))
        {
            $data = json_encode($data);
        }
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER,array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:' . strlen($data),
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Signature-Data:'.$sign,
            'timestamp:'.date("YmdHis"),
            'Signature-Type:RSA'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

}