<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/27   11:27 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 微信小程序登录配置
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\wx;


use app\common\model\WxSetting;
use exception\BaseException;

class WeApp extends Wx
{
    /**
     * 微信登录
     * @param $code
     * @return array|mixed
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public static function wxlogin($code)
    {
        // 获取当前小程序信息
        $wxapp = WxSetting::detail();
        if (empty($wxapp['wxapp_app_id']) || empty($wxapp['wxapp_app_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-微信配置-微信小程序设置] 填写appid 和 appsecret']);
        }
        // 微信登录 (获取session_key)
        if (!$session = (new WeApp())->sessionKey($wxapp['wxapp_app_id'],$wxapp['wxapp_app_secret'],$code)) {
            throw new BaseException(['msg' => (new WeApp())->getError()]);
        }
        return $session;
    }




    /**
     * @Notes: session_key
     * @Interface sessionKey
     * @param $wxapp_app_id
     * @param $wxapp_app_secret
     * @param $code
     * @return false|mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/27   11:41 上午
     */
    public function sessionKey($wxapp_app_id,$wxapp_app_secret,$code)
    {
        /**
         * code 换取 session_key
         * ​这是一个 HTTPS 接口，开发者服务器使用登录凭证 code 获取 session_key 和 openid。
         * 其中 session_key 是对用户数据进行加密签名的密钥。为了自身应用安全，session_key 不应该在网络上传输。
         */
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $result = $this->jsonDecode($this->curlRequest($url, [
            'appid' => $wxapp_app_id,
            'secret' => $wxapp_app_secret,
            'grant_type' => 'authorization_code',
            'js_code' => $code
        ]));
        if (isset($result['errcode'])) {
            $this->error = $result['errmsg'];
            return false;
        }
        return $result;
    }



    /**
     * @Notes: 解密绑定手机号
     * @Interface getPhoneNumber
     * @param $param
     * @return false|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/26   10:33 上午
     */
    public function getPhoneNumber($param){
        $wxapp = WxSetting::detail();
        return $this->decryptData($wxapp['wxapp_app_id'],$param['encryptedData'],$param['iv'],$param['session_key']);//微信用户信息
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData($appid, $encryptedData, $iv, $sessionKey)
    {
        if (strlen($sessionKey) != 24) {
            $this->error = "抱歉字段长度错误";
            return false;
        }
        $aesKey=base64_decode($sessionKey);
        if (strlen($iv) != 24) {
            $this->error = "抱歉字段长度错误";
            return false;
        }
        $aesIV=base64_decode($iv);
        $aesCipher=base64_decode($encryptedData);
        $result= openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode( $result );
        if( $dataObj  == NULL )
        {
            $this->error = "数据对象为空";
            return false;
        }
        if( $dataObj->watermark->appid != $appid)
        {
            $this->error = "APPID 错误";
            return false;
        }
        return json_decode($result,true);
    }

    /**
     * @Notes: 获取错误信息
     * @Interface getError
     * @return mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/27   11:42 上午
     */
    public function getError()
    {
        return $this->error;
    }

}