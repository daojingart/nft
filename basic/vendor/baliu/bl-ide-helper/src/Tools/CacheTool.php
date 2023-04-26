<?php

namespace Bl\Tools;

use Bl\BaLiu;

/**
 *
 * 缓存工具
 * CacheTool
 * Bl\Tools
 */
class CacheTool extends BaLiu
{
    /**
     * 加锁
     *
     * @param     $lockKey
     * @param int $expire
     * @return bool
     * @param mixed $lockKey
     */
    public function lock($lockKey, $expire = 10)
    {
    }

    /**
     * 解锁
     *
     * @param $lockKey
     * @return false|int
     * @param mixed $lockKey
     */
    public function unlock($lockKey)
    {
    }

    /**
     * 超级缓存（默认支持缓存自动预热）
     *
     * @param string  $name        缓存key
     * @param \Closure $valueFun    获取缓存数据的匿名函数或者闭包
     * @param integer $expire      缓存有效期
     * @param boolean $forceUpdate 是否强制更新缓存
     * @return mixed
     */
    public function superCache($name, $valueFun, $expire, $forceUpdate = false)
    {
    }
}
