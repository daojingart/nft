<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2021/12/18   17:03
 * +----------------------------------------------------------------------
 * | className: 云直播
 * +----------------------------------------------------------------------
 */

namespace tx;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Live\V20180801\LiveClient;
use TencentCloud\Live\V20180801\Models\DescribeLiveDomainsRequest;
use TencentCloud\Live\V20180801\Models\DescribeLivePushAuthKeyRequest;
use TencentCloud\Live\V20180801\Models\DescribeLivePlayAuthKeyRequest;
class Leb extends Base
{
    /**
     * 检测推流和拉流域名是否配置 配置则不需要配置 没有配置则需要配置则写入缓存
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface describeLiveDomains
     * @Time: 2021/12/18   17:09
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function describeLiveDomains()
    {
        try {
            $cred = new Credential($this->appid, $this->appsecret);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint(self::API_FUNCTION_DESCRIBELIVEDOMAINS);
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new LiveClient($cred, "", $clientProfile);
            $req = new DescribeLiveDomainsRequest();
            $params = [];
            $req->fromJsonString(json_encode($params));
            $resp = $client->DescribeLiveDomains($req);
            return json_decode($resp->toJsonString(),true);
        }catch(TencentCloudSDKException $e) {
            self::doLogs($e->getMessage());
            $this->errMsg = $this->switchError($e->getErrorCode());
            $this->errCode = 0;
        }
    }


    /**
     * 生成推流的URL
     * @param $domain
     * @param $streamName
     * @param $time
     * @Time: 2021/12/19   18:34
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getPushUrl
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getPushUrl($domain, $streamName,$time = null){
        $info = $this->getDescribeLivePushAuthKeyRequest($domain);
        if($info['PushAuthKeyInfo']['MasterAuthKey'] && $time){
            $txTime = strtoupper(base_convert(strtotime($time),10,16));
            $txSecret = md5($info['PushAuthKeyInfo']['MasterAuthKey'].$streamName.$txTime);
            $ext_str = "?".http_build_query(array(
                    "txSecret"=> $txSecret,
                    "txTime"=> $txTime
                ));
        }
        $data = [
            'WebRTC' => "webrtc://".$domain."/BlLive",
            'obsKey' => $streamName . (isset($ext_str) ? $ext_str : "")
        ];
        return $data;
    }

    /**
     * 获取拉流地址
     * 如果不传key和过期时间，将返回不含防盗链的url
     * @param domain 您用来推流的域名
     *        streamName 您用来区别不同推流地址的唯一流名称
     *        key 安全密钥
     *        time 过期时间 sample 2016-11-12 12:00:00
     * @return String url
     */
    public function getPullUrl($domain, $streamName,$time = null)
    {
        $info = $this->getDescribeLivePushAuthKeyRequest($domain);
        if ($info['PushAuthKeyInfo']['MasterAuthKey'] && $time) {
            $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
            $txSecret = md5($info['PushAuthKeyInfo']['MasterAuthKey'] . $streamName . $txTime);
            $ext_str = "?" . http_build_query(array(
                    "txSecret" => $txSecret,
                    "txTime" => $txTime
                ));
        }
        return [
            'webrtc' => "webrtc://".$domain."/BlLive/".$streamName.(isset($ext_str) ? $ext_str : ""),
        ];
    }


    /**
     * 获取推流域名的KEY
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getDescribeLivePushAuthKeyRequest
     * @Time: 2021/12/19   18:30
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getDescribeLivePushAuthKeyRequest($DomainName)
    {
        try {
            $cred = new Credential($this->appid, $this->appsecret);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint(self::API_FUNCTION_DESCRIBELIVEDOMAINS);
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new LiveClient($cred, "", $clientProfile);
            $req = new DescribeLivePushAuthKeyRequest();
            $params = array(
                "DomainName" => $DomainName
            );
            $req->fromJsonString(json_encode($params));
            $resp = $client->DescribeLivePushAuthKey($req);
            return json_decode($resp->toJsonString(),true);
        }catch(TencentCloudSDKException $e) {
            self::doLogs($e->getMessage());
            $this->errMsg = $this->switchError($e->getErrorCode());
            $this->errCode = 0;
        }
    }

    /**
     * 查询播放的KEY
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getDescribeLivePlayAuthKeyRequest
     * @Time: 2021/12/19   20:51
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getDescribeLivePlayAuthKeyRequest($DomainName)
    {
        try {

            $cred = new Credential($this->appid, $this->appsecret);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint(self::API_FUNCTION_DESCRIBELIVEDOMAINS);

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new LiveClient($cred, "", $clientProfile);
            $req = new DescribeLivePlayAuthKeyRequest();
            $params = array(
                "DomainName" => $DomainName
            );
            $req->fromJsonString(json_encode($params));
            $resp = $client->DescribeLivePlayAuthKey($req);
            return json_decode($resp->toJsonString(),true);
        }catch(TencentCloudSDKException $e) {
            self::doLogs($e->getMessage());
            $this->errMsg = $this->switchError($e->getErrorCode());
            $this->errCode = 0;
        }
    }

}