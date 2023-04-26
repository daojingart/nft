<?php

namespace app\common\traits;

trait InstanceModel
{

    /**
     * @var null|self 实例对象
     */
    protected static $instance = null;

    /**
     * 获取实例
     * @param array $options 实例配置
     * @return self
     */
    public static function instance($options = [])
    {
        return self::$instance = is_null(self::$instance) ? new self($options) : self::$instance;
    }

}