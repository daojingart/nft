<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/7/9   14:35
 * +----------------------------------------------------------------------
 * | className: 支付宝打款
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\alipay;

use app\common\model\WxSetting;
use exception\BaseException;

class MakePayment
{
    private $config; // 微信支付配置

    /**
     * 构造方法
     * WxPay constructor.
     * @param $config
     */
    public function __construct()
    {
        $this->config = WxSetting::detail();
    }

    /**
     * 企业打款
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface payment
     * @Time: 2022/7/9   14:43
     */
    public function payment($zfb_account,$id,$actual_amount)
    {
        $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams();
        $params->appID = $this->config['alipay_app_id'];
        $params->appPublicKey = $this->config['alipay_public_key'];
        $params->appPrivateKey = $this->config['alipay_private_key'];
//        $params->apiDomain = 'https://openapi.alipaydev.com/gateway.do'; // 设为沙箱环境，如正式环境请把这行注释
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
        // 支付接口
        $request = new \Yurun\PaySDK\AlipayApp\Fund\Transfer\Request();
        $request->businessParams->out_biz_no = 'payAli' . mt_rand(10000000, 99999999).$id;
        $request->businessParams->payee_type = 'ALIPAY_LOGONID';
        $request->businessParams->payee_account = $zfb_account;
        $request->businessParams->amount = $actual_amount;
        // 调用接口
        $result = $pay->execute($request);
        self::doLogs($result);
        if($result['alipay_fund_trans_toaccount_transfer_response']['code'] !='10000'){
            throw new BaseException(['msg' => $result['alipay_fund_trans_toaccount_transfer_response']['sub_msg']]);
        }
        return true;
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
    protected static function doLogs($values)
    {
        return write_log($values,RUNTIME_PATH."/makepayment");
    }

}