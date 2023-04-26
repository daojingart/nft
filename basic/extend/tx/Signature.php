<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2021/12/5   21:22
 * +----------------------------------------------------------------------
 * | className: 签名
 * +----------------------------------------------------------------------
 */

namespace tx;

class Signature extends Base
{
    /**
     *  生成签名
     * @return false|string
     * @Time: 2021/12/5   21:26
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSignature
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getSignature()
    {
        try {
            // 确定签名的当前时间和失效时间
            $current = time();
            $expired = $current + 86400;  // 签名有效期：1天
            // 向参数列表填入参数
            $arg_list = array(
                "secretId" => $this->appid,
                "currentTimeStamp" => $current,
                "expireTime" => $expired,
                "random" => rand());
            // 计算签名
            $original = http_build_query($arg_list);
            $signature = base64_encode(hash_hmac('SHA1', $original, $this->appsecret, true).$original);
            return $signature;
        } catch (\Exception $e) {
            $this->errMsg = $e->getMessage();
            return false;
        }
    }

}