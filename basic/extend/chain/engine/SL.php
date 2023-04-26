<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/20   16:01
 * +----------------------------------------------------------------------
 * | className: 文昌链开发
 * +----------------------------------------------------------------------
 */

namespace chain\engine;

use phpseclib3\Crypt\Hash;

class SL extends Server
{
    private $config;
    private $base_url;
    private $account;

    public function __construct($config, $env, $account)
    {
        parent::__construct();
        $this->base_url = "https://stage.apis.avata.bianjie.ai/";
        $this->config = [
            'appid' => 'f212W0M6K0w9b1s4',
            'APIKEY' => 'm2t2z006r0L9n1d4p558K5z7G0y4I9g',
            'APISECRET' => 'n2m2O006d0V9Z164t5L8w5C7c0A4w9k',
        ];
        $this->account = $account;
    }

    /**
     * 创建账户
     * @return array|bool
     */
    public function createIssuedUser($user_id)
    {
        $body = [
            "name" => getGuidV4(),
            "operation_id" => "operationid" . $this->getMillisecond(),
        ];
        $result = $this->request("/v1beta1/account", [], $body, "POST");
        self::doWcLogs(array_merge(['title'=>"创建账户日志--createIssuedUser",'请求体'=>$body], $result));
        return $result;
    }

    /**
     * 创建一个发行资产
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createAssets
     * @Time: 2022/6/20   23:56
     */
    public function createAssets($Collection)
    {
        $body = [
            "name" => $Collection['title'],
            "class_id" => 'op'.getGuidV4().$Collection['goods_id'].$Collection['member_id'],
            "symbol" => getGuidV4(),
            "description" => $Collection['desc'],
            "uri" => $Collection['thumb'],
            "uri_hash" => hash("sha256",$Collection['thumb']),
            "data" => $Collection['thumb'],
            "owner" => $this->account['address'],
            "tag" => [
                "amount"=>$Collection['price'],
            ],
            "operation_id" => "operationid" . $this->getMillisecond(),
        ];
        $result = $this->request("/v1beta1/nft/classes", [], $body, "POST");
        self::doWcLogs(array_merge(['title'=>"创建发行资产--createAssets",'请求体'=>$body], $result));
        if(isset($result['error'])){
            return $this->returnMsg($result['error']['message']);
        }
        return [
            'response' => [
                'errno' => 0,
                'asset_id' => $body['class_id'],
                'operation_id' => $body['operation_id'],
            ],
        ];
    }


    /**
     * 发行数字资产
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface publishAssets
     * @Time: 2022/6/21   11:11
     */
    public function publishAssets($asset_id,$Collection)
    {
        $body = [
            "name" => $Collection['title'],
            "uri" => $Collection['thumb'],
            "uri_hash" => hash("sha256",$Collection['thumb']),
            "data" => $Collection['thumb'],
            "recipient" => $this->account['address'],
            "tag" => [
                "amount"=> $Collection['price'],
            ],
            "operation_id" => "operationid" . $this->getMillisecond(),
        ];
        $result = $this->request("/v1beta1/nft/nfts/{$asset_id}", [], $body, "POST");
        self::doWcLogs(array_merge(['title'=>"发行数字资产--publishAssets",'请求体'=>$body], $result));
        if(isset($result['error'])){
            return $this->returnMsg($result['error']['message']);
        }
        return [
            'response' => [
                'errno' => 0,
                'asset_id' => $asset_id,
                'operation_id' => $body['operation_id'],
            ],
        ];
    }

    /**
     * 查询资产的详情
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface {id}
     * @Time: 2022/6/21   11:20
     */
    public function queryDetails($asset_id)
    {
        $result = $this->request("/v1beta1/nft/classes/{$asset_id}", [], [], "GET");
        self::doWcLogs(array_merge(['title'=>"查询数字资产--queryDetails",'请求体'=>$asset_id], $result));
        return $result;

    }

    /**
     * 转增碎片管理
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface grantShardAssets
     * @Time: 2022/6/21   11:51
     *
     */
    public function transferShard($account, $asset_id, $share_id, $to_account, $member_id)
    {
        $body = [
            'recipient' => $to_account['address'],
            'operation_id' => "operationid" . $this->getMillisecond(),
            'tag' => [
                "327823"=>"tag2221",
            ]
        ];
        $result =  $this->request("/v1beta1/nft/nft-transfers/{$asset_id}/{$account['address']}/{$share_id}", [],$body, "POST");
        self::doWcLogs(array_merge(['title'=>"转增日志请求--transferShard",'请求体'=>$body], $result));
        if(isset($result['error'])){
            return $this->returnMsg($result['error']['message']);
        }
        return [
            'response' => [
                'errno' => 0,
                'asset_id' => $asset_id,
                'operation_id' => $body['operation_id'],
            ],
        ];
    }

    /**
     * 查询用户持有的交易记录
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface listShardsByAddr
     * @Time: 2022/6/21   16:12
     */
    public function listShardsByAddr($account)
    {
        $body = [
            'offset' => '0',
            'limit' => '50',
            'module' => 'nft',
            'account' => $account,
            'operation_id' => "operationid" . $this->getMillisecond(),
        ];
        return $this->request("/v1beta1/accounts/history", $body,[], "GET");
    }

    /**
     * 交易结果查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getWindUpDetails
     * @Time: 2022/6/21   14:57
     *
     */
    public function getWindUpDetails($asset_id)
    {
        //查询详情 然后查询上链的状态
        $result =  $this->request("/v1beta1/tx/{$asset_id}", [], [], "GET");
        self::doWcLogs(array_merge(['title'=>"交易结果查询日志--getWindUpDetails",'请求体'=>$asset_id], $result));
        if(isset($result['error'])){
            return $this->returnMsg($result['error']['message']);
        }
        return [
            'response' => [
                'errno' => 0,
                'data' => $result,
            ],
        ];
    }

    /**
     *  销毁 NFT
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface consumeShard
     * @Time: 2022/6/21   16:24
     *
     */
    public function consumeShard($asset_id, $share_id, $account)
    {
        $body = [
            'operation_id' => "operationid" . $this->getMillisecond(),
        ];
        $result = $this->request("/v1beta1/nft/nfts/{$asset_id}/{$account['address']}/{$share_id}", [], $body, "DELETE");
        self::doWcLogs(array_merge(['title'=>"销毁数字藏品--consumeShard",'请求体'=>$body], $result));
        if(isset($result['error'])){
            return $this->returnMsg($result['error']['message']);
        }
        return [
            'response' => [
                'errno' => 0,
                'data' => $result,
            ],
        ];
    }

    /*
     * 查询 NFT 详情
     */
    public function getQuerysds()
    {

    }


    /**
     * 封装的请求
     * @param $path
     * @param $query
     * @param $body
     * @param $method
     * @return mixed
     * @Time: 2022/6/20   19:11
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface request
     */
    public function request($path, $query = [], $body = [], $method = 'GET')
    {
        $method = strtoupper($method);
        $apiGateway = rtrim($this->base_url, '/') . '/' . ltrim($path,
                '/') . ($query ? '?' . http_build_query($query) : '');
        $timestamp = $this->getMillisecond();
        $params = ["path_url" => $path];

        if ($query) {
            // 组装 query
            foreach ($query as $k => $v) {
                $params["query_{$k}"] = $v;
            }
        }
        if ($body) {
            // 组装 post body
            foreach ($body as $k => $v) {
                $params["body_{$k}"] = $v;
            }
        }
        // 数组递归排序
        $this->SortAll($params);
        $hexHash = hash("sha256", "{$timestamp}" . $this->config['APISECRET']);
        if (count($params) > 0) {
            // 序列化且不编码
            $s = json_encode($params,JSON_UNESCAPED_UNICODE);
            $hexHash = hash("sha256", stripcslashes($s . "{$timestamp}" . $this->config['APISECRET']));
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiGateway);
        $header = [
            "Content-Type:application/json",
            "X-Api-Key:{$this->config['APIKEY']}",
            "X-Signature:{$hexHash}",
            "X-Timestamp:{$timestamp}",
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $jsonStr = $body ? json_encode($body) : ''; //转换为json格式
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($jsonStr) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
            }
        } elseif ($method == 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            if ($jsonStr) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
            }
        } elseif ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($jsonStr) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
            }
        } elseif ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if ($jsonStr) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
            }
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;

    }

    /**
     * 签名算法的排序
     * @param $params
     * @Time: 2022/6/20   19:11
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface SortAll
     */
    public function SortAll(&$params){
        if (is_array($params)) {
            ksort($params);
        }
        foreach ($params as &$v){
            if (is_array($v)) {
                $this->SortAll($v);
            }
        }
    }



    /** get timestamp
     *
     * @return float
     */
    private function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)));
    }

    public function returnMsg($message)
    {
        return [
            'response' => [
                'errno' => 1,
                'msg' =>$message,
                'type' => 'WC'
            ],
        ];
    }

}