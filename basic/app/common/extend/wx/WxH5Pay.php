<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/16   11:15 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 微信公众号支付
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
use app\notice\model\Order as OrderNoticeModel;
use exception\BaseException;
use think\Config;

class WxH5Pay extends Wx
{

    /**
     * 统一下单API
     * @param $order_no
     * @param $openid
     * @param $total_fee
     * @return array
     * @throws BaseException
     */
    public function unifiedorder($order_no, $total_fee,$goods_name,$config)
    {
        // 当前时间
        $time = time();
        // 生成随机字符串
        $nonceStr = md5($time);
        // API参数
        $params = [
            'appid' => $this->config['wx_app_id'],
            'mch_id' => $config['mch_id'],
            'nonce_str' => $nonceStr,
            'attach' => 'test',
            'body' => $goods_name,
            'out_trade_no' => $order_no,
            'notify_url' => Config::get("payConfig")['wechat']['notify_url'],  // 异步通知地址
            'spbill_create_ip' => \request()->ip(),
            'total_fee' => moneyToSave($total_fee), // 价格:单位分
//            'total_fee' => 1, // 价格:单位分
            'trade_type' => 'MWEB',
            'scene_info' => json_encode([
                'h5_info' => [
                    'type' => 'Wap',
                    'wap_url' => base_url(),
                    'wap_name' =>  '数藏系统',
                ],
            ]),
        ];

        // 生成签名
        $params['sign'] = $this->makeSign($params,$config);
        // 请求API
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->postXmlCurl($this->toXml($params), $url);
        $prepay = $this->fromXml($result);
        //写入日志
        self::doLogs($prepay);
        // 请求失败
        if ($prepay['return_code'] === 'FAIL') {
            throw new BaseException(['msg' => $prepay['return_msg'], 'code' => -10]);
        }
        if ($prepay['result_code'] === 'FAIL') {
            throw new BaseException(['msg' => $prepay['err_code_des'], 'code' => -10]);
        }
        return [
            'jump_url' => $prepay['mweb_url']
        ];

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
            'return_code' => $is_success ? $msg ?: 'SUCCESS' : 'FAIL',
            'return_msg' => $is_success ? 'OK' : $msg,
        ]);
        die($xml_post);
    }

    /**
     * 生成paySign
     * @param $nonceStr
     * @param $prepay_id
     * @param $timeStamp
     * @return string
     */
    private function makePaySign($nonceStr, $prepay_id, $timeStamp)
    {
        $data = [
            'appId' => $this->config['wx_app_id'],
            'nonceStr' => $nonceStr,
            'package' => 'prepay_id=' . $prepay_id,
            'signType' => 'MD5',
            'timeStamp' => $timeStamp,
        ];

        //签名步骤一：按字典序排序参数
        ksort($data);

        $string = $this->toUrlParams($data);

        //签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $this->config['pay_secret'];

        //签名步骤三：MD5加密
        $string = md5($string);

        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);

        return $result;
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

    /**
     * 以post方式提交xml到对应的接口url
     * @param $xml
     * @param $url
     * @param int $second
     * @return mixed
     */
    private function postXmlCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//严格校验
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        // 运行curl
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 生成签名
     * @param $values
     * @return string 本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    private function makeSign($values,$config)
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->toUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $config['mch_secret_key'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     * @param $values
     * @return string
     */
    private function toUrlParams($values)
    {
        $buff = '';
        foreach ($values as $k => $v) {
            if ($k != 'sign' && $v != '' && !is_array($v)) {
                $buff .= $k . '=' . $v . '&';
            }
        }
        return trim($buff, '&');
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
}