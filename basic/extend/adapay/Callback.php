<?php

namespace adapay;

use adapay\tools\Logs;
use adapay\tools\Sign;
use app\common\model\Order;

/**
 * 异步回调通知类
 */
class Callback extends Base
{

    /**
     * 支付成功的回到信息
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface notifyUrl
     * @Time: 2022/8/8   15:30
     */
    public function notifyUrl()
    {
        $json_string = $_POST;
        Logs::write_log($json_string,'adaPay');
        if(Sign::verifySign($json_string['sign'],$json_string['data'],$this->config['ad_rsaPublicKey'])){
            $json_array = json_decode($json_string['data'],true);
            //判断类型
            if($json_string['type'] == 'payment.succeeded' && $json_array['status']=='succeeded'){
                $Order_info = (new Order())->where(['order_no'=>$json_array['order_no']])->find();
                if (empty($Order_info)) {
                    echo "200";die;
                }
                if ($Order_info['pay_status']['value'] == 2) {
                    echo "200";die;
                }
                $arr = [
                    'transaction_id' => $json_array['order_no'],
                    'out_trade_no' => $json_array['order_no'],
                ];
                if((new \app\notice\model\Order())->callBack($arr)){
                    echo "200";die;
                }
            }
        }
        echo "200";die;
    }

}