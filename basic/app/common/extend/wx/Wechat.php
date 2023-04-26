<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/12   4:42 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 微信公众号登录
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\wx;


use app\common\model\WxSetting;
use exception\BaseException;
use think\Cache;

class Wechat extends Wx
{
    /**
     * @Notes: 获取微信登录链接
     * @Interface getWxLoginUrl
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/19   6:08 下午
     */
    public static function getWxLoginUrl()
    {
        $state = time().mt_rand(1000,9999);
        $appInfo = WxSetting::detail();
        $redirect_uri = HOST."/h5/h5.html";
        $redirect_uri = urlEncode($redirect_uri);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appInfo['wx_app_id']}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
    }

    /**
     * 微信登录 根据Code 获取Token 和 open_id
     * @param $code
     * @return array|mixed
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public static function wxlogin($code)
    {
        // 获取当前登录应用的配置的信息
        $appInfo = WxSetting::detail();
        if (empty($appInfo['wx_app_id']) || empty($appInfo['wx_app_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-微信配置-微信公众号设置] 填写appid 和 appsecret']);
        }
        //根据code 获取Token 和OpenID
        $getTokenInfo = (new Wechat)->getAuthAccessToken($appInfo['wx_app_id'],$appInfo['wx_app_secret'],$code);
//        $getTokenInfo = [
//            'access_token' => '55_6_4ygFo_g8hosz09nQ7Z8kzLSq3HnXcezlgHlgs1wUSrqF1Azxf9lyH0bRmnOkb2ntoJqr4r6z39imuAD0i8ZA',
//            'openid' => 'otQKw0QSbQEh13dzDuDND3EU_jvc'
//        ];
        //根据access_token 和 OpenID 换取用户信息
        return (new Wechat)->getUserInfo($getTokenInfo['access_token'], $getTokenInfo['openid']);
    }


    /**
     * @Notes: 获取微信用户的信息
     * @Interface getUserInfo
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/12   5:09 下午
     */
    public function getUserInfo($access_token,$openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $result = self::curlRequest($url);
        $getMemberINfo = self::jsonDecode($result);
        return [
            'nickName' => $getMemberINfo['nickname'],
            'open_id' => $getMemberINfo['openid'],
            'avatarUrl' => $getMemberINfo['headimgurl'],
            'gender' => $getMemberINfo['sex'],
            'country' => $getMemberINfo['country'],
            'province' => $getMemberINfo['province'],
            'city' => $getMemberINfo['city'],
            'unionid' => isset($getMemberINfo['unionid'])?$getMemberINfo['unionid']:"",

        ];
    }



    /**
     * @Notes: 获取Token
     * @Interface getAuthAccessToken
     * @param $wx_app_id
     * @param $wx_app_secret
     * @param $code
     * @return mixed
     * @throws BaseException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/12   4:56 下午
     */
    protected function getAuthAccessToken($wx_app_id, $wx_app_secret, $code)
    {
        // 请求API获取 access_token
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$wx_app_id}&secret={$wx_app_secret}&code={$code}&grant_type=authorization_code";
        $result = self::curlRequest($url);
        $data = self::jsonDecode($result);
        if (array_key_exists('errcode', $data)) {
            throw new BaseException(['msg' => "access_token获取失败，错误信息：{$result}"]);
        }
        // 记录日志
        self::doLogs([
            'describe' => '获取access_token',
            'url' => $url,
            'appId' => $wx_app_id,
            'result' => $result
        ]);
        return ['access_token'=> $data['access_token'],'openid'=> $data['openid']];

    }

    /**
     * 微信登录 根据Code 获取Token 和 open_id
     * @param $code
     * @return array|mixed
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public static function wxPayLogin($code)
    {
        // 获取当前登录应用的配置的信息
        $appInfo = WxSetting::detail();
        if (empty($appInfo['wx_app_id']) || empty($appInfo['wx_app_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-微信配置-微信公众号设置] 填写appid 和 appsecret']);
        }
        //根据code 获取Token 和OpenID
        return (new Wechat)->getAuthAccessToken($appInfo['wx_app_id'],$appInfo['wx_app_secret'],$code);
    }

    /**
     * @Notes: 获取微信登录链接静默授权获取openId
     * @Interface getWxLoginUrl
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/19   6:08 下午
     */
    public static function getWxPayLoginUrl($redirect_uri)
    {
        $redirect_uri = HOST."/h5/h5.html#".$redirect_uri;
        $state = time().mt_rand(1000,9999);
        $appInfo = WxSetting::detail();
        $redirect_uri = urlEncode($redirect_uri);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appInfo['wx_app_id']}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
    }


}