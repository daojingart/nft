<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/30   23:11
 * +----------------------------------------------------------------------
 * | className: 银行卡快捷支付
 * +----------------------------------------------------------------------
 */

namespace hy;


use exception\BaseException;
use hy\tools\Utils;
use think\Config;

/**
 * 快捷银行卡操作类
 */
class Bank extends Base
{

    /**
     * 银行卡签约接口
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface bankSign
     * @Time: 2022/8/30   23:11
     */
    public function bankSign($member_id)
    {
        $param_sign = [
            'out_trade_no' => $member_id."_".Utils::orderNo(),
            'merch_user_id' => $member_id,
            'bank_type' => 0,
            'return_url' => Config::get('payConfig')['hyPay']['bindCard_notify_url'],
            'notify_url' => Config::get('payConfig')['hyPay']['return_url'],
        ];
        //加密上面的参数
        $encrypt_data = Utils::des3Encrypt($param_sign,$this->config['des3Key']);
        $param['encrypt_data'] = $encrypt_data;
        $this->publicRequest['api_type'] = 0;
        $this->publicRequest['method'] = '/v1/HeepayAuthPageSign';
        $this->publicRequest['biz_content'] = json_encode($param);
        //对公共参数进行签名
        $sign = Utils::generaterSign($this->publicRequest,$this->config['md5Key']);
        $this->publicRequest["sign"] = $sign;
        $requestString = json_encode($this->publicRequest);
        $result_json = Utils::sendRequest(self::$gateWayUrl,$this->HeepayAuthSend,$requestString);
        $result_array = json_decode($result_json,true);
        if($result_array['return_code'] != 'SUCCESS'){
            throw new BaseException(['msg' =>$result_array['return_msg'], 'code' =>-10]);
        }
        return [
            'redirect_url' => $result_array['redirect_url'],
        ];
    }

    /**
     * 银行卡信息查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface HeepayAuthConfirm
     * @Time: 2022/9/1   19:37
     */
    public function GetUserBindCardList($data)
    {
        $param_sign = [
            'out_trade_no' => $data['out_trade_no'],
        ];
        //加密上面的参数
        $encrypt_data = Utils::des3Encrypt($param_sign,$this->config['des3Key']);
        $param['encrypt_data'] = $encrypt_data;
        $this->publicRequest['api_type'] = 0;
        $this->publicRequest['method'] = '/v1/HeepayAuthPageSignQuery';
        $this->publicRequest['biz_content'] = json_encode($param);
        //对公共参数进行签名
        $sign = Utils::generaterSign($this->publicRequest,$this->config['md5Key']);
        $this->publicRequest["sign"] = $sign;
        $requestString = json_encode($this->publicRequest);
        $result_json = Utils::sendRequest(self::$gateWayUrl,$this->HeepayAuthPageSignQuery,$requestString);
        $result_array = json_decode($result_json,true);
        if($result_array['return_code'] != 'SUCCESS'){
            throw new BaseException(['msg' =>$result_array['return_msg'], 'code' =>-10]);
        }
        return $result_array;
    }


}