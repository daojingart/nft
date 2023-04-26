<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/7/7   14:26
 * +----------------------------------------------------------------------
 * | className: 上链队列
 * +----------------------------------------------------------------------
 */

use app\common\components\helpers\ConfigUtils;
use think\Env;
use \Workerman\Worker;
use \Workerman\Lib\Timer;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/common.php';

$worker = new Worker();
$worker->name="start_blockchain_timer";
$worker->onWorkerStart = function()
{
    ConfigUtils::initEnv();
    $host_url = Env::get('ws.timer_host_url');
    //查询状态 获取上链详情数据
    Timer::add(4,"getDetailsExecute",array($host_url));
    //上链铸造 提交上链
    Timer::add(2,"castingImplement",array($host_url));
    //上链执行转增逻辑
    Timer::add(3,"increaseImplement",array($host_url));
    //上链执行销毁
    Timer::add(1,"destroyExecute",array($host_url));
};


/**
 * @Notes: 天河链同步
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function thQueryTransfer($host_url)
{
    $url = $host_url."/common/Queue/thQueryTransfer";
    curlRequest($url);
}


/**
 * @Notes: 查询上链状态 获取HASH 值数据
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function getDetailsExecute($host_url)
{
    $url = $host_url."/common/Queue/getDetailsExecute";
    curlRequest($url);
}

/**
 * @Notes: 查询状态
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function destroyExecute($host_url)
{
    $url = $host_url."/common/Queue/destroyExecute";
    curlRequest($url);
}

/**
 * @Notes: 上链铸造
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function castingImplement($host_url)
{
    $url = $host_url."/common/Queue/castingQueueExecute";
    curlRequest($url);
}

/**
 * @Notes: 转增
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function increaseImplement($host_url)
{
    $url = $host_url."/common/Queue/increaseQueueExecute";
    curlRequest($url);
}


if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}


