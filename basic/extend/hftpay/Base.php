<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/8   14:03
 * +----------------------------------------------------------------------
 * | className: 汇付钱包
 * +----------------------------------------------------------------------
 */

namespace hftpay;


use exception\BaseException;
use think\Env;

class Base
{
    protected static $error;

    protected  $config; //汇付通的配置信息

    public static $header = array('Content-Type:application/json');
    public static $headerText = array('Content-Type:text/html');

    public static $sign_param = [
        'pfx_file_name' => '/www/server/tomcat/conf/HF0391.pfx',
        'pfx_file_pwd' => '123456'
    ];
    public static $sign_verifySign = [
        'cert_file' => '/www/server/tomcat/conf/CFCA_ACS_OCA31.cer',
    ];

    public static $sign_url = '';


    //环境API  根据不同环境切换不同的URL
    protected $gateWayUrl;

    /**
     * 初始化配置信息
     * @throw BaseException
     */
    public function __construct($initConfig)
    {
        if (empty($initConfig)) {
            throw new BaseException(['msg' => '请先配置汇付通支付参数', 'code' => -1]);
        }
        if($initConfig['open_purse_status'] ==20){
            throw new BaseException(['msg' => '支付通道关闭;禁止使用！', 'code' => -1]);
        }
        if(!$initConfig['hf_custid'] || !$initConfig['hf_mer_cust_id'] || !$initConfig['hf_divacctId']){
            throw new BaseException(['msg' => '缺少支付参数,支付错误', 'code' => -1]);
        }
        $this->config = [
            'mer_cust_id' => $initConfig['hf_mer_cust_id'],
            'custid' => $initConfig['hf_custid'],
            'hf_divacctId' => $initConfig['hf_divacctId'],
        ];
        self::$sign_param = [
            'pfx_file_name' => $initConfig['privatePfx_path'],
            'pfx_file_pwd' => $initConfig['privatePfxPwd']
        ];
        self::$sign_verifySign = [
            'cert_file' => $initConfig['publicKey_path'],
        ];
        if (Env::get('app_debug')) {
            self::$sign_url = "http://192.168.64.9:8080";
            $this->gateWayUrl = "https://hfpay.testpnr.com/api";
        }else{
            self::$sign_url = $_SERVER['SERVER_ADDR'];
            $this->gateWayUrl = "https://hfpay.cloudpnr.com/api";
        }

    }


    /**
     * 获取错误的信息
     * @ApiAuthor [Mr.Zhang]
     */
    public function getError()
    {
        return self::$error;
    }
    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }


}