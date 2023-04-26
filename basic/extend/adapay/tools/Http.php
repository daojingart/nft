<?php

namespace  adapay\tools;


use sd\tools\Exception;

/**
 * 支付http请求类
 */
class Http
{
    /**
     * 发起请求支付
     * @param $url
     * @param $postFields
     * @param $headers
     * @param $is_json
     * @return array
     * @Time: 2022/8/8   14:41
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface curl_request
     */
    public static function curl_request($url, $postFields = null, $headers=null, $is_json=false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (is_array($postFields) && 0 < count($postFields)) {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($is_json) {
                $json_data =  json_encode($postFields);
                array_push($headers, "Content-Length:". strlen($json_data));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            }else{
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }
        }
        if (empty($headers)){
            $headers = array('Content-type: application/x-www-form-urlencoded');
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $statuCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            Logs::write_log("curl错误返回结果:".curl_error($ch),"adaPay");
        }
        curl_close($ch);
        return $response;
    }

    /**
     * 组装header 请求头
     * @param $req_url
     * @param $post_data
     * @param $header
     * @return array|mixed
     * @Time: 2022/8/8   14:41
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface get_request_header
     */
    public static function get_request_header($req_url, $post_data, $header=array(),$APIKey,$ad_rsaPrivateKey){
        array_push($header, 'Authorization:'.$APIKey);
        array_push($header, 'Signature:'.Sign::generateSignature($req_url, $post_data,$ad_rsaPrivateKey));
        return $header;
    }
}