<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/16   4:11 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 支付宝APP支付
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\alipay;

use app\common\model\WxSetting;
use app\notice\model\Order as noticeOrderModel;
use think\Config;

vendor('alipay.aop.AopClient');
vendor('alipay.aop.AopCertification');
vendor('alipay.aop.request.AlipayTradeQueryRequest');
vendor('alipay.aop.request.AlipayTradeWapPayRequest');
vendor('alipay.aop.request.AlipayTradeQueryRequest');

class AliAppPay
{

    /**
     * @Notes: 统一下单支付接口
     * @Interface unifiedOrder
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/9/3   14:08
     */
    public function unifiedOrder($order_no, $pay_price,$goods_name,$config)
    {
        $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams();
        $params->appID = $config['app_id'];
        $params->appPrivateKey = $config['app_private_key'];
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
        $request = new \Yurun\PaySDK\AlipayApp\App\Params\Pay\Request();
        $request->notify_url = Config::get("payConfig")['aliPay']['notify_url']; // 支付后通知地址（作为支付成功回调，这个可靠）
        $request->businessParams->out_trade_no = $order_no; // 商户订单号
        $request->businessParams->total_amount = $pay_price; // 价格
        $request->businessParams->subject = $goods_name; // 商品标题
        $pay->prepareExecute($request, $url, $data);
        return http_build_query($data);
    }

    /**
     * @Notes: 支付宝异步通知
     * @Interface notifyUrl
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/9/7   15:01
     */
    public function notifyUrl()
    {
        $data = $_POST;
        if($data['trade_status'] != 'TRADE_SUCCESS'){
            die('success');
        }
        self::doLogs($data);
        $Order_info = (new \app\common\model\Order())->where(['order_no'=>$data['out_trade_no']])->find();
        if (empty($Order_info)) {
            die('error');
        }
        if ($Order_info['pay_status']['value'] == 2) {
            die('success');
        }
        $arr = [
            'transaction_id' => $data['trade_no'],
            'out_trade_no' => $data['out_trade_no'],
        ];
        //处理业务逻辑 修改订单状态 增加 我的藏品
        if((new noticeOrderModel())->callBack($arr)){
            die('success');
        }
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
        return write_log($values,__DIR__);
    }
}