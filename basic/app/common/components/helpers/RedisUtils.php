<?php


namespace app\common\components\helpers;

use Redis;


class RedisUtils
{
    /**
     * @var Redis
     */
    public static $redis;

    private static $_keyPre = 'lock:';

    /**
     * 加锁
     * @param     $lockKey
     * @param int $expire
     * @return bool
     */
    public static function lock($lockKey, int $expire = 5): bool
    {
        $lockKey    = self::$_keyPre . $lockKey;
        $expireTime = time() + $expire;

        try {
            $redis  = self::connect();
            $isLock = $redis->set($lockKey, $expireTime, ['nx', 'ex' => $expire]);
            // 不能获取锁
            if (!$isLock) {
                // 判断锁是否过期
                $lockTimeCurrent = $redis->get($lockKey);
                // 锁已过期，删除锁，重新获取
                if (time() > $lockTimeCurrent) {
                    $lockTimePre = $redis->getSet($lockKey, $expireTime);
                    if ($lockTimePre == $lockTimeCurrent) {
                        $isLock = true;
                    }
                }
            }

            if ($isLock) {
                $redis->setex($lockKey, $expire, $expireTime);
            }
        } catch (\Exception $exception) {
            $isLock = false;
        }

        return !$isLock;
    }

    /**
     * 解锁
     * @param $lockKey
     * @return false|int
     */
    public static function unlock($lockKey)
    {
        $lockKey = self::$_keyPre . $lockKey;

        try {
            return self::connect()->del($lockKey);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 获取redis句柄
     * @return Redis|object
     */
    public static function connect()
    {
        return initRedis();
    }
}