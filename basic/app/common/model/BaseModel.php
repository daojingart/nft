<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/13   3:34 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 模型基类
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

use think\Config;
use think\Db;
use think\Model;
use think\Request;
use think\Session;

class BaseModel extends Model
{


    public static $app_id;
    public static $base_url;

    /**
     * 模型基类初始化
     */
    public static function init()
    {
        parent::init();
        // 获取当前域名
        if(!IS_CLI){
            self::$base_url = HOST."/";
            // 后期静态绑定wxapp_id
            self::bindBlappId(get_called_class());
        }
    }

    /**
     * @Notes     : 用于定义全局查询范围的app_id条件
     * @Interface bindBlappId
     * @param $calledClass
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   3:42 下午
     */
    private static function bindBlappId($calledClass)
    {
        $class = [];
        if (preg_match('/app\\\(\w+)/', $calledClass, $class)) {
            $callfunc = 'set' . ucfirst($class[1]) . 'appId';
            method_exists(new self, $callfunc) && self::$callfunc();
        }
    }

    /**
     * @Notes     : 设置app_id admin模块
     * @Interface setStoreBlappId
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   3:42 下午
     */
    protected static function setAdminAppId()
    {
        $session = Session::get('blhlstore');
        !empty($session) && self::$app_id = $session['blapp']['app_id'];
    }

    /**
     * @Notes     : 设置app_id API 模块
     * @Interface setApiAppId
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   3:44 下午
     */
    protected static function setApiAppId()
    {
        $request = Request::instance();
        if (!$request->param('app_id')) {
            self::$app_id = "10001";
        } else {
            self::$app_id = $request->param('app_id');
        }
    }

    /**
     * @Notes     : 设置common API 模块
     * @Interface setApiAppId
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   3:44 下午
     */
    protected static function setCommonAppId()
    {
        self::$app_id = "10001";
    }

    /**
     * @Notes     : 设置common API 模块
     * @Interface setApiAppId
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   3:44 下午
     */
    protected static function setQueueAppId()
    {
        self::$app_id = "10001";
    }

    /**
     * @Notes     : 设置common API 模块
     * @Interface setApiAppId
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   3:44 下午
     */
    protected static function setNoticeAppId()
    {
        self::$app_id = "10001";
    }


    /**
     * @Notes     : 获取当前域名
     * @Interface baseUrl
     * @return string
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   3:44 下午
     */
    protected static function baseUrl()
    {
        $request = Request::instance();
        $host    = $request->scheme() . '://' . $request->host();
        $dirname = dirname($request->baseUrl());
        return empty($dirname) ? $host : $host . $dirname . '/';
    }

    /**
     * @Notes     : 定义全局的查询范围
     * @Interface base
     * @param $query
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   3:44 下午
     */
    protected function base($query)
    {
        if (self::$app_id > 0) {
            $query->where($query->getTable() . '.app_id', self::$app_id);
        }
    }

    /**
     * 重写父类方法--创建模型的查询对象
     *
     * @return BaseQuery
     * @throws \think\Exception
     * @author    Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time      2021/12/7 11:40
     */
    protected function buildQuery()
    {
        // 合并数据库配置
        if (!empty($this->connection)) {
            if (is_array($this->connection)) {
                $connection = array_merge(Config::get('database'), $this->connection);
            } else {
                $connection = $this->connection;
            }
        } else {
            $connection = [];
        }

        $con = Db::connect($connection);
        // 设置当前模型 确保查询返回模型对象
        $query = new BaseQuery($con, $this);
        if (isset(static::$readMaster['*']) || isset(static::$readMaster[$this->class])) {
            $query->master(true);
        }
        // 设置当前数据表和模型名
        if (!empty($this->table)) {
            $query->setTable($this->table);
        } else {
            $query->name($this->name);
        }

        if (!empty($this->pk)) {
            $query->pk($this->pk);
        }

        return $query;
    }

}
