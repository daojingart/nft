<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/14   8:06 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 短信API
 * +----------------------------------------------------------------------
 */

namespace app\api\controller;

use app\common\model\Member;
use app\common\model\Setting;
use exception\BaseException;
use sms\Driver as SmsDriver;
use TencentCloud\Captcha\V20190722\CaptchaClient;
use TencentCloud\Captcha\V20190722\Models\DescribeCaptchaResultRequest;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use think\Config;
use think\Controller;
use think\Env;

/**
 * 公共接口
 */
class Sms extends \app\common\controller\Controller
{
    private $config;

    protected $noNeedLogin = [
        'sendSms','behaviorVerificationCode'
    ];
    /**
     * 构造方法
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        // 存储配置信息
        $this->config = Setting::getItem('sms');
        // 验证用户
    }

    /**
     * 发送短信
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @param string $phone  账号
     * @ApiRoute  (/api/Sms/sendSms)
     * @ApiParams (name="sendType", type="string", required=true, description="发送类型  register=注册  forget=忘记密码  login=登录 real_name=实名认证  reset=重置密码")
     * @ApiParams (name="phone", type="string", required=true, description="手机号")
     */
    public function sendSms()
    {
        $param = $this->request->param();
        if(Env::get('app_debug') == false){
            $redis = initRedis();
            if(!$redis->get(request()->ip().'sms')){ //行为验证码的验证
                $this->error("发送短信失败-非法请求未经过效验");
            }
        }
        if (!array_key_exists('sendType',$param)){
            $this->error("发送短信失败请传入发送类型");
        }
        if (!array_key_exists('phone',$param)){
            $this->error("发送短信失败请传入手机号");
        }
        $member_info = Member::where('phone',$param['phone'])->find();
        if (empty($member_info) && $param['sendType'] == "login"){
            $this->error("手机号不存在,请先注册！");
        }
        if (empty($member_info) && $param['sendType'] == "forget"){
            $this->error("手机号不存在,请先注册！");
        }
        if (!empty($member_info) && $param['sendType'] == "register"){
            $this->error("手机号已存在！");
        }
        $StorageDriver = new SmsDriver($this->config);
        if(!$StorageDriver->sendSms()){
            $this->error("发送短信失败-".$StorageDriver->getError());
        }
        $this->success("发送短信成功");
    }


    /**
     * 行为验证码检测
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @param string $scenes_type 类型
     * @param string $ticket 字符串
     * @param string $randstr 随机字符
     * @ApiRoute  (/api/Sms/behaviorVerificationCode)
     */
    public function behaviorVerificationCode()
    {
        $redis = initRedis();
        $param = $this->request->param();
        $ip = request()->ip();
        $values = Setting::getItem("behaviorcode");
        if(!isset($values['tx_verification_open']) || $values['tx_verification_open'] == 2){
            $redis->setex($ip.$param['scenes_type'], 10, "1");
            $this->success("验证成功");
        }
        if(!$param['ticket'] || !$param['randstr']){
            $this->error("业务调用失败!");
        }
        $resource = Setting::getItem("resource");
        if(!isset($resource['engine']['tx_oss']['access_key'])){
            $this->error("请先完成视频配置中信息填写！!");
        }
        try {
            $cred = new Credential($resource['engine']['tx_oss']['access_key'], $resource['engine']['tx_oss']['secret_key']);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("captcha.tencentcloudapi.com");
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new CaptchaClient($cred, "", $clientProfile);
            $req = new DescribeCaptchaResultRequest();
            $params = array(
                "CaptchaType" => 9,
                "Ticket" => $param['ticket'],
                "UserIp" => $ip,
                "Randstr" => $param['randstr'],
                "CaptchaAppId" => intval($values['appid']),
                "AppSecretKey" => $values['secretkey']
            );
            $req->fromJsonString(json_encode($params));
            $resp = $client->DescribeCaptchaResult($req);
            $result = json_decode($resp->toJsonString(),true);
            if($result['CaptchaCode'] !=1){
                $this->error("验证失败！请重新验证");
            }
            //存储redis 验证 使用过删除  redis  验证
            $redis->setex($ip.$param['scenes_type'], 10, "1");
            $this->success("验证成功");
        }catch(TencentCloudSDKException $e) {
            $this->error($e->getMessage());
        }
    }

}