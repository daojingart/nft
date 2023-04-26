<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/19   11:46
 * +----------------------------------------------------------------------
 * | className: 百度链
 * +----------------------------------------------------------------------
 */

namespace chain\engine;

Vendor('xasset.index');

class BD extends Server
{
    private $config;
    private $base_url;
    private $account;
    private $binPath = XASSET_PATH . 'tools/xasset-cli/xasset-cli-linux';
    private $xHandle;

    public function __construct($config, $env, $account)
    {
        parent::__construct();
        if ($env == 'test') {
            $this->config = [
                'appid' => $config['appid'],
                'AccessKey' => md5($config['AccessKey']),
                'SecretKey' => md5($config['SecretKey'])
            ];
            $this->base_url = "http://120.48.16.137:8360";
        } else {
            $this->config = $config;
            $this->base_url = "https://xuper.baidu.com";
        }
        $this->account = $account;
        $crypto = new \EcdsaCrypto($this->binPath);
        $BConfig = new \XassetConfig($crypto);
        $BConfig->setCredentials($this->config['appid'], $this->config['AccessKey'], $this->config['SecretKey']);
        $BConfig->endPoint = $this->base_url;
        $this->xHandle = new \XassetClient($BConfig);
    }

    /**
     * 创建账户
     * @return array|bool
     */
    public function createIssuedUser($user_id)
    {
         return (new \Account($this->binPath))->createAccount();
    }

    /**
     * 创建资产
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createAssets
     * @Time: 2022/6/19   21:54
     */
    public function createAssets($Collection)
    {
        $stoken = $this->xHandle->getStoken($this->account);
        //组建 生成藏品的信息
        $images = explode('.', $Collection['thumb']);
        $img_md5 = md5($images[0]);
        $arrAssetInfo = array(
            'title' => $Collection['title'],
            'asset_cate' => 2,
            'thumb' => array("bos_v1://{$stoken['response']['accessInfo']['bucket']}/{$stoken['response']['accessInfo']['object_path']}{$img_md5}.jpeg/1000_500"),
            'short_desc' => $Collection['desc'],
            'img_desc' => array("bos_v1://{$stoken['response']['accessInfo']['bucket']}/{$stoken['response']['accessInfo']['object_path']}{$img_md5}.jpeg/1000_500"),
            'asset_url' => array("bos_v1://{$stoken['response']['accessInfo']['bucket']}/{$stoken['response']['accessInfo']['object_path']}{$img_md5}.jpeg"),
        );
        $strAssetInfo = json_encode($arrAssetInfo);
        $assetId = gen_asset_id($this->config['appid']);
        $result = $this->xHandle->createAsset($this->account, $assetId, $Collection['amount'], $strAssetInfo, $Collection['price']);
        self::doBdLogs(array_merge(['title'=>"资产创建返回体--createAssets"], $result));
        return $result;
    }

    /**
     * 发行数字资产
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface publishAssets
     * @Time: 2022/6/19   22:51
     */
    public function publishAssets($asset_id)
    {
        $result =  $this->xHandle->publishAsset($this->account, $asset_id);
        self::doBdLogs(array_merge(['title'=>"发行数字资产返回体--publishAssets--{$asset_id}"], $result));
        return $result;
    }

    /**
     * 查询数字资产详情
     * @param $asset_id
     * @Time: 2022/6/19   22:55
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface queryDetails
     */
    public function queryDetails($asset_id)
    {
        $result =  $this->xHandle->queryAsset($asset_id);
        self::doBdLogs(array_merge(['title'=>"资产查询详情返回体--queryDetails--{$asset_id}"], $result));
        return $result;
    }

    /**
     * 资产授予
     * @param $account
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface grantShardAssets
     * @Time: 2022/6/20   14:18
     */
    public function grantShardAssets($account, $asset_id, $price)
    {
        $shardId = gen_asset_id($this->config['appid']);
        $result = $this->xHandle->grantShard($this->account, $asset_id, $shardId, $account['address'], $price, $account['member_id']);
        self::doBdLogs(array_merge(['title'=>"资产授予交易--grantShardAssets--{$asset_id}"], $result));
        return $result;
    }

    /**
     * 转移资产碎片
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface transferShard
     * @Time: 2022/6/20   14:27
     */
    public function transferShard($account, $asset_id,$share_id,$to_account,$member_id)
    {
        $result =  $this->xHandle->transferShard($account, $asset_id, $share_id, $to_account['address'], 0, $to_account['member_id']);
        self::doBdLogs(array_merge(['title'=>"资产转增交易--transferShard--{$asset_id}"], $result));
        return $result;
    }

    /**
     * 查询用户持有碎片
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface listShardsByAddr
     * @Time: 2022/6/20   14:43
     */
    public function listShardsByAddr($account)
    {
        return $this->xHandle->listShardsByAddr($account, 1, 10);
    }

    /**
     * 碎片查询详情
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getQuerysds
     * @Time: 2022/6/20   15:10
     */
    public function getQuerysds($asset_id,$share_id)
    {
        $result =  $this->xHandle->queryShard($asset_id, $share_id);
        self::doBdLogs(array_merge(['title'=>"碎片查询详情--getQuerysds--{$asset_id}"], $result));
        return $result;
    }


    /**
     * 销毁碎片
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface consumeShard
     * @Time: 2022/6/20   15:18
     */
    public function consumeShard($asset_id,$share_id,$account)
    {
        $result =  $this->xHandle->consumeShard($this->account,$account,$asset_id,$share_id);
        self::doBdLogs(array_merge(['title'=>"碎片销毁--consumeShard--{$asset_id}"], $result));
        return $result;
    }

    /**
     * 存证信息
     * @param $asset_id
     * @Time: 2022/6/20   15:23
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getEvidenceInfo
     */
    public function getEvidenceInfo($asset_id)
    {
        $result = $this->xHandle->getEvidenceInfo($asset_id);
        self::doBdLogs(array_merge(['title'=>"存证信息查询--getEvidenceInfo--{$asset_id}"], $result));
        return $result;
    }


    /**
     * 拉取藏品的历史资产记录
     * @param $assetId
     * @param $page
     * @return array|bool
     * @Time: 2022/10/23   15:33
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getListAssetHistory
     */
    public function getListAssetHistory($assetId,$page)
    {
         return  $this->xHandle->listAssetHistory($assetId, $page, 50);
    }

    /**
     * 分页拉取指定资产已授予碎片列表
     * @param $assetId
     * @param $page
     * @return array|bool
     * @Time: 2022/10/23   15:33
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getListAssetHistory
     */
    public function getListShardsByAsset($assetId, $cursor)
    {
        return  $this->xHandle->listShardsByAsset($assetId, $cursor, 50);
    }


}