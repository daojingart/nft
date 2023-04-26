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


use adapay\Callback;
use app\common\model\PaySetting;
use think\Controller;

class Notifyada extends Controller
{
    protected $callback;

    public function __construct()
    {
        $this->callback = new Callback(PaySetting::getItem('hfPay'));
        parent::__construct();
    }

    /**
     * 支付异步回调通知
     */
    public function payment()
    {
        $this->callback->notifyUrl();
    }

    /**
     * 绑卡异步
     */
    public function adaBindCard()
    {
        die(200);
    }


    /**
     * 解绑异步
     */
    public function unbindCard()
    {
        die(200);
    }



}
