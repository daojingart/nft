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
 * | className: 杉德支付回调
 * +----------------------------------------------------------------------
 */

namespace app\notice\controller;

use app\common\extend\hfpay\Payment;
use app\common\extend\hfpay\Tools;
use app\common\extend\sd\Cloud;
use app\common\extend\sd\Wallet;
use think\Controller;

class Notifyyeepay extends Controller
{

    /**
     * 云账户开户异步通知
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface Cloudnotify
     * @Time: 2022/9/29   09:43
     */
    public function notifywallet()
    {
        $param = file_get_contents('php://input');
        (new \app\common\extend\yeepay\Wallet())->notify($param);
    }

    /**
     *  支付异步通知
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface notify
     * @Time: 2022/9/24   22:32
     */
    public function notifyPay()
    {
        $param = file_get_contents('php://input');
        (new \app\common\extend\yeepay\Payment())->notify($param);
    }


}
