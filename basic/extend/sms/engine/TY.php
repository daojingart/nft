<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/14   8:27 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 腾域短信驱动服务类
 * +----------------------------------------------------------------------
 */

namespace sms\engine;


class TY extends Server
{
    private $config;
    private $smsConfig;

    public function __construct($config,$sms_config)
    {
        parent::__construct();
        $this->config = $config;
        $this->smsConfig = $sms_config;
    }

    /**
     * 发送短信
     * @return array|bool
     */
    public function sendSms()
    {
        return $this->bindRequestBody();
    }

    /**
     * @Notes: 封装请求体
     * @Interface bindRequestBody
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/14   9:34 下午
     */
    public function bindRequestBody()
    {
        $param = [
            'userName' => $this->config['access_key'],
            'password' => $this->config['secret_key'],
            'content' => $this->getTemplateId(),
            'phoneList' => [
                $this->phone
            ]
        ];
        $this->sms_safe($this->smsConfig);
        $result = $this->requestPost("http://send.it1688.com.cn:8001/sms/api/sendMessage",json_encode($param),true);
        $resultArray = json_decode($result,true);
        if($resultArray['code'] == '0'){
            return true;
        }
        $this->error = $resultArray['message'];
        return false;
    }

    /**
     * @Notes: 获取短信模板
     * @Interface getTemplateId
     * @return string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/14   9:59 下午
     */
    public function getTemplateId()
    {
        $template_arr = [
            'register' => "【{$this->config['signature']}】您的验证码为：%u，如非本人操作,请忽略本短信！",
        ];
        $template = $template_arr['register'];
        return  sprintf($template,$this->smsCode);
    }



}