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
 * | className: 汇元银联支付
 * +----------------------------------------------------------------------
 */

namespace hy;

use exception\BaseException;
use hy\tools\Utils;

/**
 * 汇元银通快捷支付
 */
class Base
{
    protected $error;

    protected $config; //汇元银通配置信息

    public static $header = array('Content-Type:application/json');

//    protected static $gateWayUrl = "https://demo.heemoney.com/YunBiz/Wallet/"; //正式域名

    protected static $gateWayUrl = "https://demo.heemoney.com/YunBiz/Wallet/"; //正式域名

    //钱包登录注册
    protected $LoginWallet = "v1/LoginWallet";

    //发送支付交易
    protected $ApplyPay = "v1/ApplyPay";
    //查询账户信息
    protected $GetUserInfo = "v1/GetUserInfo";
    //账户注销
    protected $CancelUser = "v1/CancelUser";
    //申请分润
    protected $ApplyPayShare = "v1/ApplyPayShare";

    protected $ShareFullRefund = "v1/ShareFullRefund";

    protected $BillInfo = "v1/BillInfo";

    //新快捷页面绑卡
    protected $HeepayAuthSend = "v1/HeepayAuthPageSign";

    //新快捷绑卡查询
    protected $HeepayAuthPageSignQuery = "v1/HeepayAuthPageSignQuery";

    //交易确认
    protected $ConfirmTrade = "V1/ConfirmPay";

    protected $publicRequest;

    /**
     * 初始化配置信息
     */
    public function __construct($initConfig)
    {
        if (empty($initConfig)) {
            throw new BaseException(['msg' => '请先配置汇元支付', 'code' => -1]);
        }
        if($initConfig['open_status'] ==20 && $initConfig['open_purse_status'] ==20){
            throw new BaseException(['msg' => '支付通道关闭;禁止使用！', 'code' => -1]);
        }
        if(!$initConfig['hy_app_id'] || !$initConfig['hy_md5Key'] || !$initConfig['hy_des3Key'] || !$initConfig['hy_owerId']){
            throw new BaseException(['msg' => '缺少支付参数,支付错误', 'code' => -1]);
        }
        $this->config = [
            'app_id' => $initConfig['hy_app_id'],
            'md5Key' => $initConfig['hy_md5Key'],
            'des3Key' => $initConfig['hy_des3Key'],
            'owerId' => $initConfig['hy_owerId']
        ];
        //公共请求信息
        $this->publicRequest = [
            "version" => '1.0',
            "app_id" => $initConfig['hy_app_id'],
            "charset" => 'utf-8',
            "owner_uid" => $initConfig['hy_owerId'],
            "sign_type" => 'MD5',
            "timestamp" => Utils::getTimestampString(),
        ];
    }

    /**
     * 获取错误的提示
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function getError()
    {
        return $this->error;
    }


}