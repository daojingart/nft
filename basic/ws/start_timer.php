<?php

use app\common\components\helpers\ConfigUtils;
use think\Env;
use \Workerman\Worker;
use \Workerman\Lib\Timer;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/common.php';

$worker = new Worker();
$worker->name="start_timer";
$worker->onWorkerStart = function()
{
    ConfigUtils::initEnv();
    $host_url = Env::get('ws.timer_host_url');
    //查询状态
    Timer::add(1,"synchronous",array($host_url));
    Timer::add(30,"againSubmit",array($host_url));
    Timer::add(60,"againAddOnlineSubmit",array($host_url));
    Timer::add(3600,"automaticLabeling",array($host_url));
};




/**
 * @Notes: 自动标签
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function automaticLabeling($host_url)
{
    $url = $host_url."/common/Queue/automaticLabeling";
    curlRequest($url);
}


/**
 * @Notes: 查询状态
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function againSubmit($host_url)
{
    $url = $host_url."/common/Queue/againSubmit";
    curlRequest($url);
}

/**
 * @Notes: 重新提交
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function againAddOnlineSubmit($host_url)
{
    $url = $host_url."/common/Queue/againAddOnlineSubmit";
    curlRequest($url);
}

/**
 * 同步汇付订单同步
 * @param $host_url
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-09-15 17:22
 */
function synchronous($host_url)
{
    $url = $host_url."/common/Queue/synchronous";
    curlRequest($url);
}



if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}


