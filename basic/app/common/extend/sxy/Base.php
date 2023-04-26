<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/7/8   14:13
 * +----------------------------------------------------------------------
 * | className: 支付的基础信息
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\sxy;

use app\common\model\WxSetting;
use exception\BaseException;


class Base
{
    protected $error;
    protected $config; // 汇付天下支付配置

    public function __construct() {
        $llp_pay = WxSetting::detail();
        $this->config = [
            'sxy_merchant_id' => $llp_pay['sxy_merchant_id'],
            'sxy_partner_id' => $llp_pay['sxy_partner_id'],
            'sxy_rsa_private_key' => $llp_pay['sxy_rsa_private_key'],
            'sxy_rsa_public_key' => $llp_pay['sxy_rsa_public_key']
        ];
    }

    /**
     * 签名函数
     * @param $content
     * @return string
     * @Time: 2022/7/13   16:42
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface signData
     */
    public function signData($content)
    {
        return self::sign($content);
    }


    /**
     * 利用约定数据和私钥生成数字签名
     * @param $data 待签数据
     * @return string 返回签名
     */
    public static function sign($json_arr)
    {
        $data = array();
        foreach($json_arr as $k=>$var){
            if(is_scalar($var) && $var !== '' && $var !== null){ //如果给出的变量参数 var 是一个标量，is_scalar() 返回 TRUE，否则返回 FALSE。标量变量是指那些包含了 integer、float、string 或 boolean的变量，而 array、object 和 resource 则不是标量。
                $data[$k] = $var;
            }else if(is_object($var)){
                $data[$k] =array_filter((array) $var);
            }else if(is_array($var)){
                $data[$k] =array_filter($var);
            }
            if(empty($data[$k])){
                unset($data[$k]);
            }
        }//foreach -end
        ksort($data); //按照 键名 对关联数组进行升序排序：
        $hmacSource = '';
        foreach($data as $key => $value){
            if (is_array($value)) {
                ksort($value);
                foreach ($value as $key2 => $value2) {

                    if (is_object($value2)) {
                        $value2 = array_filter((array)$value2);
                        ksort($value2);
                        foreach ($value2 as $oKey => $oValue) {
                            $oValue .= '#';
                            $hmacSource .= trim($oValue);

                        }
                    } else if(is_array($value2)){
                        ksort($value2);
                        foreach ($value2 as $key3 => $value3) {
                            if (is_object($value3)) {
                                $value3 = array_filter((array)$value3);
                                ksort($value3);
                                foreach ($value3 as $oKey => $oValue) {
                                    $oValue .= '#';
                                    $hmacSource .= trim($oValue);
                                }
                            } else{
                                $value3 .= '#';
                                $hmacSource .= trim($value3);
                            }
                        }
                    } else{
                        $value2 .= '#';
                        $hmacSource .= trim($value2);
                    }
                }
            } else {
                $value .= '#';
                $hmacSource .= trim($value);
            }
        }

        $sha1mac=sha1($hmacSource,true); //SHA1加密
        $pubKey = file_get_contents(__DIR__.'/tianyuansiyao.pfx');//私钥签名

        $results=array();
        $worked=openssl_pkcs12_read($pubKey,$results,'123456');
        pre($results);

        $rs =openssl_sign($sha1mac,$hmac,$results['pkey'],"md5");
        $hmac=base64_encode($hmac);
        $hmacarr=array();
        $hmacarr["hmac"]=$hmac;

        $arr_t=(array_merge($json_arr,$hmacarr)); //合并数组
        $json_str=json_encode($arr_t,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);  //将数组转成JSON

        /*
         * 生成16位随机数（AES秘钥）AES加密JSON数据串
         */
        $str1='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $randStr = str_shuffle($str1);//打乱字符串
        $rands= substr($randStr,0,16);//16-bit random AES key
        $screct_key = $rands;
        $str = trim($json_str);
        $str = addPKCS7Padding($str);


        $encrypt_str= openssl_encrypt($str, 'AES-128-ECB', $screct_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);
        $data = base64_encode($encrypt_str);

        $verifyKey4Server = file_get_contents(__DIR__.'/tianyuan.cer');  //公钥加密AES
        $pem = chunk_split(base64_encode($verifyKey4Server),64,"\n");//转换为pem格式的公钥
        $public_key = "-----BEGIN CERTIFICATE-----\n".$pem."-----END CERTIFICATE-----\n";
        $pu_key =  openssl_pkey_get_public($public_key);//这个函数可用来判断公钥是否是可用的
        openssl_public_encrypt($rands,$encryptKey,$pu_key); //公钥加密
        $encryptKey = base64_encode($encryptKey);
        return [
            'data' => $data,
            'encryptKey' => $encryptKey
        ];
    }


    /**
     * PHP 7.0以上 AES填充方法
     * @param $string
     * @param $blocksize
     * @return string
     * @Time: 2022/7/18   11:02
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface addPKCS7Padding
     */
    public function addPKCS7Padding($string, $blocksize = 16) {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;

    }





    /**
     * 远程获取数据，POST模式
     * @param $url 指定URL完整路径地址
     * @param $para 请求的数据
     * return 远程输出的数据
     */
    public function getHttpResponse($url,$data,$order_id)
    {
        $data_array = self::signData($data);

        self::doLogs(['log_title'=>$url."请求内容",'content' => $data_array]);
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_HEADER, 1 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_HTTPHEADER,array(
            'Content-Type: application/vnd.5upay-v3.0+json',
            'encryptKey: '.$data_array['encryptKey'],
            'merchantId: '.'896709587',
            'requestId: '.$order_id
        ));
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl,CURLOPT_POST,true); // post传输数据
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data_array['data']);// post传输数据
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        $responseText = curl_exec($curl);
        if (curl_errno($curl) || $responseText === false) {
            curl_close($curl);
            self::doLogs(['log_title'=>'请求错误','content' => $data_array]);
        }
        curl_close($curl);
        self::doLogs(['log_title'=>$url."返回内容",'content' => $responseText]);
        return $responseText;
    }


    /**
     * @Notes: 记录日志
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doLogs($values,$dir_path='paylog')
    {
        $dir_path = RUNTIME_PATH.'/sxy/'.$dir_path;
        return write_log($values,$dir_path);
    }

}