<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/8   11:30 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 微信APP登录
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\wx;


use app\common\model\WxSetting;
use exception\BaseException;

class AppLogin extends Wx
{
    /**
     * @Notes: APP授权微信登录
     * @Interface getCode
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/8   11:32 上午\
     */
    public function getCode($code)
    {
        $appInfo = WxSetting::detail();
        if (empty($appInfo['wxapp_id']) || empty($appInfo['wxapp_sercret'])) {
            throw new BaseException(['msg' => '请到 [后台-微信配置-APP设置] 填写appid 和 appsecret']);
        }
        //根据CODE获取TOKEN和OPENID
        $getTokenInfo =  $this->getAuthAccessToken($code);
        //获取用户详细信息
        return $this->getUserInfo($getTokenInfo['access_token'], $getTokenInfo['openid']);
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
    protected function getAuthAccessToken($code)
    {
        // 请求API获取 access_token
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->config['wxapp_id']}&secret={$this->config['wxapp_sercret']}&code={$code}&grant_type=authorization_code";
        $result = self::curlRequest($url);
        $data = self::jsonDecode($result);
        if (array_key_exists('errcode', $data)) {
            self::doLogs([
                'describe' => '获取APP授权登录access_token失败',
                'url' => $url,
                'appId' => $this->config['wxapp_id'],
                'result' => $result
            ]);
            throw new BaseException(['msg' => "access_token获取失败，错误信息：{$result}"]);
        }
        // 记录日志
        self::doLogs([
            'describe' => '获取APP授权登录access_token',
            'url' => $url,
            'appId' => $this->config['wxapp_id'],
            'result' => $result
        ]);
        return ['access_token'=> $data['access_token'],'openid'=> $data['openid']];

    }

}