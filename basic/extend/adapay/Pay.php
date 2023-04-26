<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/8   15:46
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace adapay;

use adapay\tools\Http;
use adapay\tools\Sign;
use app\admin\model\Setting;
use app\api\model\order\Order as OrderModel;
use app\notice\model\Order as noticeOrderModel;
use exception\BaseException;
use think\Cache;
use think\Config;

class Pay extends Base
{
    public $endpoint = "/v1/payments";
    public $endpointConfirm = "/v1/fast_pay/confirm";

    public $gateWayUrl = "https://api.adapay.tech"; //银行卡专用

    /**
     * 创建支付对象
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface payments
     * @Time: 2022/8/8   15:47
     */
    public function payments($order_no, $amount, $goods_title, $token_no,$time_expire)
    {
        $params = array(
            'app_id' => $this->config['app_id'],
            'order_no' => $order_no,
            'pay_channel' => 'fast_pay',
            'pay_amt' => $amount,
            'goods_title' => $goods_title,
            'goods_desc' => $goods_title,
            'currency' => 'cny',
            'device_info' => json_encode([
                'device_type' => '1',
                'device_ip' => request()->ip(),
            ]),
            'time_expire' => $time_expire,
            'expend' => json_encode([
                'token_no' => $token_no
            ]),
            'notify_url' => Config::get('payConfig')['adaPay']['pay_notify_url'],  //异步
        );
        $request_params = Sign::do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header =  Http::get_request_header($req_url, $request_params, self::$header,$this->config['APIKey'],$this->config['ad_rsaPrivateKey']);
        $result= Http::curl_request($req_url, $request_params, $header, $is_json=true);
        $result = json_decode($result, true);
        if (isset($result['data'])) {
            $result_array = json_decode($result['data'], true);
            if ($result_array['status'] == 'failed' && $result_array['error_code'] == 'request_order_no_repeate') { //交易失败 返回错误提示
                throw new BaseException(['msg' => $result_array['error_msg'], 'code' => -10]);
            }else if($result_array['status'] == 'failed'){
                throw new BaseException(['msg' => $result_array['error_msg'], 'code' => -10]);
            }
            return [
                'id' => $result_array['id'], //0402616408489566208
            ];
        }
        throw new BaseException(['msg' => "未知错误!请联系开发者！", 'code' => -10]);
    }

    /**
     * 确认支付对象
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface confirmPayment
     * @Time: 2022/8/8   16:01
     */
    public function confirmPayment($payment_id,$sms_code)
    {
        $params = array(
            'app_id' => $this->config['app_id'],
            'payment_id' => $payment_id,
            'sms_code' => $sms_code,
        );
        $request_params = Sign::do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpointConfirm;
        $header =  Http::get_request_header($req_url, $request_params, self::$header,$this->config['APIKey'],$this->config['ad_rsaPrivateKey']);
        $result= Http::curl_request($req_url, $request_params, $header, $is_json=true);
        $result = json_decode($result, true);
        if (isset($result['data'])) {
            $result_array = json_decode($result['data'], true);
            if ($result_array['status'] == 'failed') { //交易失败 返回错误提示
                throw new BaseException(['msg' => $result_array['error_msg'], 'code' => -10]);
            }
            return true;
        }
        throw new BaseException(['msg' => "未知错误!请联系开发者！", 'code' => -10]);
    }


    /**
     * 创建快捷支付短信重发
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface smsCodeSend
     * @Time: 2022/8/8   16:05
     */
    public function smsCodeSend($payment_id)
    {
        $params = array(
            'payment_id' => $payment_id,
        );
        $request_params = Sign::do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint;
        $header =  Http::get_request_header($req_url, $request_params, self::$header,$this->config['APIKey'],$this->config['ad_rsaPrivateKey']);
        $result= Http::curl_request($req_url, $request_params, $header, $is_json=true);
        $result = json_decode($result, true);
        if (isset($result['data'])) {
            $result_array = json_decode($result['data'], true);
            if ($result_array['status'] == 'failed') { //交易失败 返回错误提示
                throw new BaseException(['msg' => $result_array['error_msg'], 'code' => -10]);
            }
            return true;
        }
        throw new BaseException(['msg' => "未知错误!请联系开发者！", 'code' => -10]);
    }

    /**
     * 处理回调错误
     * @return bool
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-09-15 17:12
     */
    public function synchronous()
    {
        $redis = initRedis();
        $id = $redis->rPop('adaCall');
        if (!$id){
            echo '等待订单中......';die();
        }
        $params = ['payment_id'=>$id];
        ksort($params);
        $id = isset($params['payment_id']) ? $params['payment_id'] : '';
        $request_params = $params;
        $req_url =  $this->gateWayUrl . $this->endpoint ."/".$id;
        $header = $this->get_request_header($req_url, http_build_query($request_params), self::$headerText);
        $result = $this->curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
        $result = json_decode($result, true);
        if (isset($result['data'])) {
            $result_array = json_decode($result['data'], true);
            if ($result_array['status'] == 'succeeded') {
                $order = OrderModel::where('order_no',$result_array['order_no'])->find();
                write_log('主动查询订单'.json_encode($result_array),RUNTIME_PATH.'/adaPay/synchronous');
                if ($order['pay_status']['value'] != 2) {
                    $arr = [
                        'transaction_id' => $result_array['order_no'],
                        'out_trade_no' => $result_array['order_no'],
                    ];
                    if((new noticeOrderModel())->callBack($arr)){
                        self::doLogs("支付成功",'notifyurl');
                        echo "200";die;
                    }
                }
            }
            return true;
        }
    }




    /**
     * 支付成功的回到信息
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface notifyUrl
     * @Time: 2022/8/8   15:30
     */
    public function notifyUrl()
    {
        $json_string = $_POST;
        self::doLogs($json_string,'notifyurl');
        if((new AdaTools())->verifySign($json_string['sign'],$json_string['data'])){
            $json_array = json_decode($json_string['data'],true);
            self::doLogs($json_string,'notifyurl');
            //判断类型
            if($json_string['type'] == 'payment.succeeded' && $json_array['status']=='succeeded'){
                self::doLogs("支付回调修改",'notifyurl');
                $Order_info = (new OrderModel)->where(['order_no'=>$json_array['order_no']])->find();
                if (empty($Order_info)) {
                    echo "200";die;
                }
                if ($Order_info['pay_status']['value'] == 2) {
                    echo "200";die;
                }
                self::doLogs("支付回调修改",'notifyurl');
                $arr = [
                    'transaction_id' => $json_array['order_no'],
                    'out_trade_no' => $json_array['order_no'],
                ];
                if((new noticeOrderModel())->callBack($arr)){
                    self::doLogs("支付成功",'notifyurl');

                    echo "200";die;
                }
            }
        }
        echo "200";die;

    }

}