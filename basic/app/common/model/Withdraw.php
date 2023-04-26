<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/14   17:04
 * +----------------------------------------------------------------------
 * | className:  提现管理
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


use app\common\extend\llp\CardPayment;
use exception\BaseException;
use lianlian\Withdrawal;
use think\Db;
use think\Exception;


class Withdraw extends BaseModel
{
    protected $name = 'withdraw';

    /**
     * 详情
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($id)
    {
        return self::get($id);
    }

    /**
     * 申请提现
     * @param $data
     * @param $member_info
     * @param $handling_fee
     * @return bool
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/20   21:46
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface toWithdrawal
     */
    public function toWithdrawal($data,$member_info,$handling_fee)
    {
        // 获取提现类型
        if($data['type'] == 2){
            //判断是不是存在同一笔多次提交的情况
            if(isset($data['order_sn'])){
                $order_info = $this->where(['order_sn'=>$data['order_sn']])->find();
                if(!empty($order_info)){
                    throw new BaseException(['msg' => '已经申请过了,请勿重复申请！']);
                }
                lockRedis('toWithdrawal_llp',$data['order_sn'],300);
            }
        }elseif ($data['type'] == 1){
            if($data['zfb_name'] == '' || $data['zfb_account'] == ''){
                throw new BaseException(['msg' => '支付宝信息错误']);
            }
        }
        Db::startTrans();
        try {
            // 获取提现手续费
            $service_price = bcmul($data['price'],bcdiv($handling_fee,100,2),2);
            // 添加提现记录
            $id = self::insertGetId([
                'amount' => $data['price'],
                'service_price' => $service_price,
                'actual_amount' => bcsub($data['price'],$service_price,3),
                'member_id' => $member_info['member_id'],
                'type' => $data['type'],
                'bank_id' => isset($data['bank_id'])?$data['bank_id']:0,
                'bank_name' => isset($bank_info['bank'])?$bank_info['bank']:'',
                'bank_card' => isset($bank_info['card_no'])?$bank_info['card_no']:'',
                'bank_nickname' => isset($bank_info['card_name'])?$bank_info['card_name']:'',
                'zfb_name' => isset($data['zfb_name'])?$data['zfb_name']:'',
                'zfb_account' => isset($data['zfb_account'])?$data['zfb_account']:'',
                'create_time' => time(),
                'app_id' => self::$app_id,
                'order_sn' => isset($data['order_sn'])?$data['order_sn']:'',
            ]);
            if($data['type']==1){ //A 钱包提现 支付宝正常打款
                // 扣除余额
                (new Finance())->insert([
                    'amount' => -$data['price'],
                    'member_id' => $member_info['member_id'],
                    'type' => 3,
                    'remark' => '提现金额',
                    'content' => '提现金额'.$data['price'],
                    'from' => 1,
                    'create_time' => time(),
                    'update_time' => time(),
                    'app_id' => self::$app_id,
                    'apply_id' => $id
                ]);
            }
            if($data['type']==2){ //连连钱包提现  发起提现申请
                $result_array = (new Withdrawal(PaySetting::getItem('lianlianPay')))->payment($member_info['member_id'],$data['linked_agrtno'],$data['order_sn'],time(),$data['price'],$data['password'],$data['random_key'],$service_price);
                if(isset($result_array['ret_code']) && $result_array['ret_code']=='8888'){
                    //需要进行二级短信交易确认
                    Db::commit();
                    return ['code'=>8888,'msg'=>$result_array['token']];
                }
                if(isset($result_array['ret_code']) && $result_array['ret_code']=='8889'){
                    //需要进行二级短信交易确认
                    Db::commit();
                    return ['code'=>1,'msg'=>'提现申请成功'];
                }
                if(!isset($result_array['ret_code']) || $result_array['ret_code'] != '0000'){
                    Db::rollback();
                    $this->error = $result_array['ret_msg'];
                    return false;
                }
            }
            Db::commit();
            return ['code'=>1,'msg'=>'提现成功'];
        }catch (Exception $e){
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }

    }



}