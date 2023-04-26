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
 * | className: 短信服务商引擎抽象类
 * +----------------------------------------------------------------------
 */

namespace sms\engine;

use app\admin\controller\setting\Cache;
use exception\BaseException;
use think\Exception;
use think\Request;

abstract class Server
{
    protected $phone;  //手机号
    protected $error;
    protected $smsCode;

    /**
     * 构造函数
     * Server constructor.
     * @throws Exception
     */
    protected function __construct()
    {
        //接收短信的手机号
        $this->phone = Request::instance()->param("phone");
        if (empty($this->phone)) {
            throw new BaseException(['msg' => "请先填写手机号"]);
        }
        // 生成发送短信的验证码
        $this->smsCode = $this->buildSendSms();
    }

    /**
     * 发送短信
     * @return mixed
     */
    abstract protected function sendSms();

    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @Notes: 生成短信验证码
     * @Interface buildSaveName
     * @return string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/14   8:17 下午
     */
    private function buildSendSms()
    {
        $code = rand(100000, 999999);
        $redis = initRedis();
        $redis->setex($this->phone.'code',300,$code);
//        Cache($this->phone, $code, 300); //5分钟内有效
        return $code;
    }

    /**
     * @Notes: 发送短信
     * @Interface request_post
     * @param string $url
     * @param $post_data
     * @return bool|string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/11   9:52 上午
     */
    public function requestPost($url = '', $post_data, $is_json = false)
    {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($is_json)
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($post_data)
                )
            );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }

    /**
     * 格式化参数格式化成url参数
     * @param $values
     * @return string
     */
    public function toUrlParams($values)
    {
        $buff = '';
        foreach ($values as $k => $v) {
            if ($k != 'sign' && $v != '' && !is_array($v)) {
                $buff .= $k . '=' . $v . '&';
            }
        }
        return trim($buff, '&');
    }

    /**
     * @Notes: 防止防止恶意攻击
     * @Interface sms_safe
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/14   11:36 下午
     */
    public function sms_safe($config)
    {
        $redis = initRedis();
        //两次间隔
        $sms_send_time = $config['sms_send_time']; //两次短信发送时间间隔
        //限制时间之内发送
        $sms_send_num = $config['sms_send_num'];  //最大发送次数
        //限制时间
        $sms_send_black_time = $config['sms_send_black_time'];  //限制发送时间
        //获取发送存在时间
        $phone_time = $redis->get($this->phone);
        if (!empty($phone_time)) {
            throw new BaseException(['msg' => '操作频繁,请稍后重试;请在1分钟后重试']);
        }
        //限制时间 的发送次数
        $redis->setex($this->phone, $sms_send_time,1);
        $phone_count = $redis->get($this->phone.'count');
        if ($phone_count > $sms_send_num) {
            throw new BaseException(['msg' => '发送次数超过限制']);
        }
        if (empty($phone_count)){
            $redis->setex($this->phone. 'count', $sms_send_black_time,1);
        }else{
            $redis->incr($this->phone . 'count');
        }
    }
}
