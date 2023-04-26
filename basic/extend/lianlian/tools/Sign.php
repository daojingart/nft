<?php

namespace lianlian\tools;

use exception\BaseException;

/**
 * 连连支付签名类
 */
class Sign
{

    /**
     * 签名函数
     * @param $content
     * @return string
     * @Time: 2022/7/13   16:42
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface signData
     */
    public static function signData($content,$llp_rsa_private_key): string
    {
        $md5Sign = md5($content);
        $sign_data = self::sign($md5Sign,$llp_rsa_private_key);
        return base64_encode($sign_data);
    }

    /**
     * 签名函数
     * @param $content
     * @return string
     * @Time: 2022/7/13   16:42
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface signData
     */
    public function verifySignature($content,$sign_data,$llp_rsa_public_key): string
    {
        //然后利用公钥加密
        $stream = md5($content);
        return self::isValid($stream,$sign_data,$llp_rsa_public_key);
    }

    /**
     * 利用约定数据和私钥生成数字签名
     * @param $data 待签数据
     * @return string 返回签名
     */
    public static function sign($data = '',$private_key)
    {
        if (empty($data)) {
            return False;
        }
        if (empty($private_key)) {
            throw new BaseException(['msg' => 'Private Key error!', 'code' => -10]);
        }
        $verify = openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_MD5);
        return $signature;
    }

    /**
     * 利用公钥和数字签名以及约定数据验证合法性
     * @param $data 待验证数据
     * @param $signature 数字签名
     * @return -1:error验证错误 1:correct验证成功 0:incorrect验证失败
     */
    public static function isValid($data = '', $signature = '',$llp_rsa_public_key)
    {
        if (empty($data) || empty($signature)) {
            return False;
        }
        $pkeyid = openssl_get_publickey($llp_rsa_public_key);
        if (empty($pkeyid)) {
            throw new BaseException(['msg' => 'public key resource identifier False!', 'code' => -10]);
        }
        $ret = openssl_verify($data, base64_decode($signature), $pkeyid, OPENSSL_ALGO_MD5);
        switch ($ret) {
            case -1:
                throw new BaseException(['msg' => '签名错误', 'code' => 0]);
                break;
            default:
                throw new BaseException(['msg' => '未知错误', 'code' => 0]);
                break;
        }
        return $ret;
    }

}