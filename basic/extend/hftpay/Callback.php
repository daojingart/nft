<?php

namespace hftpay;


use app\common\components\helpers\RedisUtils;
use app\common\model\FfOpenFreeOrder;
use app\common\model\Member;
use app\notice\model\Order as noticeOrderModel;

/**
 * 汇付天下 汇付通 异步
 */
class Callback extends Base
{
    /**
     * 钱包开户异步处理
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface callbacknotify
     * @Time: 2022/9/26   16:11
     */
    public function callbacknotify($param)
    {
        $result = Tools::verifySign(array_merge(self::$sign_verifySign, ['check_value'=>$param]));
        Tools::doLogs($result,'walletnotifyurl');
        if($result['resp_code']!='C00000'){
            echo "RECV_ORD_ID_".$result['order_id'];die;
        }
        //更新用户的钱包状态以及客户号钱包子账号
        (new Member())->where(['member_id'=>$result['user_id']])->update([
            'user_cust_id' => $result['user_cust_id'],
            'acct_id' => $result['acct_id']
        ]);
        echo "RECV_ORD_ID_".$result['order_id'];die;
    }

    /**
     * 开户手续费异步支付通知
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface callbacknotify
     * @Time: 2022/9/26   15:31
     */
    public function callbackFreeNotify($param)
    {
        $result = Tools::verifySign(array_merge(self::$sign_verifySign, ['check_value'=>$param]));
        Tools::doLogs($result,'notifyurlfree');
        if($result['resp_code']!='C00000'){
            die("RECV_ORD_ID_".$result['order_id']);
        }
        //处理回调信息
        $order_info = (new FfOpenFreeOrder)->where(['order_sn'=>$result['order_id']])->find();
        if (empty($order_info)) {
            die("RECV_ORD_ID_".$result['order_id']);
        }
        if ($order_info['pay_status'] == 20) {
            die("RECV_ORD_ID_".$result['order_id']);
        }
        (new Member())->where(['member_id'=>$order_info['member_id']])->update(['hf_open_free'=>20]);
        (new FfOpenFreeOrder)->where(['order_sn'=>$result['order_id']])->update(['pay_status'=>20,'pay_time'=>time()]);
        die("RECV_ORD_ID_".$result['order_id']);
    }


    /**
     * 异步支付通知
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface callbacknotify
     * @Time: 2022/9/26   15:31
     */
    public function callbackPaynotify($param)
    {
        $result = Tools::verifySign(array_merge(self::$sign_verifySign, ['check_value'=>$param]));
        Tools::doLogs($result,'notifyurl');
        if($result['resp_code']!='C00000'){
            echo "RECV_ORD_ID_".$result['order_id'];die;
        }
        //处理回调信息
        $Order_info = (new OrderModel)->where(['order_no'=>$result['order_id']])->find();
        if (empty($Order_info)) {
            echo "RECV_ORD_ID_".$result['order_id'];die;
        }
        if ($Order_info['pay_status']['value'] == 2) {
            echo "RECV_ORD_ID_".$result['order_id'];die;
        }
        $arr = [
            'transaction_id' => $result['platform_seq_id'],
            'out_trade_no' => $result['order_id'],
        ];
        // //回调成功 删除这个元素值
        // $redis = initRedis();
        // $redis->LREM("hfOrderNoList",json_encode([
        //     'order_no' => $Order_info['order_no'],
        //     'date' => $result['order_date']
        // ]),0);
        $lockKey = "returnHF_notifyUrl:{$result['order_id']}";
        if (RedisUtils::lock($lockKey, 10)) {
            Tools::doLogs("{$result['order_id']}--进入多次被锁");
            echo "RECV_ORD_ID_".$result['order_id'];die;
        }
        try {
            if((new noticeOrderModel())->callBack($arr)){
                echo "RECV_ORD_ID_".$result['order_id'];
                RedisUtils::unlock($lockKey);
            }
        } catch (\Exception $e) {
            Tools::doLogs($e->getMessage());
            RedisUtils::unlock($lockKey);
        }
        RedisUtils::unlock($lockKey);
    }



}