<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 自定义区块链引擎
 * +----------------------------------------------------------------------
 */

namespace chain;

use exception\BaseException;
use think\Env;
use think\Exception;

class Driver
{
    private $config;    // 区块链配置

    private $engine;    // 当前选择的链引擎

    private $env;       //上链环境

    private $account;       //链上账户

    /**
     * 构造方法
     * Driver constructor.
     * @param $config
     * @throws Exception
     */
    public function __construct($config, $account = [])
    {
        $this->config  = $config;
        $this->env     = Env::get('app.debug', false) ? 'test' : 'online';
        $this->account = $account;
        // 实例化当前引擎
        $this->engine = $this->getEngineClass();
    }

    /**
     * 创建一个发行账号
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface createIssuedUser
     * @Time      : 2022/6/19   13:44
     */
    public function createIssuedUser($is_user = '')
    {
        return $this->engine->createIssuedUser($is_user);
    }

    /**
     * 创造数字资产[未发行状态操作]
     */
    public function createAssets($Collection)
    {
        return $this->engine->createAssets($Collection);
    }

    /**
     * 发行数字资产
     */
    public function publish($asset_id, $Collection = [])
    {
        return $this->engine->publishAssets($asset_id, $Collection);
    }

    /**
     * 查询数字资产详细信息
     */
    public function queryDetails($asset_id)
    {
        return $this->engine->queryDetails($asset_id);
    }

    /**
     * 拉取资产登记历史记录
     */
    public function historyDetails()
    {
        return $this->engine->sendSms();
    }

    /**
     * 用于资产发行者将数字资产的一个碎片授予给指定账户
     */
    public function grant($account, $asset_id, $price)
    {
        return $this->engine->grantShardAssets($account, $asset_id, $price);
    }

    /**
     * 用于由当前持有地址转移资产碎片到目标地址。
     */
    public function transfer($account, $asset_id, $share_id, $to_account, $member_id)
    {
        return $this->engine->transferShard($account, $asset_id, $share_id, $to_account, $member_id);
    }

    /**
     * 用于核销已经上链的碎片，该动作对应一个发货场景或者一个碎片销毁场景。。
     */
    public function consume($asset_id, $share_id, $account)
    {
        return $this->engine->consumeShard($asset_id, $share_id, $account);
    }

    /**
     * 用于查询指定资产碎片详细信息。
     */
    public function querysds($asset_id, $share_id)
    {
        return $this->engine->getQuerysds($asset_id, $share_id);
    }

    /**
     * 交易结果查询
     * @param $asset_id
     * @return mixed
     * @Time      : 2022/6/21   14:56
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface getWindUpDetails
     */
    public function getWindUpDetails($asset_id)
    {
        return $this->engine->getWindUpDetails($asset_id);
    }

    /**
     * 查询用户持有的随便资产
     * @return mixed
     * @Time      : 2022/6/20   14:42
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface listShardsByAddr
     */
    public function listShardsByAddr($account)
    {
        return $this->engine->listShardsByAddr($account);
    }

    /**
     * 获取存证信息
     * @return mixed
     * @Time      : 2022/6/20   14:42
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface listShardsByAddr
     */
    public function getEvidenceInfo($asset_id)
    {
        return $this->engine->getEvidenceInfo($asset_id);
    }

    /**
     * 获取百度的历史记录
     * @return mixed
     * @Time      : 2022/6/20   14:42
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface listShardsByAddr
     */
    public function getHistoryList($assetId,$page)
    {
        return $this->engine->getListAssetHistory($assetId,$page);
    }

    /**
     * 获取已经授予的资产碎片记录
     * @return mixed
     * @Time      : 2022/6/20   14:42
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface listShardsByAddr
     */
    public function getListShardsByAsset($assetId,$page)
    {
        return $this->engine->getListShardsByAsset($assetId,$page);
    }


    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->engine->getError();
    }

    /**
     * 获取当前上链的引擎
     * @return mixed
     * @throws Exception
     */
    private function getEngineClass()
    {
        $engineName = $this->config['default'];
        $classSpace = __NAMESPACE__ . '\\engine\\' . ucfirst($engineName);
        if (!class_exists($classSpace)) {
            throw new BaseException(['msg' => '未找到合约引擎类: ' . $engineName]);
        }
        $config = isset($this->config[$engineName]) ? $this->config[$engineName] : [];
        return new $classSpace($config, $this->env, $this->account);
    }
}
