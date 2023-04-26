<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/7   9:51 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 微信APP支付
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\wx;

use app\api\model\collection\Goods;
use app\api\model\collection\Goods as GoodsModel;
use app\api\model\order\Order as OrderModel;
use app\api\service\member\MemberCommonService;
use app\api\service\order\OrderService;
use app\common\constants\OrderConstant;
use app\common\controller\Task;
use app\common\model\Member;
use app\common\model\MemberGoods;
use app\common\model\MemberVipOrder;
use app\common\model\Order;
use app\common\model\GoodsLog;
use app\common\model\Setting;
use app\common\model\WxSetting;
use app\notice\model\Order as noticeOrderModel;
use exception\BaseException;
use app\notice\model\Order as OrderNoticeModel;
use think\Config;

class AppPay extends Wx
{
    /**
     * 统一下单API
     * @param $order_no
     * @param $openid
     * @param $total_fee
     * @return array
     * @throws BaseException
     */
    public function unifiedorder($order_no,$total_fee,$goods_name,$config)
    {
        $params = [
            'appid'=> $this->config['wxapp_id'],
            'mch_id'=> $config['mch_id'],
            'nonce_str'=> md5(time()),
            'body'=> $goods_name,
            'out_trade_no'=> $order_no,
            'total_fee' => moneyToSave($total_fee), // 价格:单位分
            // 'total_fee'=> 1,
            'spbill_create_ip'=> request()->ip(),
            'notify_url' =>Config::get("payConfig")['wechat']['notify_url'],  // 异步通知地址
            'trade_type'=>'APP',
        ];
        $params['sign'] =$this->getSign($params,$config);
        // 请求API
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->curlRequest($url,$this->toXml($params));
        $prepay = $this->fromXml($result);
        //写入日志
        self::doLogs(array_merge(['log_title'=>'APP发起支付成功日志'],$prepay));
        // 请求失败
        if ($prepay['return_code'] === 'FAIL') {
            throw new BaseException(['msg' => $prepay['return_msg'], 'code' => -10]);
        }
        if ($prepay['result_code'] === 'FAIL') {
            throw new BaseException(['msg' => $prepay['return_msg'], 'code' => -10]);
        }
        $params = [
            'appid'=> $this->config['wxapp_id'],
            'partnerid' => $config['mch_id'],
            'prepayid' => $prepay['prepay_id'],
            'package' =>'Sign=WXPay',
            'noncestr' => md5(time()),
            'timestamp' => strval(time()),
        ];
        $params['sign'] = $this->getSign($params,$config);
        return $params;
    }

    /**
     * 支付成功异步通知
     * @param \app\notie\model\Order $OrderModel
     * @throws \Exception
     * @throws \think\exception\DbException
     */
    public function notify()
    {
        if (!$xml = file_get_contents('php://input')) {
            $this->returnCode(false, 'Not found DATA');
        }
        // 将服务器返回的XML数据转化为数组
        $data = $this->fromXml($xml);
//        // 记录日志
        self::doLogs(array_merge(['log_title'=>'微信公众号回调日志记录'], $data));
        // 订单信息
        $Order_info = (new OrderModel)->where(['order_no'=>$data['out_trade_no']])->find();
        if (empty($Order_info)) {
            $this->returnCode(true, 'OK');
        }
        if ($Order_info['pay_status']['value'] == 2) {
            $this->returnCode(true, 'OK');
        }
        $arr = [
            'transaction_id' => $data['transaction_id'],
            'out_trade_no' => $data['out_trade_no'],
        ];
        //处理业务逻辑 修改订单状态 增加 我的藏品
        if((new noticeOrderModel())->callBack($arr)){
            $this->returnCode(true, 'OK');
        }
    }

    /**
     * 返回状态给微信服务器
     * @param bool $is_success
     * @param string $msg
     */
    private function returnCode($is_success = true, $msg = null)
    {
        $xml_post = $this->toXml([
            'return_code' => $is_success ? 'SUCCESS' : 'FAIL',
            'return_msg' => $is_success ? 'OK' : $msg,
        ]);
        die($xml_post);
    }

    /**
     * @Notes: 生成签名
     * @Interface getSign
     * @param $arr
     * @return string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/8   11:11 上午
     */
    private function getSign($arr,$config){
        $arr = array_filter($arr);
        if(isset($arr['sign'])){
            unset($arr['sign']);
        }
        ksort($arr);
        $stringA = urldecode(http_build_query($arr));
        $stringSignTemp = $stringA.'&key='.$config['mch_secret_key'];
        $sign = strtoupper(md5($stringSignTemp));
        return $sign;

    }


    /**
     * 输出xml字符
     * @param $values
     * @return bool|string
     */
    private function toXml($values)
    {
        if (!is_array($values)
            || count($values) <= 0
        ) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param $xml
     * @return mixed
     */
    private function fromXml($xml)
    {
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }


}