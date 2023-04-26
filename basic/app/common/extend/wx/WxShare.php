<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/25   2:30 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 公众号分享相关信息
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\wx;


use app\common\model\WxSetting;
use exception\BaseException;
use think\Cache;

class WxShare extends Wx
{
    /**
     * 微信信息相关js接口(版本切换中某个版会用到)
     * @catalog  v2/微信信息相关js接口
     * @title    微信信息相关js接口
     * @description 微信信息相关js接口
     * @method  POST
     * @url     v2/wxcode/index
     * @return {"code":"1","data":{"appId":"wx82fe4a07e95c4cb0","nonceStr":"IgO1N7DLCT7YxBKH","timestamp":1573108343,"url":"http:\/\/video6.86itn.cn\/h5.html","signature":"f733c06940c55d7e84e1508cf94219ed6466b323","rawString":"jsapi_ticket=LIKLckvwlJT9cWIhEQTwfCz5ssfmF0EwF1exfRokUmij4pIIXSEOLso8Xk-w0kJQoSHs4NK4l8Bh2rH56cLxtg&noncestr=IgO1N7DLCT7YxBKH&timestamp=1573108343&url=http:\/\/video6.86itn.cn\/h5.html","JsApiTicket":"LIKLckvwlJT9cWIhEQTwfCz5ssfmF0EwF1exfRokUmij4pIIXSEOLso8Xk-w0kJQoSHs4NK4l8Bh2rH56cLxtg"},"msg":"ok"}
     * @return_param appId string 微信的唯一标识APPID
     * @return_param nonceStr string 签名跟随的字符串
     * @return_param timestamp string 生成签名的时间戳
     * @return_param url string 当前的url
     * @return_param signature string 签名
     * @return_param rawString string 分类名称
     * @return_param JsApiTicket string
     * @remark
     * @number
     */
    public function getShareInfo($url)
    {
        $appInfo = WxSetting::detail();
        $token = (new WeTemplate())->getAccessToken($appInfo['wx_app_id'],$appInfo['wx_app_secret']);
        $jsapiTicket = $this->getJsApiTicket($appInfo['wx_app_id'],$token['access_token']);
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        $string = "jsapi_ticket={$jsapiTicket['ticket']}&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        return [
            "appId" => $appInfo['wx_app_id'],
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string,
            "JsApiTicket" =>$jsapiTicket['ticket']
        ];
    }

    /**
     * @Notes: 用户第三方配置
     * @Interface getWxDetails
     * @return WxSetting|null
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/29   2:09 上午
     */
    public function getWxDetails()
    {
         return WxSetting::detail();
    }

    /**
     * @Notes: 随机字符串
     * @Interface createNonceStr
     * @param int $length
     * @return string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/25   2:41 下午
     */
    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * @Notes: getJsApiTicket
     * @Interface getJsApiTicket
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/25   2:34 下午
     */
    public function getJsApiTicket($wx_app_id,$access_token)
    {
        $cacheKey = $wx_app_id. 'gzh@JsApiTicket';
        if (!Cache::get($cacheKey)) {
            // 请求API获取 access_token
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
            $result = self::curlRequest($url);
            $data = self::jsonDecode($result);
            if (array_key_exists('errcode', $data)) {
                if($data['errcode'] != '0'){
                    self::doLogs([
                        'describe' => '获取JsApiTicket失败',
                        'url' => $url,
                        'appId' => $wx_app_id,
                        'result' => $result
                    ]);
                    throw new BaseException(['msg' => "JsApiTicket获取失败，错误信息：{$result}"]);
                }
            }
            // 记录日志
            self::doLogs([
                'describe' => '获取JsApiTicket',
                'url' => $url,
                'appId' => $wx_app_id,
                'result' => $result
            ]);
            Cache::set($cacheKey,['ticket'=> $data['ticket']], 6000);    // 7000
        }
        return Cache::get($cacheKey);
    }

}