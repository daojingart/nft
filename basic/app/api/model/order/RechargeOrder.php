<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/6/30   00:00
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 充值订单
 * +----------------------------------------------------------------------
 */

namespace app\api\model\order;

use app\common\model\Recharge;
use app\api\model\Recharge as RechargeModel;
use app\common\model\RechargeOrder as OrderModel;
use app\common\model\RechargeOrderEcard as EcardOrderEcardModel;
use app\common\model\Setting;
use exception\BaseException;

class RechargeOrder extends OrderModel
{
    /**
     * @Notes: 创建充值订单
     * @Interface createOrder
     * @param $member_info
     * @param null $eCardId
     * @return mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/30   00:02
     */
    public function createOrder($member_info, $eCardId = null,$card_id,$pay_type,$type)
    {
        // 获取订单数据
        $data = $this->getOrderData($member_info, $eCardId,$card_id,$pay_type,$type);
        // 创建记录订单信息
        return $this->saveOrder($data);
    }

    /**
     * @Notes: 保存订单记录
     * @Interface saveOrder
     * @param $data
     * @return bool
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/30   00:22
     */
    private function saveOrder($data)
    {
        $this->allowField(true)->save($data['order']);
        if (!empty($data['card'])) {
            $PlanModel = new EcardOrderEcardModel();
            $data['card']['order_id'] = $this['order_id'];
            $data['card']['create_time'] = time();
            $data['card']['update_time'] = time();
            return $PlanModel->add($data['card']);
        }
        return true;
    }

    /**
     * @Notes: 生成充值订单
     * @Interface getOrderData
     * @param $member_info
     * @param $eCardId
     * @return array
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/30   00:06
     */
    private function getOrderData($member_info, $eCardId,$card_id,$pay_type,$type)
    {
        // 订单信息
        $data = [
            'order' => [
                'member_id' => $member_info['member_id'],
                'order_no' => $this->orderNo().$member_info['member_id'],
                'app_id' => self::$app_id,
                'pay_type' => $pay_type
            ]
        ];
        // 充值E卡选择
        $data = $this->createDataByPlan($data,$eCardId,$card_id,$type);
        // 实际到账E卡金额
        $data['order']['actual_amount'] = $data['order']['face_value'];
        return $data;
    }

    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return 'BLED'.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    /**
     * @Notes: 获取E卡套餐数据
     * @Interface createDataByPlan
     * @param $order
     * @param $planId
     * @return bool
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/30   00:06
     */
    private function createDataByPlan($data,$eCardId,$card_id,$type)
    {
        if($type ==1){  //直接充值
            //非直接充值 选择了其他的规格
            $cardInfo['ecard_id'] = '0';
            $cardInfo['face_value'] = $eCardId;
            $cardInfo['price'] = $eCardId;
        }else{
            // 获取E卡套餐详情
            // 获取E卡套餐详情
            $cardInfo = RechargeModel::detail($eCardId)->toArray();
            if (empty($cardInfo)) {
                throw new BaseException(['msg' => '充值规格不存在']);
            }
        }
        //判断价格是否为0
        if($cardInfo['face_value'] ==0){
            throw new BaseException(['msg' => '充值金额不能为0']);
        }
        $data['card'] = $cardInfo;
        $data['order']['ecard_id'] = $cardInfo['ecard_id'];
        $data['order']['face_value'] = $cardInfo['face_value']; //面值
        $data['order']['pay_price'] = $cardInfo['price']; //价格
        $data['order']['card_id'] = $card_id; //银行卡的ID
        return $data;
    }
}