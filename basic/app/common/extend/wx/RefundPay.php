<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/5/8   11:53 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 微信退款API类
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\wx;


class RefundPay extends Wx
{
    /**
     * @Notes: 发起退款请求
     * @Interface outPay
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/8   11:53 上午
     * https://api.mch.weixin.qq.com/secapi/pay/refund 小程序
     */
    public function outPay($order_type,$order_sn,$amount)
    {
        //根据不同的支付类型 选择不同的APPID进行退款操作
        $app_id = '';
        switch ($order_type['value'])
        {
            case "1": //公众号
                $app_id = $this->config['wx_app_id'];
                break;
            case "5": //小程序
                $app_id = $this->config['wxapp_app_id'];
                break;
            case "6": //APP
                $app_id = $this->config['wxapp_id'];
                break;
        }
        $requestParam = [
            'appid' => $app_id, //微信公众号的appid
            'mch_id' => $this->config['mch_id'], //商户号
            'nonce_str' => md5(time()), //随机字符串
            'out_trade_no' => $order_sn,//商户订单号
            'out_refund_no' => $order_sn,
            'total_fee' => sprintf("%.2f", $amount)*100,
            'refund_fee' => sprintf("%.2f", $amount)*100,
        ];
        $requestParam['sign'] = $this->getSign($requestParam);
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $result = $this->postXmlSSLCurl($this->toXml($requestParam),$url);
        $prepay = $this->fromXml($result);
        self::doLogs(array_merge(['log_title'=>'退款日志记录'],$prepay));
        if(($prepay['return_code']=='SUCCESS') && ($prepay['result_code']=='SUCCESS')){
            //退款成功之后执行的业务逻辑
            return  [
                'code' => '1',
                'data' => [
                    'refund_id' => $prepay['refund_id'],
                    'out_pay_time' => time()
                ]
            ];
        }else if(($prepay['return_code']=='FAIL') || ($prepay['result_code']=='FAIL')){
            //执行退款失败 执行的业务逻辑
            return  [
                'code' => '-1',
                'msg' => $prepay['return_msg']
            ];
        }

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


    /**
     * 生成签名
     * 参数名ASCII码从小到大排序（字典序）；
     * 如果参数的值为空不参与签名；
     * 参数名区分大小写；
     * 验证调用返回或微信主动通知签名时，传送的sign参数不参与签名，将生成的签名与该sign值作校验。
     * 微信接口可能增加字段，验证签名时必须支持增加的扩展字段
     */
    public function getSign($arr){
        $arr = array_filter($arr);
        if(isset($arr['sign'])){
            unset($arr['sign']);
        }
        ksort($arr);
        $stringA = urldecode(http_build_query($arr));
        $stringSignTemp = $stringA.'&key='. $this->config['pay_secret'];
        $sign = strtoupper(md5($stringSignTemp));
        return $sign;
    }

    /**
     * 需要使用证书的请求
     */
    public  function postXmlSSLCurl($xml,$url,$second=30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT, CERT_LOG_PATH);
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,KEY_LOG_PATH);
        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            curl_close($ch);
            return false;
        }
    }
}