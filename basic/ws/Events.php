<?php
use GatewayWorker\Lib\Gateway;

class Events
{
    public static $redis = null;
    public static $db = null;
    //初始化静态变量redis

    /**
     * @Notes: 每个进程生命周期内都只会触发一次。
     * @Interface onWorkerStart
     * @param $worker
     * @author: Mr.Wang
     * @copyright: 樂天購有限公司
     * @Time: 2021/6/27   16:46
     */
    public static function onWorkerStart($worker)
    {

    }

    /**
     * @Notes: 当客户端连接上gateway进程时(TCP三次握手完毕时)触发的回调函数。
     * @Interface onConnect
     * @param $client_id
     * @author: Mr.Wang
     * @copyright: 樂天購有限公司
     * @Time: 2021/6/27   16:47
     */
    public static function onConnect($client_id)
    {
    }

    /**
     * @Notes: 当客户端发来数据(Gateway进程收到数据)后触发的回调函数
     * @Interface onMessage
     * @param $client_id
     * @param $message
     * @author: Mr.Wang
     * @copyright: 樂天購有限公司
     * @Time: 2021/6/27   16:48
     */
    public static function onMessage($client_id, $message)
   {

   }

}
