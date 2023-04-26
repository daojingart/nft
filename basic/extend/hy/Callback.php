<?php

namespace hy;

use app\common\components\helpers\RedisUtils;
use app\common\model\CardCategory;
use app\common\model\MemberCard;
use app\common\model\PaySetting;
use app\notice\model\Order;
use hy\tools\Logs;

/**
 * 汇元异步回调类
 */
class Callback extends Base
{
    /**
     * 快捷银行卡绑定回调类
     * @ApiAuthor [Mr.Zhang]
     */
    public function addBankNotice($data)
    {
        if(empty($data)){
            die('ok');
        }
        $bankInfo = (new Bank(PaySetting::getItem('hyPay')))->GetUserBindCardList($data);
        if($bankInfo['return_code'] != 'SUCCESS'){
            die('ok');
        }
        $member_id = substr($data['out_trade_no'],0,strrpos($data['out_trade_no'],"_"));
        $CardCategory = (new CardCategory())->where(['title'=>$bankInfo['bank_name']])->find();
        $category_id = 0;
        if(empty($CardCategory)){
            $category_id = (new CardCategory())->insertGetId([
                'title' => $bankInfo['bank_name'],
                'color' => '#BCD3D8',
                'app_id' => '10001',
                'create_time' => time(),
                'update_time' => time(),
                'thumb' => ''
            ]);
        }else{
            $category_id = $CardCategory['id'];
        }
        $MemberCard = (new MemberCard())->where(['txn_seqno'=>$bankInfo['auth_code']])->find();
        if(empty($MemberCard)){
            (new MemberCard())->insert([
                'card_no' => $bankInfo['bank_card_no'],
                'card_name' => $bankInfo['bank_user_name'],
                'bank' => $bankInfo['bank_name'],
                'phone' => $bankInfo['mobile'],
                'app_id' => '10001',
                'create_time' => time(),
                'update_time' => time(),
                'member_id' => $member_id,
                'category_id' => $category_id,
                'card_type' => '储蓄卡',
                'pay_type' => 4,
                'txn_seqno' => $bankInfo['auth_code'],
                'build_status' => '20',
            ]);
        }
        $url = HOST."/h5/h5.html#/";
        Header("Location:$url");
        exit;
    }


    /**
     * 支付异步通知
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function payNotice($data)
    {
        if(empty($data)){
            die(1);
        }
        $param_array = json_decode($data,true);
        if($param_array['bill_status']=='Success'){
            $lockKey = "return_notifyUrl:{$param_array['out_trade_no']}";
            if (RedisUtils::lock($lockKey, 10)) {
                Logs::write_log("{$param_array['out_trade_no']}--进入多次被锁","hyPay");
                die(1);
            }
            if($param_array['pay_method']==63){
                (new Order())->where(['order_no'=>$param_array['out_trade_no']])->update(['pay_type'=>14]);
            }else{
                (new Order)->where(['order_no'=>$param_array['out_trade_no']])->update(['pay_type'=>12]);
            }
            try {
                $arr = [
                    'transaction_id' => $param_array['hy_bill_no'],
                    'out_trade_no' => $param_array['out_trade_no'],
                    'pay_method' => $param_array['pay_method']
                ];
                $res = (new Order())->callBack($arr);
            } catch (\Exception $e) {
                Logs::write_log($e->getMessage(),"hyPay");
                RedisUtils::unlock($lockKey);
                die(1);
            }
            RedisUtils::unlock($lockKey);
            if (($res ?? false)) {
                die(1);
            }
        }
    }

}