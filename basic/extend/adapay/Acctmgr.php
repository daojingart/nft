<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/8   14:15
 * +----------------------------------------------------------------------
 * | className: 用户相关操作
 * +----------------------------------------------------------------------
 */

namespace adapay;

use adapay\tools\Http;
use adapay\tools\Sign;
use exception\BaseException;

class Acctmgr extends Base
{
    protected $endpoint = "/v1/members";

    /**
     * 创建认证的账户信息
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createRealname
     * @Time: 2022/8/8   14:16
     */
    public function createRealName($param)
    {
        $params = [
            'app_id' => $this->config['app_id'],
            'member_id' => $param['member_id'],
            'tel_no' => $param['phone'],
            'user_name' => $param['name'],
            'cert_type' => '00',
            'cert_id' => $param['card'],
        ];
        $request_params = Sign::do_empty_data($params);
        $req_url =  $this->gateWayUrl . $this->endpoint;
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
        throw new BaseException(['msg' => "未知错误!请检查参数配置是否正确", 'code' => -10]);
    }

}