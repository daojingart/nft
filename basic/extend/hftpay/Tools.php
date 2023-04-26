<?php
namespace hftpay;

use exception\BaseException;
use think\Exception;

class Tools extends Base
{

    /**
     * 加签
     * @throws Exception
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface makeSign
     * @Time: 2022/9/23   13:43
     */
    public static function makeSign($sign_data,$params)
    {
        $url = self::$sign_url."/hfpcfca/cfca/makeSign";
        $data = array(
            'data' => json_encode($sign_data),
            'params' => json_encode($params)
        );
        $result_json = curlRequest($url,$data);
        $result_array = json_decode($result_json,true);
        if($result_array['resp_code']!='C00000'){
            self::$error = "加签失败";
            return false;
        }
        return $result_array;
    }

    /**
     * 加签
     * @throws Exception
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface makeSign
     * @Time: 2022/9/23   13:43
     */
    public static function verifySign($sign_data)
    {
        $url = self::$sign_url."/hfpcfca/cfca/verifySign";
        $data = [
            'params' => json_encode($sign_data)
        ];
        $result_json = curlRequest($url,$data);
        $result_array = json_decode($result_json,true);
        if($result_array['resp_code']!='C00000'){
            self::$error = "验签失败";
            return false;
        }
        $result_array = json_decode($result_array['params'],true);
        if($result_array['resp_code'] !== 'C00000'){
            self::$error = $result_array['resp_desc'];
            return false;
        }
        return $result_array;
    }


    /**
     * 请求接口返回数据
     * @param $apiUrl
     * @return array
     * @throws Exception
     */
    public static function requestData($apiUrl,$check_value,$mer_cust_id)
    {
        try{
            $data = [
                'url'         => $apiUrl,          // 接口 url
                'requestData' => [
                    'mer_cust_id' => $mer_cust_id,
                    'version' => 10,
                    'check_value' => $check_value,
                ], // 请求接口参数
                'headers' => ['Content-type' => 'application/x-www-form-urlencoded;charset=UTF-8']
            ];
            $res = self::httpPostRequest($data['url'],$data['headers'],$data['requestData']);
        } catch (\Exception $e) {
            self::$error = "api requestData error :".$e;
            return false;
        }
        return [
            'status' => $res['info']['http_code'],
            'body' => $res['body']
        ];
    }




    /**
     * curl post 请求方法
     * @param string $url
     * @param array $header
     * @param array $requestData
     * @return array
     */
    private static function httpPostRequest(string $url = '', array $header = array(), array $requestData = array())
    {
        self::doLogs(['log_title'=>$url."请求内容",'header'=>$header,'content' => $requestData]);
        $curl = curl_init();
        curl_setopt ( $curl, CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($requestData));
        $res = curl_exec($curl);
        $info = curl_getinfo($curl);
        $error = curl_error($curl);
        curl_close($curl);
        self::doLogs(['log_title'=>$url."返回内容",'content' => $res,'返回状态码'=>$error,'详细信息'=>$info]);
        return [
            'body' => $res,
            'info' => $info,
            'error' => $error,
        ];
    }

    /**
     * @Notes: 记录日志
     * @Interface doLogs
     * @param $values
     * @param string $dir_path
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    public static function doLogs($values, string $dir_path='log')
    {
        $dir_path = RUNTIME_PATH.'/hfPay/';
        return write_log($values,$dir_path);
    }


}