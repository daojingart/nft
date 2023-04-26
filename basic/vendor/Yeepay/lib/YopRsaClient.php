<?php

require_once("YopRequest.php");
require_once("YopResponse.php");
require_once("YopError.php");
require_once("Util/HttpRequest.php");
require_once("Util/StringUtils.php");
require_once("Util/HttpUtils.php");
require_once("Util/Base64Url.php");


class YopRsaClient
{

    public function __construct()
    {

    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @param $encode_data
     * @return array
     */
    public static function SignRsaParameter($methodOrUri, $YopRequest)
    {
        $appKey = $YopRequest->{$YopRequest->config->APP_KEY};
        if (empty($appKey)) {
            $appKey = $YopRequest->config->CUSTOMER_NO;
            $YopRequest->removeParam($YopRequest->config->APP_KEY);
        }
        if (empty($appKey)) {
            error_log("appKey 与 customerNo 不能同时为空");
        }

        date_default_timezone_set('PRC');
        $dataTime = new DateTime();
        $timestamp = $dataTime->format(DateTime::ISO8601); // Works the same since const ISO8601 = "Y-m-d\TH:i:sO"

        $headers = array();

        $headers['x-yop-appkey'] = $YopRequest->appKey;
        $headers['x-yop-request-id'] = $YopRequest->requestId;

        $protocolVersion = "yop-auth-v2";
        $EXPIRED_SECONDS = "1800";

        $authString = $protocolVersion . "/" . $appKey . "/" . $timestamp . "/" . $EXPIRED_SECONDS;

        $headersToSignSet = array();
        array_push($headersToSignSet, "x-yop-request-id");

        $appKey = $YopRequest->{$YopRequest->config->APP_KEY};

        if (!StringUtils::isBlank($YopRequest->config->CUSTOMER_NO)) {
            $headers['x-yop-customerid'] = $appKey;
            array_push($headersToSignSet, "x-yop-customerid");
        }

        // Formatting the URL with signing protocol.
        $canonicalURI = HttpUtils::getCanonicalURIPath($methodOrUri);

        // Formatting the query string with signing protocol.
        $canonicalQueryString = YopRsaClient::getCanonicalQueryString($YopRequest, true);

        // Sorted the headers should be signed from the request.
        $headersToSign = YopRsaClient::getHeadersToSign($headers, $headersToSignSet);

        // Formatting the headers from the request based on signing protocol.
        $canonicalHeader = YopRsaClient::getCanonicalHeaders($headersToSign);

        $signedHeaders = "";
        if ($headersToSignSet != null) {
            foreach ($headersToSign as $key => $value) {
                $signedHeaders .= strlen($signedHeaders) == 0 ? "" : ";";
                $signedHeaders .= $key;
            }
            $signedHeaders = strtolower($signedHeaders);
        }

        $canonicalRequest = $authString . "\n" . $YopRequest->httpMethod . "\n" . $canonicalURI . "\n" . $canonicalQueryString . "\n" . $canonicalHeader;

        // Signing the canonical request using key with sha-256 algorithm.

        if (empty($YopRequest->secretKey)) {
            error_log("secretKey must be specified");
        }

        extension_loaded('openssl') or die('php需要openssl扩展支持');

        $private_key = $YopRequest->secretKey;
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        $privateKey = openssl_pkey_get_private($private_key);// 提取私钥
        ($privateKey) or die('密钥不可用');

        $signToBase64 = "";
        // echo "tyuiop".$canonicalRequest;
        openssl_sign($canonicalRequest, $encode_data, $privateKey, "SHA256");

        openssl_free_key($privateKey);

        $signToBase64 = Base64Url::encode($encode_data);

        $signToBase64 .= '$SHA256';

        $headers['Authorization'] = "YOP-RSA2048-SHA256 " . $protocolVersion . "/" . $appKey . "/" . $timestamp . "/" . $EXPIRED_SECONDS . "/" . $signedHeaders . "/" . $signToBase64;

        if ($YopRequest->config->debug) {
            var_dump("authString=" . $authString);
            var_dump("canonicalURI=" . $canonicalURI);
            var_dump("canonicalQueryString=" . $canonicalQueryString);
            var_dump("canonicalHeader=" . $canonicalHeader);
            var_dump("canonicalRequest=" . $canonicalRequest);
            var_dump("signToBase64=" . $signToBase64);
        }
        $YopRequest->headers = $headers;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public static function get($methodOrUri, $YopRequest)
    {
        $content = YopRsaClient::getForString($methodOrUri, $YopRequest);
        $response = YopRsaClient::handleRsaResult($YopRequest, $content);
        return $response;
    }

    public static function getForString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "GET";
        $serverUrl = YopRsaClient::richRequest($methodOrUri, $YopRequest);
        $serverUrl .= (strpos($serverUrl, '?') === false ? '?' : '&') . $YopRequest->toQueryString();

        self::SignRsaParameter($methodOrUri, $YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    public static function post($methodOrUri, $YopRequest)
    {
        $content = YopRsaClient::postString($methodOrUri, $YopRequest);
        $response = YopRsaClient::handleRsaResult($YopRequest, $content);
        return $response;
    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @return type
     */
    public static function postString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "POST";
        $serverUrl = YopRsaClient::richRequest($methodOrUri, $YopRequest);

        self::SignRsaParameter($methodOrUri, $YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    /**
     * @param $YopRequest
     * @param $forSignature
     * @return string
     */
    public static function getCanonicalQueryString($YopRequest, $forSignature)
    {
        if (!empty($YopRequest->jsonParam)) {
            return "";
        }

        $ArrayList = array();
        $StrQuery = "";
        foreach ($YopRequest->paramMap as $k => $v) {
            if ($forSignature && strcasecmp($k, "Authorization") == 0) {
                continue;
            }
            array_push($ArrayList, $k . "=" . rawurlencode($v));
        }
        sort($ArrayList);

        foreach ($ArrayList as $kv) {
            $StrQuery .= strlen($StrQuery) == 0 ? "" : "&";
            $StrQuery .= $kv;
        }

        return $StrQuery;
    }

    /**
     * @param $headers
     * @param $headersToSign
     * @return arry
     */
    public static function getHeadersToSign($headers, $headersToSign)
    {
        $ret = array();
        if ($headersToSign != null) {
            $tempSet = array();
            foreach ($headersToSign as $header) {
                array_push($tempSet, strtolower(trim($header)));
            }

            $headersToSign = $tempSet;
        }

        foreach ($headers as $key => $value) {
            if ($value != null && !empty($value)) {
                if (($headersToSign == null && isDefaultHeaderToSign($key)) || ($headersToSign != null && in_array(strtolower($key), $headersToSign) && $key != "Authorization")) {
                    $ret[$key] = $value;
                }

            }
        }
        ksort($ret);
        return $ret;
    }

    /**
     * @param $header
     * @return bool
     */
    public static function isDefaultHeaderToSign($header)
    {
        $header = strtolower(trim($header));
        $defaultHeadersToSign = array();
        array_push($defaultHeadersToSign, "host");
        array_push($defaultHeadersToSign, "content-type");

        return strpos($header, "x-yop-") == 0 || in_array($defaultHeadersToSign, $header);
    }

    /**
     * @param $headers
     * @return string
     */
    public static function getCanonicalHeaders($headers)
    {
        if (empty($headers)) {
            return "";
        }

        $headerStrings = array();

        foreach ($headers as $key => $value) {
            if ($key == null) {
                continue;
            }
            if ($value == null) {
                $value = "";
            }
            $key = HttpUtils::normalize(strtolower(trim($key)));
            $value = HttpUtils::normalize(trim($value));
            array_push($headerStrings, $key . ':' . $value);
        }

        sort($headerStrings);
        $StrQuery = "";

        foreach ($headerStrings as $kv) {
            $StrQuery .= strlen($StrQuery) == 0 ? "" : "\n";
            $StrQuery .= $kv;
        }

        return $StrQuery;
    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @return YopResponse
     */
    public static function upload($methodOrUri, $YopRequest)
    {
        $content = self::uploadForString($methodOrUri, $YopRequest);
        $response = self::handleRsaResult($YopRequest, $content);
        return $response;
    }

    public static function uploadForString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "POST";
        $serverUrl = self::richRequest($methodOrUri, $YopRequest);
        self::SignRsaParameter($methodOrUri, $YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    static public function richRequest($methodOrUri, $YopRequest)
    {
        if (strpos($methodOrUri, $YopRequest->config->serverRoot)) {
            $methodOrUri = substr($methodOrUri, strlen($YopRequest->config->serverRoot) + 1);
        }
        $serverUrl = $YopRequest->serverRoot;
        $serverUrl .= $methodOrUri;
        preg_match('@/rest/v([^/]+)/@i', $methodOrUri, $version);
        if (!empty($version)) {
            $version = $version[1];
            if (!empty($version)) {
                $YopRequest->setVersion($version);
            }
        }
        $YopRequest->setMethod($methodOrUri);

        return $serverUrl;
    }

    static public function handleRsaResult($YopRequest, $content)
    {

        $sign = trim($content['header']['x-yop-sign']);
        $signStr = $content['content'];
        $signStr = self::trimall($signStr);
        $response = new YopResponse();
        $jsoncontent = json_decode($content['content']);
        if(empty($sign)){
            return $content['content'];
        }

        if (!empty($jsoncontent->result)) {
            $response->state = "SUCCESS";
            $response->result = $jsoncontent->result;
            $response->requestId = $YopRequest->requestId;
            // $signStr=$jsoncontent->result;
        } else {
            $response->state = "FAILURE";
            $response->requestId = $jsoncontent->requestId;
            $response->error->code = $jsoncontent->code;
            $response->error->message = $jsoncontent->message;
            $response->error->subCode = $jsoncontent->subCode;
            $response->error->subMessage = $jsoncontent->subMessage;
//            $signStr = $content['content'];
        }
        $response->validSign = YopRsaClient::isValidRsaResult($signStr, $sign, $YopRequest->yopPublicKey);
        return json_encode($response);
    }
    //去空格换行符
    static public function trimall($str){
        $qian=array(" ","　","\t","\n","\r");
        return str_replace($qian, '', $str);
    }

#header sign 验签
    public static function isValidRsaResult($result, $sign, $public_key)
    {
        // $result=json_encode($result,320);
        $str = "";
        if ($result == null || empty($result)) {
            $str = "";
        } else {
            $str .= trim($result);
        }
        ;

        $public_key = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($public_key, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        $pu_key = openssl_pkey_get_public($public_key);

      //  $str=str_replace("\\","",str_replace("\\n","",$str));

        $str= self::trimall($str);
        $str= trim($str, '"');
        $res = openssl_verify($str,Base64Url::decode($sign), $pu_key,"SHA256"); //验证
        openssl_free_key($pu_key);
        if ($res == 1) {
            return true;
        } else {
            return false;
        }
    }
}
