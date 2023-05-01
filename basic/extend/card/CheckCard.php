<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 16:39
 */
namespace card;

use app\admin\model\Setting;
use exception\BaseException;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Faceid\V20180301\FaceidClient;
use TencentCloud\Faceid\V20180301\Models\IdCardVerificationRequest;
class CheckCard
{
    private $SecretId = '';
    private $SecretKey = '';
    private $ali_AppCode = '';

    private $config = '';

    public $error = '';

    public function __construct()
    {
        $config = Setting::getItem('certification');
		$this->config = $config;
    }


    /**
     * 二要素
     * @param $name
     * @param $card
     * @return bool|void
     * @Time: 2022/10/17   21:27
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface Txcheck
     */
    public function Txcheck($name,$card)
    {
        try {
            $cred = new Credential($this->config['AppKey'], $this->config['AppSecret']);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("faceid.tencentcloudapi.com");
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new FaceidClient($cred, "", $clientProfile);
            $req = new IdCardVerificationRequest();
            $params['IdCard'] = $card;
            $params['Name'] = $name;
            $req->fromJsonString(json_encode($params));
            $resp = $client->IdCardVerification($req);
            $result = json_decode($resp->toJsonString(),true);
            if (array_key_exists('Result', $result)) {
                if ($result['Result'] != 0) {
                    $this->error = "身份证信息不匹配";
                    return false;
                }
                return true;
            }
        }catch(TencentCloudSDKException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

	/**
	 * 三要素认证
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 */
	public function threeElements($param)
	{
		// 云市场分配的密钥Id
		$secretId = $this->config['qf_AppKey'];
		// 云市场分配的密钥Key
		$secretKey = $this->config['qf_AppSecret'];
		$source = 'market';
		$datetime = gmdate('D, d M Y H:i:s T');
		$signStr = sprintf("x-date: %s\nx-source: %s", $datetime, $source);
		$sign = base64_encode(hash_hmac('sha1', $signStr, $secretKey, true));
		$auth = sprintf('hmac id="%s", algorithm="hmac-sha1", headers="x-date x-source", signature="%s"', $secretId, $sign);
		// 请求方法
		$method = 'POST';
		// 请求头
		$headers = array(
			'X-Source' => $source,
			'X-Date' => $datetime,
			'Authorization' => $auth,
		);
		// 查询参数
		$queryParams = array (
		);
		// body参数（POST方法下）
		$bodyParams = array (
			'idCard' => $param['card'],
			'mobile' => $param['phone'],
			'realName' => $param['name'],
		);
		// url参数拼接
		$url = 'https://service-4epp7bin-1300755093.ap-beijing.apigateway.myqcloud.com/release/phone3element';
		if (count($queryParams) > 0) {
			$url .= '?' . http_build_query($queryParams);
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(function ($v, $k) {
			return $k . ': ' . $v;
		}, array_values($headers), array_keys($headers)));
		if (in_array($method, array('POST', 'PUT', 'PATCH'), true)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bodyParams));
		}
		$data = curl_exec($ch);
		if (curl_errno($ch)) {
			$this->error = curl_error($ch);
			return false;
		}
		curl_close($ch);
		$content_array = json_decode($data,true);
		if(empty($content_array)){
			$this->error = '效验失败,未获取到有效信息,请检查配置参数';
			return false;
		}
		if(isset($content_array['error_code']) && $content_array['error_code']!='0'){
			$this->error = $content_array['reason'];
			return false;
		}
		//1-信息匹配，-1-信息不匹配，0-运营商系统中无记录
		if(isset($content_array['error_code']) && $content_array['error_code']=='0' && $content_array['result']['VerificationResult'] == '-1'){
			$this->error = "核验结果不一致,请您检查输入信息是否为本人信息";
			return false;
		}
		if(isset($content_array['error_code']) && $content_array['error_code']=='0' && $content_array['result']['VerificationResult'] == '0'){
			$this->error = "运营商系统中无记录,请您检查输入信息是否为本人信息";
			return false;
		}
		return true;
	}

    public function threeElementsDemo($param)
    {
        // 云市场分配的密钥Id
        $secretId = $this->config['qf_AppKey'];
        // 云市场分配的密钥Key
        $secretKey = $this->config['qf_AppSecret'];
        $source = 'market';
        $datetime = gmdate('D, d M Y H:i:s T');
        $signStr = sprintf("x-date: %s\nx-source: %s", $datetime, $source);
        $sign = base64_encode(hash_hmac('sha1', $signStr, $secretKey, true));
        $auth = sprintf('hmac id="%s", algorithm="hmac-sha1", headers="x-date x-source", signature="%s"', $secretId, $sign);
        // 请求方法
        $method = 'POST';
        // 请求头
        $headers = array(
            'X-Source' => $source,
            'X-Date' => $datetime,
            'Authorization' => $auth,
        );
        // 查询参数
        $queryParams = array (
        );
        // body参数（POST方法下）
        $bodyParams = array (
            'idCard' => $param['card'],
            'mobile' => $param['phone'],
            'realName' => $param['name'],
        );
        // url参数拼接
        $url = 'https://service-4epp7bin-1300755093.ap-beijing.apigateway.myqcloud.com/release/phone3element';
        if (count($queryParams) > 0) {
            $url .= '?' . http_build_query($queryParams);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(function ($v, $k) {
            return $k . ': ' . $v;
        }, array_values($headers), array_keys($headers)));
        if (in_array($method, array('POST', 'PUT', 'PATCH'), true)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bodyParams));
        }
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->error = curl_error($ch);
            return false;
        }
        curl_close($ch);
        $content_array = json_decode($data,true);
        if(empty($content_array)){
            return '效验失败,未获取到有效信息,请检查配置参数';
        }
        if(isset($content_array['error_code']) && $content_array['error_code']!='0'){
            return $content_array['reason'];
        }
        //1-信息匹配，-1-信息不匹配，0-运营商系统中无记录
        if(isset($content_array['error_code']) && $content_array['error_code']=='0' && $content_array['result']['VerificationResult'] == '-1'){
            return "核验结果不一致,请您检查输入信息是否为本人信息";
        }
        if(isset($content_array['error_code']) && $content_array['error_code']=='0' && $content_array['result']['VerificationResult'] == '0'){
            return "运营商系统中无记录,请您检查输入信息是否为本人信息";
        }
        return 1;
    }



    /**
     * 四要素认证
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface fourElements
     * @Time: 2022/8/16   14:32
     */
    public function fourElements($param)
    {
        $host = "http://yhkys.market.alicloudapi.com";
        $path = "/communication/personal/1887";
        $method = "POST";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->config['ali_AppCode']);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
        $bodys = "acc_no={$param['acc_no']}&idcard={$param['card']}&mobile={$param['phone']}&name={$param['name']}";
        $url = $host . $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $content = curl_exec($curl);
        curl_close($curl); //关闭连接
        //处理异常 直接返回异常
        $content_array = json_decode($content,true);
        if(empty($content_array)){
            $this->error = '效验失败,未获取到有效信息,请检查配置参数';
            return false;
        }
        if(isset($content_array['code']) && $content_array['code']!='10000'){
            $this->error = $content_array['message'];
            return false;
        }
        if($content_array['data']['state']==2){
            $this->error = "核验结果不一致,请您检查输入信息是否为本人信息";
            return false;
        }
        if($content_array['data']['state']==3){
            $this->error = "核验结果异常,请您检查输入的信息";
            return false;
        }
        return true;
    }

    public function getError()
    {
        return $this->error;
    }
}