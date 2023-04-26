<?php

namespace sd;

use exception\BaseException;
use think\Env;

class SdPay
{
    protected $error;

    protected $config; // 杉德配置信息

    protected $baseUrl; // 云账户的接口

    protected $quicktopupUrl; // 快捷的接口

    protected $prefix = "sd_"; // 杉德支付的配置

    /**
     * @throws BaseException
     */
    public function __construct($initConfig)
    {
        if (empty($initConfig)) {
            throw new BaseException(['msg' => '请先配置杉德支付参数', 'code' => -1]);
        }
        if($initConfig['open_status'] ==20 && $initConfig['open_purse_status'] == 20){
            throw new BaseException(['msg' => '支付通道关闭;禁止使用！', 'code' => -1]);
        }
        if(!$initConfig['merchant_id'] || !$initConfig['privatePfx_path'] || !$initConfig['privatePfxPwd'] || !$initConfig['publicKey_path']){
            throw new BaseException(['msg' => '缺少支付参数,支付错误', 'code' => -1]);
        }
        $this->config = $initConfig;
        $this->baseUrl = "https://faspay-oss.sandpay.com.cn/pay/h5/cloud?"; //云账户正式
        // $this->baseUrl = "https://sandcash-uat01.sand.com.cn/pay/h5/cloud?"; //云账户测试
        $this->quicktopupUrl = "https://sandcash.mixienet.com.cn/pay/h5/quicktopup?"; //快捷正式
        // $this->quicktopupUrl = "https://sandcash-uat01.sand.com.cn/pay/h5/quicktopup?"; //快捷测试
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 获取时间戳
     */
    public function getTimestamp()
    {
        return date("YmdHis");
    }

    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return 'sdPay_'.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }
}