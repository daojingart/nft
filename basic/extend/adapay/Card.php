<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/8   15:00
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace adapay;

use adapay\tools\Http;
use adapay\tools\Sign;
use exception\BaseException;
use think\Config;

/**
 * 绑定银行卡
 */
class Card extends Base
{
    public $endpoint = "/v1/fast_card";

    public $gateWayUrl = "https://page.adapay.tech";

    /**
     *  创建快捷银行卡绑卡
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface buildCard
     * @Time: 2022/8/8   15:02
     */
    public function bindCard($param)
    {
        $params = array(
            'app_id' => $this->config['app_id'],
            'card_id'=> $param['card_no'],
            'tel_no'=> $param['phone'],
            'member_id'=> $param['member_id'],
        );
        $request_params = Sign::do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint. "/apply";
        $header =  Http::get_request_header($req_url, $request_params, self::$header,$this->config['APIKey'],$this->config['ad_rsaPrivateKey']);
        $result= Http::curl_request($req_url, $request_params, $header, $is_json=true);
        $result = json_decode($result,true);
        if(isset($result['data'])){
            $result_array = json_decode($result['data'],true);
            if($result_array['status'] == 'failed'){ //交易失败 返回错误提示
                throw new BaseException(['msg' => $result_array['error_msg'], 'code' => -10]);
            }
            return $result_array['id'];
        }
        throw new BaseException(['msg' => "未知错误!请联系开发者！", 'code' => -10]);
    }


    /**
     * 快捷绑卡确认
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface confirmBindCard
     * @Time: 2022/8/8   15:27
     */
    public function confirmBindCard($id,$sms_code)
    {
        $params = array(
            'apply_id' => $id,
            'sms_code'=> $sms_code,
            'notify_url' => Config::get('payConfig')['adaPay']['bindCard_notify_url'],  // 异步通知地址
        );
        $request_params = Sign::do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint. "/confirm";
        $header =  Http::get_request_header($req_url, $request_params, self::$header,$this->config['APIKey'],$this->config['ad_rsaPrivateKey']);
        $result= Http::curl_request($req_url, $request_params, $header, $is_json=true);
        $result = json_decode($result,true);
        if(isset($result['data'])){
            $result_array = json_decode($result['data'],true);
            if($result_array['status'] == 'failed'){ //交易失败 返回错误提示
                throw new BaseException(['msg' => $result_array['error_msg'], 'code' => -10]);
            }
            return true;
        }
        throw new BaseException(['msg' => "未知错误!请联系开发者！", 'code' => -10]);
    }

    /**
     * 解除绑定银行卡
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface cardList
     * @Time: 2022/8/8   15:36
     */
    public function unbindCard($member_id,$token_no)
    {
        $params = array(
            'adapay_func_code' => 'fast_card.unBindCard',
            'order_no'=> $this->orderNo(),
            'app_id'=> $this->config['app_id'],
            'member_id'=> $member_id,
            'token_no' => $token_no,
            'notify_url' => Config::get('payConfig')['adaPay']['unbindCard_notify_url'],  // 异步通知地址
        );
        $request_params = Sign::do_empty_data($params);
        $req_url =  $this->gateWayUrl .$this->endpoint.'/unBindCard';
        $header =  Http::get_request_header($req_url, $request_params, self::$header,$this->config['APIKey'],$this->config['ad_rsaPrivateKey']);
        $result= Http::curl_request($req_url, $request_params, $header, $is_json=true);
        $result = json_decode($result,true);
        if(isset($result['data'])){
            $result_array = json_decode($result['data'],true);
            if($result_array['status'] == 'failed'){ //交易失败 返回错误提示
                throw new BaseException(['msg' => $result_array['error_msg'], 'code' => -10]);
            }
            return true;
        }
        throw new BaseException(['msg' => "未知错误!请联系开发者！", 'code' => -10]);
    }


    /**
     * 获取绑定的银行卡列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface cardList
     * @Time: 2022/8/8   15:36
     */
    public function getBankList($member_id)
    {
        $params = array(
            'app_id'=> $this->config['app_id'],
            'member_id'=> $member_id,
        );
        ksort($params);
        $request_params = Sign::do_empty_data($params);
        $req_url = $this->gateWayUrl . $this->endpoint. "/list";
        $header = Http::get_request_header($req_url, http_build_query($request_params), self::$headerText,$this->config['APIKey'],$this->config['ad_rsaPrivateKey']);
        $result = Http::curl_request($req_url . "?" . http_build_query($request_params), "", $header, false);
        $result = json_decode($result,true);
        if(empty($result)){
            return [];
        }
        if(isset($result['data'])){
            $result_array = json_decode($result['data'],true);
            if($result_array['status'] == 'failed'){ //交易失败 返回错误提示
                throw new BaseException(['msg' => $result_array['error_msg'], 'code' => -10]);
            }
            return $result_array['fast_cards'];
        }
        throw new BaseException(['msg' => "未知错误!请联系开发者！", 'code' => -10]);
    }




    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return 'adacard_'.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }

}