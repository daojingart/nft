<?php

use app\common\components\helpers\ConfigUtils;
use think\Env;
use \Workerman\Worker;
use \Workerman\Lib\Timer;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/common.php';

$worker = new Worker();
$worker->name="start_goods_timer";
$worker->onWorkerStart = function()
{
    ConfigUtils::initEnv();
    $host_url = Env::get('ws.timer_host_url');
    //查询状态
    Timer::add(1,"synchronizeGoods",array($host_url));
    Timer::add(1,"lotteryDraw",array($host_url));
};

/**
 * @Notes: 同步库存状态
 * @Interface coinLog2Mysql
 * @author: Mr.Wang
 * @Time: 2021/7/6   09:56
 * @url /notice/Notify/openTeam
 */
function synchronizeGoods($host_url)
{
    $url = $host_url."/common/Queue/synchronizeGoods";
    curlRequest($url);
}

/**
 * 抽签开奖
 * @author: [Mr.Zhang] [1040657944@qq.com]
 * @Interface lotteryDraw
 * @Time: 2022/8/4   15:18
 */
function lotteryDraw($host_url)
{
    $url = $host_url."/common/task/startSubscription";
    curlRequest($url);
}




if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}


