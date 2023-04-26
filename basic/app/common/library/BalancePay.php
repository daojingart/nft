<?php

namespace app\common\library;

use app\common\model\Finance;
use app\common\model\Member;
use app\notice\model\Order;
use think\Db;

/**
 * 余额支付
 */
class BalancePay
{
    public $error;


    /**
     * 余额支付
     */
    public function balancePayment($params,$member_id,$account)
    {
        $sub_str = substr($params['order_no'],0,5);
        if($sub_str!='GOODS'){
            if ($params['pay_status'] == 2) {
                $this->error = "订单已支付，不可重复操作";
                return false;
            }
        }
        if ($account < $params['pay_price']) {
            $this->error = "余额不足";
            return false;
        }
        Db::startTrans();
        try {
            //添加消费记录
            $dealRecord = [
                'member_id' => $params['member_id'],
                'type'      => 3, //下单
                'amount'    => -$params['pay_price'],
                'remark'    => '下单购买，扣除余额' . $params['pay_price'],
            ];
            (new Finance())->allowField(true)->save($dealRecord);
            $arr = [
                'transaction_id' => $params['order_no'],
                'out_trade_no'   => $params['order_no'],
            ];
            if (!(new Order())->callBack($arr)) {
                $this->error = "支付失败";
                return false;
            }
            Db::commit();
            return [
                'order_id' => $params['order_id'],
                'order_sn' => $params['order_no'],
            ];
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }





    /**
     * 余额支付
     * @ApiAuthor [Mr.Zhang]
     */
    public function getError()
    {
        return $this->error;
    }
}