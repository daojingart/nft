<?php

namespace  sd\tools;


/**
 * 支付http请求类
 */
class Http
{
    /**
     * 发送请求
     * @param $url
     * @param $param
     * @return bool|mixed
     * @throws Exception
     */
    public static function http_post_json($url, $param)
    {
        if (empty($url) || empty($param)) {
            return false;
        }
        $param = json_encode($param);
        try {
            $ch = curl_init();//初始化curl
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //正式环境时解开注释
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $data = curl_exec($ch);//运行curl
            curl_close($ch);
            if (!$data) {
                throw new \Exception('请求出错');
            }
            return $data;
        } catch (\Exception $e) {
            throw $e;
        }

    }
}