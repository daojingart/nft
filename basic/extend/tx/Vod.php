<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2021/12/5   22:05
 * +----------------------------------------------------------------------
 * | className: 云点播相关信息
 * +----------------------------------------------------------------------
 */

namespace tx;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Vod\V20180717\Models\ApplyUploadRequest;
use TencentCloud\Vod\V20180717\VodClient;
use TencentCloud\Vod\V20180717\Models\DescribeMediaInfosRequest;
use TencentCloud\Vod\V20180717\Models\PullUploadRequest;
use TencentCloud\Vod\V20180717\Models\CreateProcedureTemplateRequest;
use TencentCloud\Vod\V20180717\Models\DescribeProcedureTemplatesRequest;
use TencentCloud\Vod\V20180717\Models\DescribeTaskDetailRequest;


class Vod extends Base
{

    /**
     * 获取云点播资源的信息
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getDescribeMediaInfos
     * @Time: 2021/12/5   22:26
     */
    public function getDescribeMediaInfos($FileIds,$Filters=true):array
    {
        try {
            $cred = new Credential($this->appid, $this->appsecret);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint(self::API_FUNCTION_DESCRIBEMEDIAINfOS);
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "", $clientProfile);
            $req = new DescribeMediaInfosRequest();
            if($Filters){
                $params = array(
                    "FileIds" => [$FileIds],
                    "Filters" => array( "metaData" )
                );
            }else{
                $params = array(
                    "FileIds" => [$FileIds],
                );
            }
            $req->fromJsonString(json_encode($params));
            return json_decode($client->DescribeMediaInfos($req)->toJsonString(),true);
        }catch(TencentCloudSDKException $e) {
            self::doLogs($e->getMessage());
            $this->errMsg = $this->switchError($e->getErrorCode());
            $this->errCode = 0;
        }
    }

    /**
     * 文件点播上传(微信的录音文件)
     * @param string $MediaFilePath
     * @Time: 2022/3/17   21:16
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface VodUploadClient
     */
    public function VodUploadClient(string $MediaFilePath,string $Names)
    {
        //先去调用判断任务流模板的信息 判断是否存在任务流 不存在创建
        $result = $this->describeProcedureTemplatesRequest($Names);
        if($result['TotalCount'] ===0 ){
            //不存在任务流 则创建这个任务流
            $this->createProcedureTemplateRequest($Names);
        }
        try {
            $cred = new Credential($this->appid, $this->appsecret);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("vod.tencentcloudapi.com");
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "", $clientProfile);
            $req = new PullUploadRequest();
            $params = array(
                "MediaUrl" => $MediaFilePath,
                "Procedure" => $Names
            );
            $req->fromJsonString(json_encode($params));
            $resp = $client->PullUpload($req);
            $result = json_decode($resp->toJsonString(),true);
            //根据TaskId  获取任务转码详情  然后根据文件的ID 直接获取最终的转码链接
            $describe = $this->describeTaskDetailRequest($result['TaskId']);
            //根据这个详情获取最终的文件ID  然后再去获取媒资详情
            return $describe['PullUploadTask']['FileId'];
        }
        catch(TencentCloudSDKException $e) {
            self::doLogs($e->getMessage());
            $this->errMsg = $this->switchError($e->getErrorCode());
            $this->errCode = 0;
        }
    }

    /**
     * 创建转码任务流模板
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createProcedureTemplateRequest
     * @Time: 2022/3/20   22:09
     */
    public function createProcedureTemplateRequest(string $Names)
    {
        try {
            $cred = new Credential($this->appid, $this->appsecret);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("vod.tencentcloudapi.com");
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "", $clientProfile);
            $req = new CreateProcedureTemplateRequest();
            if($Names == 'edu_mp3'){
                $Definition = self::Transcoding_mp3;
            }else{
                $Definition = self::Transcoding_mp4;
            }
            $params = array(
                "Name" => $Names,
                "MediaProcessTask" => array(
                    "TranscodeTaskSet" => array(
                        array(
                            "Definition" => $Definition
                        )
                    )
                )
            );
            $req->fromJsonString(json_encode($params));
            $resp = $client->CreateProcedureTemplate($req);
            return json_decode($resp->toJsonString(),true);
        }catch(TencentCloudSDKException $e) {
            pre($e);
            self::doLogs($e->getMessage());
            $this->errMsg = $this->switchError($e->getErrorCode());
            $this->errCode = 0;
        }
    }

    /**
     * 获取任务流模板
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface describeProcedureTemplatesRequest
     * @Time: 2022/3/20   22:12
     */
    public function describeProcedureTemplatesRequest(string $Names):array
    {
        try {
            $cred = new Credential($this->appid, $this->appsecret);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("vod.tencentcloudapi.com");
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "", $clientProfile);
            $req = new DescribeProcedureTemplatesRequest();
            $params = array(
                "Names" => [$Names]
            );
            $req->fromJsonString(json_encode($params));
            $resp = $client->DescribeProcedureTemplates($req);
            return json_decode($resp->toJsonString(),true);
        }
        catch(TencentCloudSDKException $e) {
            self::doLogs($e->getMessage());
            $this->errMsg = $this->switchError($e->getErrorCode());
            $this->errCode = 0;
        }
    }

    /**
     * 查询任务详情接口
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface describeTaskDetailRequest
     * @Time: 2022/3/21   11:44
     */
    public function describeTaskDetailRequest($TaskId)
    {
        try {
            $cred = new Credential($this->appid, $this->appsecret);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("vod.tencentcloudapi.com");
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "", $clientProfile);
            $req = new DescribeTaskDetailRequest();
            $params = array(
                "TaskId" => $TaskId
            );
            $req->fromJsonString(json_encode($params));
            $resp = $client->DescribeTaskDetail($req);
            return json_decode($resp->toJsonString(),true);
        }catch(TencentCloudSDKException $e) {
            echo $e;
        }
    }


}