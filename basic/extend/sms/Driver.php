<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 自定义短信基类
 * +----------------------------------------------------------------------
 */

namespace sms;

use exception\BaseException;
use think\Exception;

class Driver
{
    private $config;    // 短信 配置
    private $engine;    // 当前短信引擎类
    private $sms_Config;    // 当前短信引擎类

    /**
     * 构造方法
     * Driver constructor.
     * @param $config
     * @throws Exception
     */
    public function __construct($config)
    {
        $sms_Config['sms_send_time'] = '60';  //两次短信发送时间间隔 单位：秒
        $sms_Config['sms_send_num'] = '10';    //最大发送次数
        $sms_Config['sms_send_black_time'] = '600';    //限制发送时间 单位：秒 (一小时为 60*60 = 3600 秒)
        $this->config = $config;
        $this->sms_Config = $sms_Config;
        // 实例化当前存储引擎
        $this->engine = $this->getEngineClass();
    }

    /**
     * 执行发送短信
     */
    public function sendSms()
    {
        return $this->engine->sendSms();
    }


    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->engine->getError();
    }

    /**
     * 获取当前的短信引擎
     * @return mixed
     * @throws Exception
     */
    private function getEngineClass()
    {
        $engineName = $this->config['default'];
        $classSpace = __NAMESPACE__ . '\\engine\\' . ucfirst($engineName);
        if (!class_exists($classSpace)) {
            throw new BaseException(['msg' => '未找到短信引擎类: ' . $engineName]);
        }
        $config = isset($this->config['engine'][$engineName]) ? $this->config['engine'][$engineName] : [];
        return new $classSpace($config,$this->sms_Config);
    }
}
