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


use app\common\model\PaySetting;
use hy\Callback;

class Notifyhy
{

    protected $callback;

    public function __construct()
    {
        $this->callback = new Callback(PaySetting::getItem('hyPay'));
    }

    /**
     * 汇元绑定银行卡快捷
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface payment
     * @Time: 2022/8/8   15:29
     */
    public function hyBankNotice()
    {
        $this->callback->addBankNotice($_GET);
    }

    /**
     *  adaPay  汇元支付回调
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface payment
     * @Time: 2022/8/8   15:29
     */
    public function hyCallback()
    {
        $param = '{"version":"2","app_id":"hyp200428107197000024987419303A3","owner_uid":"1071972125197","notify_type":"Trade","hy_bill_no":"H2303107484478AD","out_trade_no":"2023021854101495855","pay_amt_fen":"1","real_amt_fen":"1","bill_status":"Success","time_end":"20230310160040","pay_method":"63","sign":"FCEF35AC0C7BB50D73E58ADB7A560A79"}';
        $this->callback->payNotice($param);
    }


    /**
     * 汇元信息同步接口
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface hyReturn
     * @Time: 2022/8/29   18:50
     */
    public function hyReturn()
    {
        $data = $_GET;
        $result = (new \app\common\extend\hy\Pay())->returnNotifyUrl($data);
        if($result==1){
            $url = HOST."/h5/h5.html#/";
            Header("Location:$url");
            exit;
        }else{
            $url = HOST."/h5/h5.html#/";
            Header("Location:$url");
            exit;
        }
    }



    /**
     * 分账的回调信息
     */
    public function PayShareCallBack()
    {
        (new \app\common\extend\hy\Pay())->notifyApplyPayShareUrl();
    }


        /**
     * 测试支付使用
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface sdCard
     * @Time: 2022/8/5   17:04
     */
    public function testPay()
    {
        return (new \app\common\extend\hy\Pay())->ApplyPayShare('100260317193254');
    }

    /**
     * 解除绑定
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface sdCard
     * @Time: 2022/8/5   17:04
     */
    public function unbank()
    {
        (new Bank())->unbindCard([
            'member_id' => 'test_one50',
            'token_no' => '10032489070'
        ]);
    }


    public function test()
    {
        $order = new Order();
        //查询数据库的数据模拟队列的操作
        $order_list = $order->limit(0,10)->field("order_no")->select();
        foreach ($order_list as $key=>$value){
            $arr = [
                'transaction_id' =>$value['order_no'],
                'out_trade_no' => $value['order_no'],
                'time' => date("Y-m-d H:i:s",time()),
            ];
            $order->callBack($arr);
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
    protected static function doLogs($values,$dir_path='log')
    {
        $dir_path = RUNTIME_PATH.'/notice/'.$dir_path;
        return write_log($values,$dir_path);
    }


}
