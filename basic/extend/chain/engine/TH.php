<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/11   23:39
 * +----------------------------------------------------------------------
 * | className: 天河链接
 * +----------------------------------------------------------------------
 */

namespace chain\engine;

use exception\BaseException;

class TH extends Server
{
    private $config;
    private $base_url;
    private $account;

    public function __construct($config, $env, $account)
    {
        parent::__construct();
        if ($env == 'test') {
            $this->base_url = "https://test.api.tichain.tianhecloud.com/";
        } else {
            $this->base_url = "https://api.tichain.tianhecloud.com/";
        }
        $this->config = $config;
        $this->account = $account;
    }

    /**
     * 创建用户账户
     * @return mixed|void
     * @Time: 2022/8/11   23:59
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createIssuedUser
     */
    public function createIssuedUser($user_id)
    {
        $userKey = blhlhash($user_id);
        $params = [
            'appId' => $this->config['APIKEY'],
            'appKey' => $this->config['APISECRET'],
            'userId' => $user_id,
            'userKey' => $userKey
        ];
        $request = $this->curlRequest('api/v2/user',json_encode($params));
        $requestArray = json_decode($request,true);
        self::doThLogs(['title'=>"创建账户日志--createIssuedUser",'请求体'=>$params,'返回内容'=>$requestArray]);
        if($requestArray['code']!=0){
            if($requestArray['code'] == '802000'){
                //查询信息
                return $this->getUserDetails([
                    'member_id' => $user_id,
                    'userKey' => $userKey
                ]);
            }
            return $this->returnMsg($requestArray['message']);
        }
        return [
            'address' => $requestArray['data']['pubKey']['address'],
            'userKey' => $params['userKey']
        ];
    }

    /**
     * 数字商品发行 [后台添加完藏品;执行上链成功后才可以前端展示]
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createAssets
     * @Time: 2022/8/11   23:56
     * 0xef9712ab131624bd97daa920591ae17d299562bb
     */
    public function createAssets($Collection)
    {
        $params = [
            'appId' => $this->config['APIKEY'],
            'appKey' => $this->config['APISECRET'],
            'userId' => $Collection['member_id'],
            'userKey' => $this->account['userKey'],
            'name' => $Collection['title'],
            'pieceCount' => $Collection['amount'],
            'initPrice' => $Collection['price']
        ];
        if (!empty($Collection['contract_address'])) {
            $params['contractAddress'] = $Collection['contract_address'];
            $params['baseTokenId'] = (int)$Collection['base_token_id'];
        }
        $request = $this->curlRequest('api/v2/nfr/publish',json_encode($params));
        $requestArray = json_decode($request,true);
        self::doThLogs(['title'=>"数字商品发行日志--createAssets",'请求体'=>$params,'返回内容'=>$requestArray]);
        if($requestArray['code']!=0 && $requestArray['code']!='400'){
            return $this->returnMsg($requestArray['message']);
        }
        if($requestArray['code']=='400'){
            return $this->returnMsg($requestArray['msg']);
        }
        return $requestArray['data'];
    }

    /**
     * 执行数字藏品的交易[转增]
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface publishAssets
     * @Time: 2022/8/12   01:24
     */
    public function transferShard($account,$asset_id,$share_id,$to_account,$member_id)
    {
        $params = [
            'appId' => $this->config['APIKEY'],
            'appKey' => $this->config['APISECRET'],
            'userId' => $this->account['member_id'],
            'userKey' => $this->account['userKey'],
            'contractAddress' => $share_id, //藏品的合约地址
            'tokenId' => $asset_id,
            'from' => $account,
            'to' => $to_account,
        ];
        $request = $this->curlRequest('api/v2/nfr/transfer',json_encode($params));
        $requestArray = json_decode($request,true);
        self::doThLogs(['title'=>"执行数字藏品的交易--transferShard",'请求体'=>$params,'返回内容'=>$requestArray]);
        if($requestArray['code']!=0 && $requestArray['code']!='400'){
            return $this->returnMsg($requestArray['message']);
        }
        if($requestArray['code']=='400'){
            return $this->returnMsg($requestArray['msg']);
        }
        return [
            'transactionHash' => $requestArray['data']['transactionHash'],
        ];
    }

    /**
     * 销毁数字藏品
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface consumeShard
     * @Time: 2022/8/12   16:32
     */
    public function consumeShard($contractAddress, $tokenId)
    {
        $params = [
            'appId' => $this->config['APIKEY'],
            'appKey' => $this->config['APISECRET'],
            'userId' => $this->account['member_id'],
            'userKey' => $this->account['userKey'],
            'contractAddress' => $contractAddress, //藏品的合约地址
            'tokenId' => $tokenId,
        ];
        $request = $this->curlRequest('api/v2/nfr/burn',json_encode($params));
        $requestArray = json_decode($request,true);
        self::doThLogs(['title'=>"销毁数字藏品--consumeShard",'请求体'=>$params,'返回内容'=>$requestArray]);
        if($requestArray['code']!=0 && $requestArray['code']!='400'){
            return $this->returnMsg($requestArray['message']);
        }
        if($requestArray['code']=='400'){
            return $this->returnMsg($requestArray['msg']);
        }
        return true;
    }


    /**
     * 交易详情查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getWindUpDetails
     * @Time: 2022/8/12   09:56
     */
    public function getWindUpDetails($Collection)
    {
        $params = [
            'appId' => $this->config['APIKEY'],
            'appKey' => $this->config['APISECRET'],
            'userId' => $this->account['member_id'],
            'userKey' => $this->account['userKey'],
            'transactionHash' => $Collection,
            'methodName' => $this->account['methodName']
        ];
        $request = $this->curlRequest('api/v2/transaction/detail',json_encode($params));
        $requestArray = json_decode($request,true);
        self::doThLogs(['title'=>"交易结果查询--getWindUpDetails",'请求体'=>$params,'返回内容'=>$requestArray]);
        if($requestArray['code']!=0 && $requestArray['code']!='400'){
            return $this->returnMsg($requestArray['message']);
        }
        if(isset($requestArray['data']['isSuccess'])){
            return [
                'tokenId' =>$requestArray['data']['tokenId'],
                'hash' => $Collection,
                'isSuccess' => 1,
            ];
        }
        return false;
    }

    /**
     * 用户账号查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getWindUpDetails
     * @Time: 2022/8/12   09:56
     */
    public function getUserDetails($Collection)
    {
        $params = [
            'appId' => $this->config['APIKEY'],
            'appKey' => $this->config['APISECRET'],
            'userId' => $Collection['member_id'],
            'userKey' => $Collection['userKey'],
        ];
        $request = $this->curlRequest('api/v2/user/info',json_encode($params));
        $requestArray = json_decode($request,true);
        self::doThLogs(['title'=>"用户账号查询--getUserDetails",'请求体'=>$params,'返回内容'=>$requestArray]);
        if($requestArray['code']!=0 && $requestArray['code']!='400'){
            return $this->returnMsg($requestArray['message']);
        }
        return [
            'address' =>$requestArray['data']['address'],
            'userKey' => $Collection['userKey'],
        ];
    }

    /**
     * 统一返回
     * @param $message
     * @return array[]
     * @Time: 2022/8/12   00:26
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface returnMsg
     */
    public function returnMsg($message)
    {
        throw new BaseException(['msg' => $message."天河"]);
    }

    /**
     * 请求日志
     * @param $url
     * @param $data
     * @return bool|string
     * @Time: 2022/8/12   00:26
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface curlRequest
     */
    public function curlRequest($url,$data = ''){
        $ch = curl_init();
        $params[CURLOPT_HTTPHEADER] = [
            'Content-Type: application/json; charset=utf-8',
        ];    //请求url地址
        $params[CURLOPT_URL] = $this->base_url.$url;    //请求url地址
        $params[CURLOPT_HEADER] = false; //是否返回响应头信息
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
        $params[CURLOPT_TIMEOUT] = 30; //超时时间

        if(!empty($data)){
            $params[CURLOPT_POST] = true;
            $params[CURLOPT_POSTFIELDS] = $data;
        }
        $params[CURLOPT_SSL_VERIFYPEER] = false;//请求https时设置,还有其他解决方案
        $params[CURLOPT_SSL_VERIFYHOST] = false;//请求https时,其他方案查看其他博文
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }


}