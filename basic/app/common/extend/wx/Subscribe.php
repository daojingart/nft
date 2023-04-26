<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/27   10:49 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 微信小程序订阅消息
 * +----------------------------------------------------------------------
 */

namespace app\common\extend\wx;


use app\common\model\WxSetting;
use exception\BaseException;
use think\Cache;

class Subscribe extends Wx
{
    /**
     * @Notes: 获取所有模板消息的ID
     * @Interface getTemplateId
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/27   10:51 下午
     */
    public function getTemplateId()
    {
        $appInfo = WxSetting::detail()['wxapp_template'];
        return  self::jsonDecode($appInfo);
    }


    /**
     * @Notes: 微信订单支付成功
     * @Interface payment
     * @param $order
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/11   2:18 下午
     * @author: Mr.Zhang
     */
    public function paymentMsg($order)
    {
        //获取可用模板
        $template_id = $this->getTemplateId()['wxapp_order_pay'];
        if(!$template_id){
            return false;
        }
        // 发送模板消息
        $this->sendTemplateMessage([
            'touser' => $order['open_id'], //接收用户的openID
            'template_id' => $template_id, //消息通知模板的ID
            'page' => "pages/classify/teamdetail?order_id={$order['order_id']}&showTab=joining", //URL跳转的链接
            'data' => [
                'character_string1' => [
                    'value' => $order['order_sn'] //订单编号
                ],
                'date2' => [
                    'value' => date("Y-m-d H:i:s",time()) //支付时间
                ],
                'thing3' => [
                    'value' => mb_substr($order['title'],0,20) //商品名称
                ],
                'amount4' => [
                    'value' => $order['pay_price'] //支付金额
                ],
                'thing7' => [
                    'value' => "点击详情查看更多商品" //备注
                ]
            ]
        ]);
    }


    /**
     * @Notes: 收益通知
     * @Interface payment
     * @param $order
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/11   2:18 下午
     * @author: Mr.Zhang
     */
    public function wxappOrderShip($order)
    {
        //获取可用模板
        $template_id = $this->getTemplateId()['wxapp_income_message'];
        if(!$template_id){
            return false;
        }
        // 发送模板消息
        $this->sendTemplateMessage([
            'touser' => $order['open_id'], //接收用户的openID
            'template_id' => $template_id, //消息通知模板的ID
            'page' => 'pages/personal/cyincome', //URL跳转的链接
            'data' => [
                'name1' => [
                    'value' => $order['member_name'] //下单人
                ],
                'thing2' => [
                    'value' => $order['title'] //购买产品名称
                ],
                'time3' => [
                    'value' => $order['pay_time'] //下单时间
                ],
                'amount4' => [
                    'value' => $order['amount'] //获得佣金
                ],
                'thing6' => [
                    'value' => "点击查看更多详情信息" //备注
                ]
            ]
        ]);
    }


    /**
     * @Notes: 提现成功通知
     * @Interface payment
     * @param $order
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/11   2:18 下午
     * @author: Mr.Zhang
     */
    public function wxappIncomeMessage($order)
    {
        //获取可用模板
        $template_id = $this->getTemplateId()['wxapp_arrive_message'];
        if(!$template_id){
            return false;
        }
        // 发送模板消息
        $this->sendTemplateMessage([
            'touser' => $order['open_id'], //接收用户的openID
            'template_id' => $template_id, //消息通知模板的ID
            'page' => 'pages/personal/account', //URL跳转的链接
            'data' => [
                'amount1' => [
                    'value' => $order['amount'] //提现金额
                ],
                'amount2' => [
                    'value' => $order['handling_fee'] //手续费
                ],
                'thing3' => [
                    'value' => "手动转账" //打款方式
                ],
                'thing6' => [
                    'value' => "点击查看更多详情" //提示
                ],
            ]
        ]);
    }


    /**
     * @Notes: 拼团进度通知
     * @Interface payment
     * @param $order
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/11   2:18 下午
     * @author: Mr.Zhang
     */
    public function wxappArriveMessage($order)
    {
        //获取可用模板
        $template_id = $this->getTemplateId()['wxapp_team_success_message'];
        if(!$template_id){
            return false;
        }
        // 发送模板消息
        $this->sendTemplateMessage([
            'touser' => $order['open_id'], //接收用户的openID
            'template_id' => $template_id, //消息通知模板的ID
            'page' => "pages/classify/teamdetail?order_id={$order['order_id']}&showTab=joining", //URL跳转的链接
            'data' => [
                'thing1' => [
                    'value' => $order['title'] //商品名称
                ],
                'phrase9' => [
                    'value' => $order['phrase9'] //状态
                ],
                'amount4' => [
                    'value' => $order['amount4'] //订单价格
                ],
                'thing6' => [
                    'value' => $order['thing6'] //温馨提示
                ]
            ]
        ]);
    }


    /**
     * @Notes: 订单发货通知
     * @Interface payment
     * @param $order
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/11   2:18 下午
     * @author: Mr.Zhang
     */
    public function wxappTeamSuccessMessage($order)
    {
        //获取可用模板
        $template_id = $this->getTemplateId()['wxapp_order_ship'];
        if(!$template_id){
            return false;
        }
        // 发送模板消息
        $this->sendTemplateMessage([
            'touser' => $order['open_id'], //接收用户的openID
            'template_id' => $template_id, //消息通知模板的ID
            'page' => "pages/classify/teamdetail?order_id={$order['order_id']}&showTab=joining", //URL跳转的链接
            'data' => [
                'character_string1' => [
                    'value' => $order['order_sn'] //订单编号
                ],
                'thing2' => [
                    'value' => $order['title'] //商品名称
                ],
                'thing7' => [
                    'value' => $order['express_company'] //物流名称
                ],
                'character_string4' => [
                    'value' => $order['express_no'] //快递单号
                ],
                'thing8' => [
                    'value' => "请您注意查收您的包裹奥" //订单商品
                ]
            ]
        ]);
    }


    /**
     * @Notes: 积分变更提醒
     * @Interface payment
     * @param $order
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/11   2:18 下午
     * @author: Mr.Zhang
     */
    public function wxappScoreMessage($order)
    {
        //获取可用模板
        $template_id = $this->getTemplateId()['wxapp_score_message'];
        if(!$template_id){
            return false;
        }
        // 发送模板消息
        $this->sendTemplateMessage([
            'touser' => $order['open_id'], //接收用户的openID
            'template_id' => $template_id, //消息通知模板的ID
            'page' => 'pages/integral/integral', //URL跳转的链接
            'data' => [
                'character_string1' => [
                    'value' => $order['character_string1'] //消耗数量
                ],
                'character_string2' => [
                    'value' => $order['character_string2'] //剩余积分
                ],
                'thing7' => [
                    'value' => $order['thing7'] //商品名称
                ],
                'character_string6' => [
                    'value' => $order['character_string6'] //订单编号
                ]
            ]
        ]);
    }


    /**
     * @Notes: 审核结果通知
     * @Interface payment
     * @param $order
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/11   2:18 下午
     * @author: Mr.Zhang
     */
    public function wxappMerchantReview($order)
    {
        //获取可用模板
        $template_id = $this->getTemplateId()['wxapp_merchant_review'];
        if(!$template_id){
            return false;
        }
        // 发送模板消息
        $this->sendTemplateMessage([
            'touser' => $order['open_id'], //接收用户的openID
            'template_id' => $template_id, //消息通知模板的ID
            'page' => 'pages/order/order', //URL跳转的链接
            'data' => [
                'thing1' => [
                    'value' => $order['store_name'] //审核对象
                ],
                'phrase2' => [
                    'value' => $order['status'] //审核结果
                ],
                'time3' => [
                    'value' => date("Y-m-d H:i:s",time()) //审核时间
                ],
                'thing4' => [
                    'value' => $order['content'] //审核说明
                ],
                'thing5' => [
                    'value' => "如有疑问，请联系我们"
                ]
            ]
        ]);
    }

    /**
     * @Notes:发送模板消息
     * @Interface sendTemplateMessage
     * @return bool$param
     * @throws BaseException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/11   6:19 下午
     */
    public function sendTemplateMessage($param)
    {
        // 微信接口url
        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token={$accessToken}";
        // 构建请求
        $params = [
            'touser' => $param['touser'], //接收者（用户）的 openid
            'template_id' => $param['template_id'], //所需下发的订阅模板id
            'page' => $param['page'], //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
            'data' => $param['data']  //模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
        ];
        $result = $this->curlRequest($url, $this->jsonEncode($params));
        // 记录日志
        $this->doLogs(['describe' => '小程序订阅消息推送', 'url' => $url, 'params' => $params, 'result' => $result]);
        // 返回结果
        $response = $this->jsonDecode($result);
        if (!isset($response['errcode'])) {
            $this->error = 'not found errcode';
            return false;
        }
        if ($response['errcode'] != 0) {
            $this->error = $response['errmsg'];
            return false;
        }
        return true;
    }

    /**
     * @Notes: 获取Token
     * @Interface getAccessToken
     * @return mixed
     * @throws BaseException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:48 上午
     */
    public function getAccessToken()
    {
        $cacheKey = $this->config['wxapp_app_id'] . '@access_token';
        if (!Cache::get($cacheKey)) {
            // 请求API获取 access_token
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->config['wxapp_app_id']}&secret={$this->config['wxapp_app_secret']}";
            $result = $this->curlRequest($url);
            $data = $this->jsonDecode($result);
            if (array_key_exists('errcode', $data)) {
                throw new BaseException(['msg' => "小程序订阅access_token获取失败，错误信息：{$result}"]);
            }
            // 记录日志
            $this->doLogs([
                'describe' => '小程序模板消息获取access_token',
                'url' => $url,
                'appId' => $this->config['wxapp_app_id'],
                'result' => $result
            ]);
            // 写入缓存
            Cache::set($cacheKey, $data['access_token'], 6000);    // 7000
        }
        return Cache::get($cacheKey);
    }
}