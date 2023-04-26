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

use app\common\extend\hfpay\Payment;
use app\common\extend\hfpay\Tools;
use app\common\extend\hfpay\Wallet;
use think\Controller;

class Notifyhf extends Controller
{
    /**
     * 钱包开户回调
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface walletNoticle
     * @Time: 2022/9/23   15:01
     */
    public function walletNoticle()
    {
        $param = file_get_contents('php://input');
        $param = substr($param,12);
        (new Wallet())->callbacknotify($param);
    }

    /**
     *  支付异步通知
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface notify
     * @Time: 2022/9/24   22:32
     */
    public function synchronizeNotify()
    {
        $param = file_get_contents('php://input');
        $param = substr($param,12);
        (new Payment())->callbacknotify($param);
    }

    /**
     * 开户手续费异步支付通知
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface notify
     * @Time: 2022/9/24   22:32
     */
    public function asynchronousFree()
    {
        $param = file_get_contents('php://input');
        $param = substr($param,12);
        (new Payment())->callbackFreeNotify($param);
    }


}
