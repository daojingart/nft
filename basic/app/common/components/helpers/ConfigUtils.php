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

class ConfigUtils
{
    /**
     * 初始化Env
     * @return void
     */
    public static function initEnv()
    {
        define('DS', DIRECTORY_SEPARATOR);
        defined('ENV_PREFIX') or define('ENV_PREFIX', 'PHP_'); // 环境变量的配置前缀
        defined('ROOT_PATH') or define('ROOT_PATH', dirname(realpath(APP_PATH)) . DS);

        // 加载环境变量配置文件
        if (is_file(ROOT_PATH . '.env')) {
            $env = parse_ini_file(ROOT_PATH . '.env', true);
            foreach ($env as $key => $val) {
                $name = ENV_PREFIX . strtoupper($key);
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $item = $name . '_' . strtoupper($k);
                        putenv("$item=$v");
                    }
                } else {
                    putenv("$name=$val");
                }
            }
        }
    }
}