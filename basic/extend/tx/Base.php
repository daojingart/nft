<?php
namespace tx;
use wechat\Loader;

/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2021/12/5   21:18
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */
class Base
{

    public $appid;
    public $appsecret;
    public $errCode = 1;
    public $errMsg  = "";

    /** VODAPI接口URL需要使用此前缀 */
    const API_FUNCTION_DESCRIBEMEDIAINfOS = 'vod.tencentcloudapi.com';
    /** LIVE 直播 */
    const API_FUNCTION_DESCRIBELIVEDOMAINS = 'live.tencentcloudapi.com';

    //音频转码的模板的ID
    const Transcoding_mp3 = 1010;
    //视频转码的模板的ID
    const Transcoding_mp4 = 100030;

    /**
     * 构造方法
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->appid         = isset($options['access_key']) ? $options['access_key'] : '';
        $this->appsecret     = isset($options['secret_key']) ? $options['secret_key'] : '';
    }


    /**
     * @Notes: 当前当前错误代码
     * @Interface getErrorCode
     * @return int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/11/19   00:35
     */
    public function getErrorCode()
    {
        return $this->errCode;
    }


    /**
     * @Notes: 获取当前错误内容
     * @Interface getError
     * @return string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/11/19   00:35
     */
    public function getError()
    {
        return $this->errMsg;
    }

    /**
     * @Notes: 获取当前操作的腾讯APPID
     * @Interface getAppid
     * @return mixed|string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/11/19   00:36
     */
    public function getAppid()
    {
        return $this->appid;
    }

    /**
     * @Notes: 记录日志错误日志的
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doLogs($values)
    {
        return write_log($values,RUNTIME_PATH.'/tengxun/error');
    }

    /**
     * 错误码列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface switchError
     * @Time: 2021/12/18   17:37
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function switchError($code)
    {
        switch ($code)
        {
            case "AuthFailure.SignatureFailure":
                return "请检查是否配置云点播信息[检查路径：店铺管理->视频存储]是否配置；签名错误。签名计算错误，请对照调用方式中的签名方法文档检查签名计算过程。";
            case "ActionOffline":
                return "接口已下线。请联系河南八六互联开发者";
            case "AuthFailure.InvalidSecretId":
                return "密钥非法（不是云 API 密钥类型）。";
            case "AuthFailure.MFAFailure":
                return "MFA 错误。具体联系腾讯云客服";
            case "AuthFailure.SecretIdNotFound":
                return "密钥不存在。请在控制台检查密钥是否已被删除或者禁用，如状态正常，请检查密钥是否填写正确，注意前后不得有空格。";
            case "AuthFailure.SignatureExpire":
                return "签名过期。Timestamp 和服务器时间相差不得超过五分钟，请检查本地时间是否和标准时间同步。";
            case "IpInBlacklist":
                return "IP地址在黑名单中。请联系腾讯云客服";
            case "RequestLimitExceeded":
                return "请求的次数超过了频率限制。";
            case "ResourceInsufficient":
                return "资源不足。";
            case "ResourceNotFound":
                return "资源不存在。";
            case "ResourceUnavailable":
                return "资源不可用。";
        }
    }


}