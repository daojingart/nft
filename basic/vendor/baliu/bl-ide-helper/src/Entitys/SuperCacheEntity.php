<?php

namespace Bl\Entitys;

use Bl\BaLiu;

/**
 * 超级缓存辅助类
 */
class SuperCacheEntity extends BaLiu
{

    protected $needCache = false;

    protected $data = null;

    protected $ttl = 0;

    /**
     * @return bool
     */
    public function isNeedCache()
    {
    }

    /**
     * 设置是否需要缓存
     *
     * @param bool $needCache
     * @return $this
     */
    public function setNeedCache($needCache)
    {
    }

    /**
     * 缓存数据
     *
     * @return mixed
     */
    public function getData()
    {
    }

    /**
     * 设置数据
     *
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
    }

    /**
     * 获取缓存有效期
     *
     * @return int
     */
    public function getTtl()
    {
    }

    /**
     * 设置缓存有效期
     *
     * @param int $ttl
     * @return $this
     */
    public function setTtl(int $ttl)
    {
    }
}
