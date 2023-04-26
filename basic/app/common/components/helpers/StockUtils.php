<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/9/13   11:11
 * +----------------------------------------------------------------------
 * | className: Redis 库存工具类
 * +----------------------------------------------------------------------
 */

namespace app\common\components\helpers;

use think\Exception;

class StockUtils
{
    public static $redis;

    private static $_keyPre = 'goods_stock_number_';
    private static $_keyLockPre = 'goods_lock_stock_number_';
    private static $_soldKeyPre = 'order_stock_number_';

    /**
     * 获取藏品库存  所有的藏品、盲盒都可以使用这个方法获取库存
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getStock
     * @Time: 2022/9/13   11:28
     */
    public static function getStock(string $goods_id)
    {
        $stockKey = self::$_keyPre . $goods_id;
        $stock = self::connect()->get($stockKey);
        return $stock == null ? 0 : $stock;
    }

    /**
     * 获取已经销售的数量和库存同步
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSoldStock
     * @Time: 2022/9/16   14:38
     */
    public static function getSoldStock(string $goods_id)
    {
        $stockKey = self::$_soldKeyPre . $goods_id;
        $stock = self::connect()->get($stockKey);
        return $stock == null ? 0 : $stock;
    }

    /**
     * 获取已经销售的数量和库存同步
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSoldStock
     * @Time: 2022/9/16   14:38
     */
    public static function addSoldStock(string $goods_id,string $number)
    {
        $stockKey = self::$_soldKeyPre . $goods_id;
        if($number == '-1'){
            $stockKeyNumber = self::connect()->get($stockKey);
            if($stockKeyNumber==0){
                return 0;
            }
        }
        $stock = self::connect()->incrBy($stockKey,$number);
        return $stock == null ? 0 : $stock;
    }

    /**
     * 库存回归增加
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSoldStock
     * @Time: 2022/9/16   14:38
     */
    public static function increaseStock(string $goods_id,string $number)
    {
        $stockKey = self::$_keyPre . $goods_id;
        $stock = self::connect()->incrBy($stockKey,$number);
        return $stock == null ? 0 : $stock;
    }

    /**
     * 库存增加 更新 [为了防止编辑时有库存 增加库存]
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface addStock
     * @Time: 2022/9/13   11:29
     */
    public static function addStock(string $goods_id,string $number)
    {
        $stockKey = self::$_keyPre . $goods_id;
        $stockLockKey = self::$_keyLockPre . $goods_id;
        $isStockKey = self::connect()->exists($stockKey);
        //判断这个键是否存在 存在直接重置库存
        if($isStockKey){
            self::connect()->set($stockKey,$number);
        }
        RedisUtils::lock($stockLockKey, 10);
        try {
            //防止多次增加库存  增加库存锁
             $isStockKey = self::connect()->exists($stockKey);
            if(!$isStockKey){
                self::connect()->set($stockKey,$number);
            }
        } catch (\Exception $exception) {
            pre($exception);
            self::writeLog($exception->getMessage());
        } finally {
            RedisUtils::unlock($stockLockKey);
        }
        return $number;
    }

    /**
     * 扣除减少库存
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface deductStock
     * @Time: 2022/9/13   11:37
     */
    public static function deductStock(string $goods_id,string $number)
    {
        $stockKey = self::$_keyPre . $goods_id;
        $soldStockKey = self::$_soldKeyPre . $goods_id;
        //判断扣减的库存是否存在 不存在 则抛出异常
        $isStockKey = self::connect()->exists($stockKey);
        if(!$isStockKey){
            return false;
        }
        $stock_number = self::connect()->get($stockKey);
        if($stock_number>0){
            $newStock   = self::connect()->incrBy($stockKey, -$number);
            if ($newStock >= 0) {
                //写入已售的键 然后后期同步
                self::connect()->incrBy($soldStockKey, $number);
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 删除库存键
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface delStock
     * @Time: 2022/9/16   15:49
     */
    public static function delStock(string $goods_id)
    {
        $stockKey = self::$_keyPre . $goods_id;
        return self::connect()->del($stockKey);
    }


    /**
     * 获取redis句柄
     * @return Redis|object
     */
    public static function connect()
    {
        return initRedis();
    }

    /**
     * 写入库存日志
     * @param $message
     * @param $level
     * @Time: 2022/9/16   11:09
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface writeLog
     */
    public static function writeLog($message, $level = "INFO"){
        $log_file = date('Ymd') . '.log';
        $filePath = RUNTIME_PATH . '/stock/';
        if (!is_dir($filePath)){
            mkdir($filePath, 0755, true);
        }
        $server_addr = "127.0.0.1";
        if (isset($_SERVER["REMOTE_ADDR"])){
            $server_addr = $_SERVER["REMOTE_ADDR"];
        }
        $message_format = "[". $level ."] [".gmdate("Y-m-d\TH:i:s\Z")."] ". $server_addr." ". $message. "\n";
        $fp = fopen($log_file, "a+");
        fwrite($fp, $message_format);
        fclose($fp);
    }
}