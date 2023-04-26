<?php

namespace app\common\library;

use app\admin\model\Setting;
use app\common\model\MemberReal;
use app\common\model\Order;
use app\common\model\PaySetting;
use exception\BaseException;
use sd\Acctmgr;
use sd\Cloud;
use sd\Quick;

/**
 * 杉德支付类
 */
class SdPay
{
    /**
     * 杉德快捷支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function QuickPayment($order_sn, $total_fee, $goods_name, $member_id)
    {
        $order_info = (new Order())->where(['order_no' => $order_sn])->find();
        if (empty($order_info)) {
            throw new BaseException(['msg' => "订单信息错误", 'code' => -1]);
        }
        if ($order_info['pay_status']['value'] != 1) {
            throw new BaseException(['msg' => "订单信息错误", 'code' => -1]);
        }
        $order = Setting::getItem('order');
        $expire_time = date('YmdHis', strtotime($order_info['create_time']) + $order['pay_time'] * 60);
        $member_realInfo = (new MemberReal())->where(['member_id' => $member_id])->find();
        if (empty($member_realInfo)) {
            throw new BaseException(['msg' => "请先实名认证", 'code' => -1]);
        }
        return (new Quick(PaySetting::getItem('sdPay')))->paymentQuick($order_sn, $expire_time, $total_fee, $goods_name, $member_id, $member_realInfo['name'], $member_realInfo['card']);
    }


    /**
     * 杉德云账户支付
     * @ApiAuthor [Mr.Zhang]
     */
    public function cloudPayment($order_sn, $total_fee, $goods_name, $member_id)
    {
        $order_info = (new Order())->where(['order_no' => $order_sn])->find();
        if (empty($order_info)) {
            throw new BaseException(['msg' => "订单信息错误", 'code' => -1]);
        }
        if ($order_info['pay_status']['value'] != 1) {
            throw new BaseException(['msg' => "订单信息错误", 'code' => -1]);
        }
        $order = Setting::getItem('order');
        $expire_time = date('YmdHis', strtotime($order_info['create_time']) + $order['pay_time'] * 60);
        $member_realInfo = (new MemberReal())->where(['member_id' => $member_id])->find();
        if (empty($member_realInfo)) {
            throw new BaseException(['msg' => "请先实名认证", 'code' => -1]);
        }
        $deduct_values = 0;
        $sale_member_id = 0;
        //判断下订单类型是否支持分账
        if(in_array($order_info['order_type'],['3','11'])){
            $serviceValues = Setting::getItem("service");
            //获取总的服务费比例：
            $sum_serviceValues =  bcadd($serviceValues['service_fee'],$serviceValues['creator_fee'],2);
            $sum_serviceValues_percentage = bcdiv($sum_serviceValues,100,2);
            //计算需要扣除的金额
            $deduct_values = bcmul($total_fee,$sum_serviceValues_percentage,2);
            $sale_member_id = $order_info['sale_member_id'];
        }
        return (new Cloud(PaySetting::getItem('sdPay')))->paymentCloud($order_sn,$expire_time,$total_fee,$goods_name,$member_id,$member_realInfo['name'],$sale_member_id,$order_info['order_type'],$deduct_values);
    }

    /**
     * 获取用户开户状态
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function getUserStatus($member_id)
    {
        return (new Acctmgr(PaySetting::getItem('sdPay')))->getMemberStatusQuery($member_id);
    }

}