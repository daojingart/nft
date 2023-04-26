<?php

use app\common\components\helpers\ConfigUtils;
use think\Env;
use \Workerman\Worker;
use \Workerman\Lib\Timer;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/common.php';

$worker = new Worker();
$worker->name="start_sd_timer";
$worker->onWorkerStart = function()
{
    ConfigUtils::initEnv();
    $host_url = Env::get('ws.timer_host_url');
    //查询状态
    Timer::add(60,"sdCloudAccount",array($host_url));
    Timer::add(60,"sdGetPayStatus",array($host_url));
};


/**
 * @Notes: 杉德云钱包
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function sdCloudAccount($host_url)
{
    $url = $host_url."/common/Queue/sdCloudAccount";
    curlRequest($url);
}

/**
 * @Notes: 杉德亏阿杰
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function sdGetPayStatus($host_url)
{
    $url = $host_url."/common/Queue/sdGetPayStatus";
    curlRequest($url);
}


if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}


