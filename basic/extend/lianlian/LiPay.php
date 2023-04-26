<?php

namespace lianlian;

use exception\BaseException;
use think\Env;

/**
 * 连连支付基类
 */
class LiPay
{
    protected $error;
    protected $config; // 连连支付配置
    protected $baseUrl; // 连连支付接口地址
    protected $basePageUrl; // 连连支付页面接口地址

    /**
     * @throws BaseException
     */
    public function __construct($initConfig)
    {
        if (empty($initConfig)) {
            throw new BaseException(['msg' => '请先配置连连支付参数', 'code' => -1]);
        }
        if($initConfig['open_status'] ==20 && $initConfig['open_purse_status'] == 20){
            throw new BaseException(['msg' => '支付通道关闭;禁止使用！', 'code' => -1]);
        }
        if(!$initConfig['app_id'] || !$initConfig['llp_rsa_private_key'] || !$initConfig['llp_rsa_public_key']){
            throw new BaseException(['msg' => '缺少支付参数,支付错误', 'code' => -1]);
        }
        $this->config = $initConfig;
        if (Env::get('app_debug')) {
            $this->baseUrl = "https://accpapi-ste.lianlianpay-inc.com";
            $this->basePageUrl = "https://accpgw-ste.lianlianpay-inc.com";
        } else {
            $this->baseUrl = "https://accpapi.lianlianpay.com";
            $this->basePageUrl = "https://accpgw.lianlianpay.com";
        }
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
    public function orderNo()
    {
        return 'account_'.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }
}