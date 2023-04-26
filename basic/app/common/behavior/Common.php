<?php

namespace app\common\behavior;

use think\Config;

class Common
{
    public function appInit()
    {
        (!class_exists(\Bl\BaLiu::class)) && exit("当前服务器信息已记录,请先安装八六互联安全组件,保证程序的安全运行");
        $_SERVER["BL_VERSION"] = Config::get("version");
    }


}