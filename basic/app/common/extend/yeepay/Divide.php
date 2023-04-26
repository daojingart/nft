<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/10/14   14:28
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\yeepay;

use app\common\model\DivideOrder;
use app\common\model\Member;
use app\common\model\Order;
use exception\BaseException;
use think\Exception;

class Divide extends Base
{
    /**
     * 申请分润
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface divideApply
     * @Time: 2022/10/14   14:29
     */
    public function divideApply($value)
    {
        //计算分润问题
        $order_info = (new Order())->where(['order_no'=>$value['order_sn']])->find();
        $member_info = (new Member())->where(['member_id'=>$order_info['sale_member_id']])->find();
        if($member_info['yeepay_walletUserNo']){
            //计算下二级市场的手续费佣金问题
            $serviceValues = \app\common\model\Setting::getItem("service");
            //获取总的服务费比例：
            $sum_serviceValues =  bcadd($serviceValues['service_fee'],$serviceValues['creator_fee'],3);
            $sum_serviceValues_percentage = bcdiv($sum_serviceValues,100,3);
            //计算需要扣除的金额
            $deduct_values = bcmul($order_info['total_price'],$sum_serviceValues_percentage,2);
            //计算下 易宝的每笔交易需要扣除的手续费金额
            $y_sum_serviceValues_percentage = bcdiv($this->config['poundage'],100,4);
            $yeepay_sellerAmount = bcmul($order_info['total_price'],$y_sum_serviceValues_percentage,3);
            //然后计算下剩余的金额 如果这个金额少于1分钱 则不用计算这个手续费
            $yeepay_sellerAmount = round($yeepay_sellerAmount, 2);
            $k_yeepay_sellerAmount = 0;
            if($yeepay_sellerAmount>=0.01){
                $k_yeepay_sellerAmount = $yeepay_sellerAmount;
            }
            //计算卖家的佣金
            $sellerAmount = bcsub($order_info['total_price'],$deduct_values,2); //68   32  0.65
            $order_sn = $this->orderNo();
            $request = new \YopRequest($this->config['appKey'], $this->config['private_key']);
            $request->addParam("parentMerchantNo", $this->config['merchantNo']);                                            //发起方商户编号
            $request->addParam("merchantNo", $this->config['merchantNo']);//收款商户编号
            $request->addParam("orderId",$value['order_sn']);
            $request->addParam("uniqueOrderNo",$value['yeep_order_sn']);
            $request->addParam("divideRequestId",$order_sn);
            $request->addParam("divideDetail",json_encode([
                [
                    'ledgerNo' => $member_info['yeepay_walletUserNo'],
                    'amount' => $sellerAmount,
                    'ledgerType' => 'MERCHANT2MEMBER'
                ],
                [
                    'ledgerNo' => $this->config['merchantNo'],
                    'amount' => bcsub($deduct_values,$k_yeepay_sellerAmount,2),
                    'ledgerType' => 'MERCHANT2MERCHANT'
                ],
            ]));
            //提交Post请求，第一个参数为手册上的接口地址
            $response = \YopRsaClient::post("/rest/v1.0/divide/apply", $request);
            $responseArray = json_decode($response, true);
            self::doLogs($responseArray,'divideApply');
            (new DivideOrder())->where(['id' => $value['id']])->update([
                'divideStatus' => 20,
            ]);
            if ($responseArray['state'] == 'SUCCESS') {
                if (isset($responseArray['result']['code']) && $responseArray['result']['code'] =='OPR00000') {
                    //写入更新表条件 执行更新条件
                    (new DivideOrder())->where(['id' => $value['id']])->update([
                        'divide_order_sn' => $order_sn,
                        'divideDetail' => json_encode([
                            [
                                'ledgerNo' => $member_info['yeepay_walletUserNo'],
                                'amount' => $sellerAmount,
                                'ledgerType' => 'MERCHANT2MEMBER'
                            ],
                            [
                                'ledgerNo' => $this->config['merchantNo'],
                                'amount' => $deduct_values,
                                'ledgerType' => 'MERCHANT2MERCHANT'
                            ],
                        ]),
                        'divideStatus' => 30,
                    ]);
                }else{
                    (new DivideOrder())->where(['id' => $value['id']])->update([
                        'divide_order_sn' => $order_sn,
                        'divideDetail' => json_encode([
                            [
                                'ledgerNo' => $member_info['yeepay_walletUserNo'],
                                'amount' => $sellerAmount,
                                'ledgerType' => 'MERCHANT2MEMBER'
                            ],
                            [
                                'ledgerNo' => $this->config['merchantNo'],
                                'amount' => $deduct_values,
                                'ledgerType' => 'MERCHANT2MERCHANT'
                            ],
                        ]),
                        'divideStatus' => 40,
                    ]);
                }
            }
        }
    }

    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }

}