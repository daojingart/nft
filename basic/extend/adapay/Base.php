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
 * | className: 汇付天下支付
 * +----------------------------------------------------------------------
 */

namespace adapay;

use app\common\model\WxSetting;
use exception\BaseException;


/**
 * 汇付Adapay 支付
 */
class Base
{
    protected $error;

    protected $config;

    public static $header = array('Content-Type:application/json');
    public static $headerText = array('Content-Type:text/html');

    protected $gateWayUrl = "https://api.adapay.tech";

    /**
     * 初始化配置信息
     * @param $initConfig
     * @throw
     */
    public function __construct($initConfig)
    {
        if (empty($initConfig)) {
            throw new BaseException(['msg' => '请先配置汇付通支付参数', 'code' => -1]);
        }
        if($initConfig['open_status'] ==20){
            throw new BaseException(['msg' => '支付通道关闭;禁止使用！', 'code' => -1]);
        }
        if(!$initConfig['app_id'] || !$initConfig['ad_APIKey'] || !$initConfig['rsaPrivateKey'] || !$initConfig['ad_rsaPublicKey']){
            throw new BaseException(['msg' => '缺少支付参数,支付错误', 'code' => -1]);
        }
        $this->config = [
            'app_id' => $initConfig['app_id'],
            'APIKey' => $initConfig['ad_APIKey'],
            'ad_rsaPrivateKey' => $initConfig['rsaPrivateKey'],
            'ad_rsaPublicKey' => $initConfig['ad_rsaPublicKey']
        ];
    }


}