<?php

namespace app\common\traits;

trait Instance
{

    protected static $instance = null;

    /**
     * 获取实例
     * @return self
     */
    public static function instance(): self
    {
        return self::$instance = is_null(self::$instance) ? new self() : self::$instance;
    }

}