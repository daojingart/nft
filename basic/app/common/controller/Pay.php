<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/17   5:57 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 支付总控
 * +----------------------------------------------------------------------
 */

namespace app\common\controller;


use app\common\extend\alipay\AliAppPay;
use app\common\extend\alipay\AliPay as AliPayController;
use app\common\extend\wx\AppPay;
use app\common\extend\wx\WxH5Pay;
use app\common\extend\wx\WxPay;
use app\common\model\PaySetting;

class Pay
{
    /**
     * 构建微信公众号支付[实物商城使用]
     * @param $order_no
     * @param $open_id
     * @param $pay_price
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function wxPay($order_no, $open_id, $pay_price,$goods_name)
    {
        return (new WxPay())->unifiedorder($order_no, $open_id, $pay_price,$goods_name,PaySetting::getItem("wxPay"));
    }

    /**
     * @Notes:构建APP支付 微信
     * @Interface appPay
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/8   10:24 上午
     */
    public function appPay($order_no,$total_fee,$goods_name)
    {
        return (new AppPay())->unifiedorder($order_no,$total_fee,$goods_name,PaySetting::getItem("wxPay"));
    }


    /**
     * @Notes:H5支付 微信
     * @Interface appPay
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/8   10:24 上午
     */
    public function wxH5Pay($order_no,$total_fee,$goods_name)
    {
        return (new WxH5Pay())->unifiedorder($order_no,$total_fee,$goods_name,PaySetting::getItem("wxPay"));
    }


    /**
     * @Notes: 构建支付宝支付 H5
     * @Interface wxPay
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/16   4:24 下午
     */
    public function aliPay($order_no, $pay_price,$goods_name)
    {
        return (new AliPayController())->unifiedorder($order_no, $pay_price,$goods_name,PaySetting::getItem("aliPay"));
    }

    /**
     * @Notes: 构建支付宝支付APP
     * @Interface wxPay
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/16   4:24 下午
     */
    public function aliAppPay($order_no, $pay_price,$goods_name)
    {
        return (new AliAppPay())->unifiedorder($order_no, $pay_price,$goods_name,PaySetting::getItem("aliPay"));
    }




}