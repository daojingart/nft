<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/9   9:32 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 微信支付配置
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\store;


use app\admin\controller\Controller;
use app\common\model\PaySetting;
use app\common\model\WxSetting as WxSettingModel;

class Pay extends Controller
{
    /**
     * 微信支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function balance()
    {
        return $this->updateEvent('balance');
    }

    /**
     * 微信支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function wxpay()
    {
        return $this->updateEvent('wxPay');
    }


    /**
     * 支付宝支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function alipay()
    {
        return $this->updateEvent('aliPay');
    }

    /**
     * 连连支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function lianlianpay()
    {
        return $this->updateEvent('lianlianPay');
    }

    /**
     * 杉德支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function sdpay()
    {
        return $this->updateEvent('sdPay');
    }

    /**
     * 汇付支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function hfpay()
    {
        return $this->updateEvent('hfPay');
    }

    /**
     * 汇元支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function hypay()
    {
        return $this->updateEvent('hyPay');
    }

    /**
     * 汇付通支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function hftpay()
    {
        return $this->updateEvent('hftPay');
    }


    /**
     * 易宝支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function yeepay()
    {
        return $this->updateEvent('yeePay');
    }

    /**
     * 首信易支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     */
    public function sxpay()
    {
        return $this->updateEvent('sxPay');
    }




    /**
     * 更新配置
     * @param $key
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    private function updateEvent($key)
    {
        if (!$this->request->isAjax()) {
            $values = PaySetting::getItem($key);
            return $this->fetch($key, compact('values'));
        }
        $model = new PaySetting;
        $param = $this->postData($key);
        if ($model->edit($key, $param)) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失败');
    }

}