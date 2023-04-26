<?php

namespace  adapay\tools;

/**
 * 签名类
 */
class Sign
{
    /**
     *  处理参数
     * @param $req_params
     * @return array
     * @Time: 2022/8/8   14:22
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface do_empty_data
     */
    public static function do_empty_data($req_params){
        $req_params = array_filter($req_params, function($v){
            if (!empty($v) || $v == '0') {
                return true;
            }
            return false;
        });
        return $req_params;
    }

    /**
     * 生成签名
     * @param $url
     * @param $params
     * @return string
     * @Time: 2022/8/10   15:37
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface generateSignature
     */
    public static function generateSignature($url, $params,$ad_rsaPrivateKey){
        if (is_array($params)){
            $Parameters = array();
            foreach ($params as $k => $v)
            {
                $Parameters[$k] = $v;
            }
            $data = $url . json_encode($Parameters);
        }else{
            $data = $url . $params;
        }
        $sign = self::SHA1withRSA($data,$ad_rsaPrivateKey);
        return $sign;
    }


    /**
     *  加密
     * @param $data
     * @return string
     * @Time: 2022/8/9   15:44
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface SHA1withRSA
     */
    public static function SHA1withRSA($data,$ad_rsaPrivateKey){
        $key = openssl_get_privatekey($ad_rsaPrivateKey);
        try {
            openssl_sign($data, $signature, $key);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return base64_encode($signature);
    }

    /***
     * 回调验证签名
     * @param $signature
     * @param $data
     * @return bool
     * @Time: 2022/8/9   15:43
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface verifySign
     */
    public static function verifySign($signature, $data,$ad_rsaPublicKey){
        $key = openssl_get_publickey($ad_rsaPublicKey);
        if (openssl_verify($data, base64_decode($signature), $key)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 循环参数判断是否为空
     * @param $value
     * @return bool
     * @Time: 2022/8/10   15:37
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface checkEmpty
     */
    public function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

    /**
     * 获取数组的字符串
     * @param $data
     * @param $key
     * @return string
     * @Time: 2022/8/10   15:37
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface get_array_value
     */
    public function get_array_value($data, $key){
        if (isset($data[$key])){
            return $data[$key];
        }
        return "";
    }

    /**
     * 生成请求的参数字符串
     * @param $params
     * @return false|string
     * @Time: 2022/8/10   15:37
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createLinkstring
     */
    function createLinkstring($params)
    {
        $arg = "";

        foreach ($params as $key=> $val){
            if($val){
                $arg .= $key . "=" . $val . "&";
            }
        }
        $arg = substr($arg,0, -1);
        return $arg;
    }

}