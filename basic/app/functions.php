<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/11/20   12:59
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 扩展函数实现
 * +----------------------------------------------------------------------
 */

use exception\BaseException;

if (!function_exists('array_column')) {
    /**
     * array_column 兼容低版本php
     * (PHP < 5.5.0)
     * @param $array
     * @param $columnKey
     * @param null $indexKey
     * @return array
     */
    function array_column($array, $columnKey, $indexKey = null)
    {
        $result = array();
        foreach ($array as $subArray) {
            if (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
                $result[] = is_object($subArray) ? $subArray->$columnKey : $subArray[$columnKey];
            } elseif (array_key_exists($indexKey, $subArray)) {
                if (is_null($columnKey)) {
                    $index = is_object($subArray) ? $subArray->$indexKey : $subArray[$indexKey];
                    $result[$index] = $subArray;
                } elseif (array_key_exists($columnKey, $subArray)) {
                    $index = is_object($subArray) ? $subArray->$indexKey : $subArray[$indexKey];
                    $result[$index] = is_object($subArray) ? $subArray->$columnKey : $subArray[$columnKey];
                }
            }
        }
        return $result;
    }
}

if (!function_exists('BlError')) {
    /**
     * [Error description]
     * @param string $value [description]
     */
    function BlError($code = 403, $msg = '系统错误')
    {
        throw new BaseException(['msg' => $msg,'code' => $code]);
    }
}

if (!function_exists('load_wechat')) {
    /**
     * 获取微信操作对象（单例模式）
     * @staticvar array $wechat 静态对象缓存对象
     * @param type $type 接口名称 ( Card|Custom|Device|Extend|Media|Oauth|Pay|Receive|Script|User )
     * @return \Wehcat\WechatReceive 返回接口对接
     */
    function &load_wechat($type = '', $options = [])
    {
        static $wechat = array();
        $index         = md5(strtolower($type));
        if (!isset($wechat[$index])) {
            // 定义微信公众号配置参数（这里是可以从数据库读取的哦）
            $options = array_merge(array(
                'token'          => '', // 填写你设定的key
                'appid'          => '', // 填写高级调用功能的app id, 请在微信开发模式后台查询
                'appsecret'      => '', // 填写高级调用功能的密钥
                'encodingaeskey' => '', // 填写加密用的EncodingAESKey（可选，接口传输选择加密时必需）
                'mch_id'         => '', // 微信支付，商户ID（可选）
                'partnerkey'     => '', // 微信支付，密钥（可选）
                'ssl_cer'        => '', // 微信支付，双向证书（可选，操作退款或打款时必需）
                'ssl_key'        => '', // 微信支付，双向证书（可选，操作退款或打款时必需）
                'cache_path'      => '', // 设置SDK缓存目录（可选，默认位置在Wechat/Cache下，请保证写权限）
            ), $options);
            wechat\Loader::config($options);
            $wechat[$index] = wechat\Loader::get($type);
        }
        return $wechat[$index];
    }
}

if (!function_exists('getSignature')) {
    /**
     * 腾讯云生成签名
     * @param $options
     * @return false|string
     * @Time: 2021/12/8   10:49
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSignature
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    function getSignature($options = [])
    {
        $Signature = new \tx\Signature($options);
        return $Signature->getSignature();
    }
}

if (!function_exists('replacePhone')) {
    /**
     * 腾讯云生成签名
     * @param $options
     * @return false|string
     * @Time: 2021/12/8   10:49
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSignature
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    function replacePhone($phone = null)
    {
        return preg_replace("/^(\d{3})(\d{4})(\d{4})$/", "$1****$3",$phone);
    }
}


