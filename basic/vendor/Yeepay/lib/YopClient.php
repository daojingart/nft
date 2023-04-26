<?php

require_once("YopRequest.php");
require_once("YopResponse.php");
require_once("YopError.php");
require_once ("YopConfig.php");
require_once ("Util/BlowfishEncrypter.php");
require_once("Util/YopSignUtils.php");
require_once("Util/HttpRequest.php");

class YopClient{

    public function __construct(){

    }

    public function __set($name, $value){
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }

    static public function get($methodOrUri, $YopRequest){
        $content = self::getForString($methodOrUri, $YopRequest);
        $response = self::handleResult($YopRequest, $content);
        return $response;
    }

    static public function getForString($methodOrUri, $YopRequest){
        $YopRequest->httpMethod = "GET";
        $serverUrl = self::richRequest($methodOrUri, $YopRequest);

        self::signAndEncrypt($YopRequest);
        $serverUrl .= (strpos($serverUrl,'?') === false ?'?':'&') . $YopRequest->toQueryString();
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    static public function post($methodOrUri, $YopRequest){
        $content = self::postForString($methodOrUri, $YopRequest);
        $response = self::handleResult($YopRequest, $content);
        return $response;
    }

    static public function postForString($methodOrUri, $YopRequest){
        $YopRequest->httpMethod = "POST";
        $serverUrl = self::richRequest($methodOrUri, $YopRequest);

        self::signAndEncrypt($YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    static public function upload($methodOrUri, $YopRequest){
        $content = self::uploadForString($methodOrUri, $YopRequest);
        $response = self::handleResult($YopRequest, $content);
        return $response;
    }

    static public function uploadForString($methodOrUri, $YopRequest){
        $YopRequest->httpMethod = "POST";
        $serverUrl = self::richRequest($methodOrUri, $YopRequest);

        self::signAndEncrypt($YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    static public function signAndEncrypt($YopRequest){

        if(empty($YopRequest->method)){
            error_log("method must be specified");
        }
        if(empty($YopRequest->secretKey)){
            error_log("secretKey must be specified");
        }
        $appKey =$YopRequest->appKey;
        if(empty($appKey)){
            $appKey = $YopRequest->config->CUSTOMER_NO;
        }
        if(empty($appKey)){
            error_log("appKey 与 customerNo 不能同时为空");
        }

        $toSignParamMap = array_merge($YopRequest->paramMap,array("v"=>$YopRequest->version, "method"=>$YopRequest->method));
        $signValue=YopSignUtils::sign($toSignParamMap,$YopRequest->ignoreSignParams,$YopRequest->secretKey,$YopRequest->signAlg,$YopRequest->config->debug);

        date_default_timezone_set('PRC');
        $dataTime = new DateTime();
        $timestamp = $dataTime->format(DateTime::ISO8601); // Works the same since const ISO8601 = "Y-m-d\TH:i:sO"

        $headers = array();
        $headers['x-yop-appkey'] = $appKey;
        $headers['x-yop-date'] = $timestamp;
        $headers['Authorization'] = "YOP-HMAC-AES128 " . $signValue;

        $YopRequest->headers=$headers;
        if($YopRequest->encrypt) {
            YopClient::encrypt($YopRequest);
        }
    }

    #加密
    static public function encrypt($YopRequest){
        $builder = $YopRequest->paramMap;
       // var_dump($builder);
        /*foreach ($builder as $k => $v){
            if($YopRequest->Config->ispublicedKey($k)){
                unset($builder[$k]);
            }else{
            }
        }*/
        if(!empty($builder)){
            $encryptBody="";
            foreach ($builder as $k=>$v){
                $encryptBody .= strlen($encryptBody) == 0 ? "" : "&";
                $encryptBody .= $k."=".urlencode($v);
            }
        }
        if(empty($encryptBody)){
            $YopRequest->addParam($YopRequest->Config->ENCRYPT,true);
        }else{
            if(!empty($YopRequest->{$YopRequest->Config->APP_KEY})){
                $encrypt = AESEncrypter::encode($encryptBody,$YopRequest->secretKey);
                $YopRequest->addParam($YopRequest->Config->ENCRYPT,$encrypt);
            }else{
                $encrypt=BlowfishEncrypter::encode($encryptBody,$YopRequest->secretKey);
                $YopRequest->addParam($YopRequest->Config->ENCRYPT,$encrypt);
            }
        }
    }

    #解密
    static public function decrypt($YopRequest, $strResult){
        if(!empty($strResult) && $YopRequest->{$YopRequest->Config->ENCRYPT}){
            if(!empty($YopRequest->{$YopRequest->Config->APP_KEY})){
                $strResult = AESEncrypter::decode($strResult, $YopRequest->secretKey);
            }else{
                $strResult = BlowfishEncrypter::decode($strResult, $YopRequest->secretKey);
            }
        }
        return  $strResult;
    }

    static public function richRequest($methodOrUri, $YopRequest){
        if(strpos($methodOrUri, $YopRequest->config->serverRoot)){
            $methodOrUri = substr($methodOrUri,strlen($YopRequest->config->serverRoot)+1);
        }
        $serverUrl = $YopRequest->serverRoot;
        $serverUrl .= $methodOrUri;
        preg_match('@/rest/v([^/]+)/@i', $methodOrUri, $version);
        if(!empty($version)){
            $version = $version[1];
            if(!empty($version)){
                $YopRequest->setVersion($version);
            }
        }
        $YopRequest->setMethod($methodOrUri);
        return $serverUrl;
    }

   static public function handleResult($YopRequest, $content){
//        print_r($content);
        if ($YopRequest->downRequest) {
            return $content;
        }
           $response = new YopResponse();
           $jsoncontent = json_decode($content['content']);
           $response->requestId = $YopRequest->requestId;

        $response->requestId = $YopRequest->requestId;
        if(!empty($jsoncontent->result)){
            $response->state = "SUCCESS";
            $response->result = $jsoncontent->result;
            $response->sign = $jsoncontent->sign;
        } else {
            $response->state = "FAILURE";
            //$response->error = new YopError();
            $response->error->code = $jsoncontent->error->code;
            $response->error->message = $jsoncontent->error->message;
            $response->sign = $jsoncontent->sign;
        }
      // $response->validSign = YopSignUtils::isValidResult($jsoncontent->result, $YopRequest->secretKey, $YopRequest->signAlg,$jsoncontent->sign);


       return $response;
    }
}
