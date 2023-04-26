<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/9/23   15:36
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace hftpay;

use app\admin\model\Setting;
use app\api\model\order\Order as OrderModel;
use app\common\components\helpers\RedisUtils;
use app\common\model\FfOpenFreeOrder;
use app\common\model\Member;
use app\common\model\MemberReal;
use app\common\model\Order;
use app\notice\model\Order as noticeOrderModel;
use think\Config;
use think\Exception;
use think\Request;

class Payment extends Base
{
    /**
     * 统一下单接口
     * @param $param
     * @throws \think\Exception
     * @Time: 2022/9/24   18:46
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface payments
     */
    public function payments($member_id,$order_sn,$amount,$goods_name,$order_type,$expire_time,$user_cust_id)
    {
        //查询卖家的信息
        $order_info = (new Order())->where(['order_no'=>$order_sn])->find();
        $time = date("Ymd",time());
        $params = [
            'version' => 10,
            'mer_cust_id' => $this->config['mer_cust_id'],
            'order_id' => $order_sn,
            'user_cust_id' => $user_cust_id,
            'order_date' => $time,
            'trans_amt' => $amount,
            'goods_desc' => $goods_name,
            'order_expire_time' => $expire_time,
            'dev_info_json' => json_encode([
                'devType' =>1,
                'ipAddr' => Request()->ip(),
                'IMEI' => '01147200197659'.$member_id
            ]),
            'object_info' => json_encode([
                'marketType' => 1,
                'objectName' => $goods_name
            ]),
            'bg_ret_url' => Config::get('payConfig')['hytPay']['wallet_notify_url'],
            'ret_url' => Config::get('payConfig')['hytPay']['return_url'],
        ];
        //如果订单是分润订单 则需要进行分润订单处理
        if($order_type == 3 || $order_type == 11){
            $member_info = (new Member())->where(['member_id'=>$order_info['sale_member_id']])->find();
            if(!$member_info['user_cust_id'] || !$member_info['acct_id']){
                $this->error = "卖家信息错误,无法正常购买！联系平台--凭据--{$order_info['sale_member_id']}";
                return false;
            }
            //计算下 分账的金额  然后进行 分账 如果是分账需要商户的分账也需要传
            //计算分账的资金
            $serviceValues = \app\common\model\Setting::getItem("service");
            //获取总的服务费比例：
            $sum_serviceValues =  bcadd($serviceValues['service_fee'],$serviceValues['creator_fee'],2);
            $sum_serviceValues_percentage = bcdiv($sum_serviceValues,100,2);
            //计算需要扣除的金额
            $deduct_values = bcmul($amount,$sum_serviceValues_percentage,2);
            //计算卖家的佣金
            $sellerAmount = bcsub($amount,$deduct_values,2);
            $params['div_details'] = json_encode([
                [
                    'divCustId' => $member_info['user_cust_id'],
                    'divAcctId' => $member_info['acct_id'],
                    'divAmt' => $sellerAmount,
                    'riskDivType' => 1
                ],
                [
                    'divCustId' => $this->config['mer_cust_id'],
                    'divAcctId' => $this->config['hf_divacctId'],
                    'divAmt' => $deduct_values,
                    'riskDivType' => 2
                ]
            ]);
        }else{
            $params['div_details'] = json_encode([
                [
                    'divCustId' => $this->config['mer_cust_id'],
                    'divAcctId' => $this->config['hf_divacctId'],
                    'divAmt' => $amount,
                    'riskDivType' => 2
                ]
            ]);
        }
        //进行加密数据
        $requestData = Tools::requestData($this->gateWayUrl."/hfpwallet/pay033",Tools::makeSign(self::$sign_param,$params)['check_value'],$this->config['mer_cust_id']);
        $result = Tools::verifySign(array_merge(self::$sign_verifySign, ['check_value'=>json_decode($requestData['body'],1)['check_value']]));
        (new OrderModel())->where(['order_no'=>$order_sn])->update(['pay_type' => 15]);
        return $result['pay_url'];
    }




    /**
     * 获取支付状态
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getPayStatus
     * @Time: 2022/10/4   15:37
     */
    public function getPayStatus($order_sn,$order_date)
    {
        $params = [
            'version' => "10",
            'mer_cust_id' => self::$config['mer_cust_id'],
            'order_id' => $order_sn,
            'order_date' => $order_date,
            'trans_type' => "36",
        ];
        $requestData = Tools::requestData($this->gateWayUrl."/alse/qry008",Tools::makeSign(self::$sign_param,$params)['check_value']);
        return  Tools::verifySign(array_merge(self::$sign_verifySign, ['check_value'=>json_decode($requestData['body'],1)['check_value']]));
    }


    /**
     * 统一下单接口
     * @param $param
     * @return array
     * @throws \think\Exception
     * @Time: 2022/9/24   18:46
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface payments
     */
    public function premiumPayments($member_id,$order_sn,$amount,$goods_name,$card)
    {
        //查询下 信息
        $memberReal_info = (new MemberReal())->where(['member_id' => $member_id])->find();
        if(empty($memberReal_info)){
            throw new Exception("请先实名认证");
        }
        //查询卖家的信息
        $order_info = (new FfOpenFreeOrder())->where(['order_sn'=>$order_sn])->find();
        $time = date("Ymd",time());
        $order = Setting::getItem('order');
        $params = [
            'version' => 10,
            'mer_cust_id' => $this->config['mer_cust_id'],
            'order_id' => $order_sn,
            'order_date' => $time,
            'trans_amt' => $amount,
            'goods_desc' => $goods_name,
             'user_name' => $memberReal_info['name'],
             'id_card_type' =>10,
             'id_card' => $card,
            'order_expire_time' => date('YmdHis', strtotime($order_info['create_time'])+$order['pay_time']*60),
            'dev_info_json' => json_encode([
                'devType' =>1,
                'ipAddr' => Request()->ip(),
                'IMEI' => '01147200197659'.$member_id
            ]),
            'object_info' => json_encode([
                'marketType' => 1,
                'objectName' => $goods_name
            ]),
            'bg_ret_url' => Config::get('payConfig')['hytPay']['pay_notify_url'],
            'ret_url' => Config::get('payConfig')['hytPay']['pay_return_url'],
        ];
        //如果订单是分润订单 则需要进行分润订单处理
        $params['div_details'] = json_encode([
            [
                'divCustId' => $this->config['mer_cust_id'],
                'divAcctId' => $this->config['hf_divacctId'],
                'divAmt' => $amount,
                'riskDivType' => 2
            ]
        ]);
		//进行加密数据
        $requestData = Tools::requestData($this->gateWayUrl."/hfpwallet/pay033",Tools::makeSign(self::$sign_param,$params)['check_value'],$this->config['mer_cust_id']);
        $result = Tools::verifySign(array_merge(self::$sign_verifySign, ['check_value'=>json_decode($requestData['body'],1)['check_value']]));
        (new OrderModel())->where(['order_no'=>$order_sn])->update(['pay_type' => 15]);
        return $result['pay_url'];
    }



}