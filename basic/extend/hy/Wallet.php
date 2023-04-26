<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/19   11:07
 * +----------------------------------------------------------------------
 * | className: 汇元钱包开户 登录
 * +----------------------------------------------------------------------
 */

namespace hy;

use exception\BaseException;
use hy\tools\Utils;

class Wallet extends Base
{
    /**
     * 钱包页面地址[开户/登录]
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface openLoginWallet
     * @Time: 2022/8/19   11:07
     */
    public function openLoginWallet($member_id)
    {
        $param = [
            'user_code' => $member_id
        ];
        $this->publicRequest['biz_content'] = json_encode($param);
        //对公共参数进行签名
        $sign = Utils::generaterSign($this->publicRequest,$this->config['md5Key']);
        $this->publicRequest["sign"] = $sign;
        $requestString = json_encode($this->publicRequest);
        $result_json = Utils::sendRequest(self::$gateWayUrl,$this->LoginWallet,$requestString);
        $result_array = json_decode($result_json,true);
        if($result_array['return_code'] != 'SUCCESS'){
            $this->error = $result_array['return_msg'];
            return false;
        }
        return $result_array['redirect_url'];
    }

    /**
     * 获取用户信息 [钱包数据]
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getUserInfo
     * @Time: 2022/8/19   18:10
     */
    public function getUserInfo($member_id)
    {
        $param = [
            'user_code' => $member_id
        ];
        $this->publicRequest['biz_content'] = json_encode($param);
        //对公共参数进行签名
        $sign = Utils::generaterSign($this->publicRequest,$this->config['md5Key']);
        $this->publicRequest["sign"] = $sign;
        $requestString = json_encode($this->publicRequest);
        $result_json = Utils::sendRequest(self::$gateWayUrl,$this->GetUserInfo,$requestString);
        $result_array = json_decode($result_json,true);
        if($result_array['return_code'] != 'SUCCESS'){
            $this->error = $result_array['return_msg'];
            return false;
        }
        return $result_array['user_uid'];
    }


}