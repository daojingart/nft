<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/2/13   15:04
 * +----------------------------------------------------------------------
 * | className: 汇元订单处理
 * +----------------------------------------------------------------------
 */

namespace app\notice\service;

use app\common\model\Member;
use app\common\model\memberHySplitAccounts;
use app\common\model\PaySetting;
use app\common\model\Setting;
use app\common\model\WxSetting;
use hy\Divide;
use think\Controller;

class HyOrder extends Controller
{

    public function profitsharingOrder($Order_info,$arr)
    {
        if($Order_info['pay_type']['value']=='12' || $Order_info['pay_type']['value']=='14' ){  //如果是 汇元支付
            $pay_method = $arr['pay_method']; //支付方式 0 余额  16快捷
            //获取交易的金额     //组装进去的数据 然后执行分账信息
            $hy_pay = PaySetting::getItem('hyPay');
            if($pay_method==0){
                $handling_fee = $hy_pay['hy_wallet_handling'];
            }else{
                $handling_fee = $hy_pay['hy_fast_handling'];
            }
            $handling_fee = bcdiv($handling_fee,100,2);
            $amountTransaction = $Order_info['pay_price'];
            //减去扣除的手续费 得到汇元扣除的手续费
            $h_amount = bcmul($Order_info['pay_price'],$handling_fee,4); //汇元的手续费
            if($h_amount<0.1){
                $h_amount = 0.1;
            }
            $amountTransaction = bcsub($amountTransaction,$h_amount,4); //剩余的钱 再去计算平台的收益
            //如果是 二级市场的订单 需要计算会员的分润 如果不是则不用计算
            $deduct_values = 0;
            if ($Order_info['order_type'] == 3) {
                //获取需要扣除的金额
                $serviceValues = Setting::getItem("service");
                //获取总的服务费比例：
                $sum_serviceValues =  bcadd($serviceValues['service_fee'],$serviceValues['creator_fee'],2);
                $sum_serviceValues_percentage = bcdiv($sum_serviceValues,100,2);
                //计算需要扣除的金额
                $deduct_values = bcmul($amountTransaction,$sum_serviceValues_percentage,4);
            }
            $sellerAmount = bcsub($amountTransaction,$deduct_values,4);
            if($sellerAmount>0){
                $hy_order_sn = $this->orderSplitAccountsNo();
                $memberInfo = (new Member())->where(['member_id'=> $Order_info['sale_member_id']])->field("member_id,user_uid")->find();
                (new memberHySplitAccounts())->insertData([
                    'order_id' => $Order_info['order_id'],
                    'order_sn' => $arr['out_trade_no'],
                    'hy_bill_no' => $arr['transaction_id'],
                    'new_order_sn' => $hy_order_sn,
                    'pay_amt_fen' => $Order_info['total_price'],
                    'user_uid' => $memberInfo['user_uid'],
                    'member_id' => $memberInfo['member_id'],
                ]);
                (new Divide(PaySetting::getItem("hyPay")))->ApplyPayShare([
                    'hy_bill_no' => $arr['transaction_id'],
                    'out_trade_no' => $arr['out_trade_no'],
                    'out_share_no' => $hy_order_sn,
                    'deduct_values' => $deduct_values, //平台的佣金手续费
                    'user_uid' => $memberInfo['user_uid'],
                    'user_amount' => $sellerAmount, //卖家的佣金,
                    'amountTransaction' => $amountTransaction, //除去汇元的剩余
                    'order_type' => $Order_info['order_type']
                ]);
            }
        }
    }



    /**
     * 生成订单号
     */
    protected function orderSplitAccountsNo()
    {
        return 'FZ'.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }


}