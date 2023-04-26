<?php

namespace app\common\library;

use app\admin\model\Setting;
use app\common\model\MemberReal;
use app\common\model\Order;
use app\common\model\PaySetting;
use app\common\model\RechargeOrder;
use exception\BaseException;
use lianlian\Acctmgr;
use lianlian\Txn;
use think\exception\DbException;

/**
 * 连连支付类
 */
class LianPay
{
    protected $pay;

    protected $acctmgr;

    protected $lianlianPay;
    public function __construct()
    {
        $lianlianPay = PaySetting::getItem('lianlianPay');
        try {
            $this->pay = new Txn($lianlianPay);
            $this->acctmgr = new Acctmgr($lianlianPay);
            $this->lianlianPay = $lianlianPay;
        } catch (BaseException $e) {
        }
    }

    /**
     * 统一支付创单
     * @ApiAuthor [Mr.Zhang]
     * @throws BaseException
     * @throws DbException
     */
    public function createRadecreate($type,$order_id,$member_id)
    {
        if(!$type){
            throw new BaseException(['msg' => "参数错误", 'code' => -1]);
        }
        if(!in_array($type, ['USER_TOPUP','GENERAL_CONSUME'])){
            throw new BaseException(['msg' => "支付类型错误", 'code' => -1]);
        }
        if(!$order_id){
            throw new BaseException(['msg' => "参数错误", 'code' => -1]);
        }
        //判断下这个订单的ID 是否存在 且是这个用户的不然则返回错误
        $order_info = [
            'txn_type' => $type,
            'user_id' => $member_id
        ];
        if($type === 'USER_TOPUP'){
            //查询充值的订单
            $rechargeOrderInfo = (new RechargeOrder())->where(['order_id'=>$order_id,'member_id'=>$member_id])->find();
            if(empty($rechargeOrderInfo)){
                throw new BaseException(['msg' => "订单不存在", 'code' => -1]);
            }
            if($rechargeOrderInfo['pay_status']!=10){
                throw new BaseException(['msg' => "订单错误;无法创建订单", 'code' => -1]);
            }
            $order_sn = $rechargeOrderInfo['order_no'];
            $total_amount = $rechargeOrderInfo['pay_price'];
            $goods_name = "账户余额充值:".$total_amount."元";
            $create_time = $rechargeOrderInfo['create_time'];
        }else{
            $orderInfo = (new Order())::detail($order_id);
            if(empty($orderInfo)){
                throw new BaseException(['msg' => "订单不存在", 'code' => -1]);
            }
            if($orderInfo['pay_status']['value']!=1){
                throw new BaseException(['msg' => "订单错误;无法创建订单", 'code' => -1]);
            }
            if($member_id != $orderInfo['member_id']){
                throw new BaseException(['msg' => "非法请求订单信息", 'code' => -1]);
            }
            $order_sn = $orderInfo['order_no'];
            $total_amount = $orderInfo['pay_price'];
            $goods_name = $orderInfo['goods'][0]['goods_name'];
            $create_time = $orderInfo['create_time'];
            if(in_array($order_info['order_type'],[3,11])){
                $serviceValues = Setting::getItem("service");
                //计算分账的资金
                //获取总的服务费比例：
                $sum_serviceValues =  bcadd($serviceValues['service_fee'],$serviceValues['creator_fee'],2);
                $sum_serviceValues_percentage = bcdiv($sum_serviceValues,100,2);
                //计算需要扣除的金额
                $deduct_values = bcmul($orderInfo['total_price'],$sum_serviceValues_percentage,2);
                //计算卖家的佣金
                $sellerAmount = bcsub($orderInfo['total_price'],$deduct_values,2);
                if($deduct_values>0){
                    $order_info['payeeInfo'] = [
                        [
                            'payee_id' => $this->lianlianPay['app_id'],
                            'payee_type' => 'MERCHANT',
                            'payee_amount' => $deduct_values,
                            'payee_memo' => '交易分账'
                        ],[
                            'payee_id' => $orderInfo['sale_member_id'],
                            'payee_type' => 'USER',
                            'payee_amount' => $sellerAmount,
                            'payee_memo' => '交易分账'
                        ]
                    ];
                }else{
                    $order_info['payeeInfo'] = [
                        [
                            'payee_id' => $orderInfo['sale_member_id'],
                            'payee_type' => 'USER',
                            'payee_amount' => $sellerAmount,
                            'payee_memo' => '交易分账'
                        ]
                    ];
                }
            }else{
                $order_info['payeeInfo'] = [
                    [
                        'payee_id' => $this->lianlianPay['app_id'],
                        'payee_type' => 'MERCHANT',
                        'payee_amount' => $orderInfo['total_price'],
                    ]
                ];
            }
        }
        $order = Setting::getItem('order');
        $order_info['pay_expire'] = $order['pay_time'];
        $order_info['orderInfo'] = [
            'txn_seqno' => $order_sn,
            'txn_time' => date("YmdHis",strtotime($create_time)),
            'total_amount' => $total_amount,
            'goods_name' => $goods_name,
        ];
        return $this->pay->tradecreate($order_info);
    }


    /**
     * 余额支付
     * @ApiAuthor [Mr.Zhang]
     * @throws BaseException
     * @throws DbException
     */
    public function balancePay($order_no,$pwd,$random_key,$member_id,$total_fee,$goods_name)
    {
        $member_real_info = (new MemberReal())->where(['member_id'=>$member_id])->find();
        return $this->pay->paymentBalance($order_no,$total_fee,$goods_name,$member_id,$member_real_info['phone'],$member_real_info['create_time'],$member_real_info['name'],$member_real_info['card'],$pwd,$random_key);
    }

    /**
     * 银行卡支付
     * @ApiAuthor [Mr.Zhang]
     * @throws BaseException
     * @throws DbException
     */
    public function bankcardPay($order_no,$pwd,$random_key,$member_id,$total_fee,$goods_name,$linked_agrtno)
    {
        $member_real_info = (new MemberReal())->where(['member_id'=>$member_id])->find();
        return $this->pay->paymentBankcard($order_no,$total_fee,$goods_name,$member_id,$member_real_info['phone'],$member_real_info['create_time'],$member_real_info['name'],$member_real_info['card'],$pwd,$random_key,$linked_agrtno);
    }


    /**
     * 获取开户状态
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function getIsOpenStatus($member_id)
    {
        return $this->acctmgr->getQueryUserinfo($member_id);
    }



}