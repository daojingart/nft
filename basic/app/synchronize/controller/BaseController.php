<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/3/24   10:07
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\synchronize\controller;

use think\Controller;
use think\Db;
use think\Request;

class BaseController extends Controller
{
    protected $connection = [
        // 数据库类型
        'type'        => 'mysql',
        // 数据库连接DSN配置
        'dsn'         => '',
        // 服务器地址
        'hostname'    => 'rm-m5e584wz0o1q7agb49o.mysql.rds.aliyuncs.com',
        // 数据库名
        'database'    => 'test_nft_admin',
        // 数据库用户名
        'username'    => 'mas',
        // 数据库密码
        'password'    => '$Tr$%Z^&eZF$Ek2%',
        // 数据库连接端口
        'hostport'    => '3306',
        // 数据库连接参数
        'params'      => [],
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 数据库表前缀
        'prefix'      => 'snake_',
    ];
    protected $db_app;

    public function __construct(Request $request = null)
    {
        $this->db_app = Db::connect($this->connection);
    }

}