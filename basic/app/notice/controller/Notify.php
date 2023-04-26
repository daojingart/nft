<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 回调通知接口
 * +----------------------------------------------------------------------
 */

namespace app\notice\controller;

use app\common\extend\alipay\AliAppPay;
use app\common\extend\alipay\AliPay;
use app\common\extend\wx\AppPay;
use app\common\extend\wx\WxH5Pay;
use app\common\extend\wx\WxPay;
use app\common\model\CardCategory;
use app\common\model\MemberCard;
use app\notice\model\Order;
use think\Controller;

class Notify extends Controller
{
    /**
     * 微信公众号异步
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function order()
    {
        $WxPay = new WxPay();
        $WxPay->notify();
    }


    /**
     * 微信APP异步
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function appOrder()
    {
        $WxPay = new AppPay();
        $WxPay->notify();
    }

    /**
     * 微信h5异步
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function wxH5Pay()
    {
        $WxPay = new WxH5Pay();
        $WxPay->notify();
    }


    /**
     * 支付宝H5支付的回调
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface alipayH5Pay
     * @Time: 2022/6/25   17:45
     */
    public function alipayH5Pay()
    {
        $AliPay = new AliPay();
        $AliPay->notifyUrl();
    }

    /**
     * 支付宝H5支付的回调
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface alipayH5Pay
     * @Time: 2022/6/25   17:45
     */
    public function alipayAppOrder()
    {
        $AliPay = new AliAppPay();
        $AliPay->notifyUrl();
    }





    public function test()
    {
		echo "1111";die;
        $order = new Order();
        $order->processTasksJob([
            'transaction_id' => '2023031897551004316',
            'out_trade_no' => '2023031897551004316',
        ]);
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
        $dir_path = RUNTIME_PATH.'/notice/'.$dir_path;
        return write_log($values,$dir_path);
    }


}
