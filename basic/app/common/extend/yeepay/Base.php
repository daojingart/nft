<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/10/9   18:57
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\yeepay;

use app\common\model\WxSetting;
use think\Loader;

class Base
{
    protected $config; // 易宝支付配置
    protected $error;

    public function __construct()
    {
        Loader::import('Yeepay.lib.YopRsaClient', VENDOR_PATH,'.php');
        Loader::import('Yeepay.lib.YopClient3', VENDOR_PATH,'.php');
        Loader::import('Yeepay.lib.YopSignUtils', VENDOR_PATH,'.php');
        Loader::import('Yeepay.lib.Util.YopSignUtils', VENDOR_PATH,'.php');
        $yb_pay = WxSetting::detail();
        $this->config = [
            'appKey' => $yb_pay['yb_app_id'],
            'merchantNo' => $yb_pay['yb_merchantNo'],
            'private_key' => $yb_pay['yb_private_key'],
            'public_key' => $yb_pay['yb_public_key'],
            'poundage' => '0.65', //手续费
        ];
    }

    /**
     * @Notes: 记录日志
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doLogs($values,$dir_path='log')
    {
        $dir_path = RUNTIME_PATH.'/yeepay/'.$dir_path;
        return write_log($values,$dir_path);
    }

}