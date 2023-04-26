<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/2/13   16:20
 * +----------------------------------------------------------------------
 * | className: 系统内部支付分润处理
 * +----------------------------------------------------------------------
 */

namespace app\notice\service;

use app\common\model\Finance;
use app\common\model\Setting;

class SystemPay
{
    /**
     * 系统内部的支付分润
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface profitsharingOrder
     * @Time: 2023/2/13   16:21
     */
    public function profitsharingOrder($Order_info)
    {
        //获取交易的金额
        $amountTransaction = $Order_info['pay_price'];
        //获取需要扣除的金额
        $serviceValues = Setting::getItem("service");
        //获取总的服务费比例：
        $sum_serviceValues = bcadd($serviceValues['service_fee'], $serviceValues['creator_fee'], 2);
        $sum_serviceValues_percentage = bcdiv($sum_serviceValues, 100, 2);
        //计算需要扣除的金额
        $deduct_values = bcmul($amountTransaction, $sum_serviceValues_percentage, 2);
        //计算卖家的佣金
        $sellerAmount = bcsub($amountTransaction, $deduct_values, 2);
        if ($sellerAmount > 0) {
            $dealRecord = [
                'member_id' => $Order_info['sale_member_id'],
                'type' => 2, //分佣
                'amount' => $sellerAmount,
                'remark' => "用户购买售卖藏品获得收益{$sellerAmount}"
            ];
            (new Finance())->allowField(true)->save($dealRecord);
        }
    }

}