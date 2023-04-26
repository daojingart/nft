<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 服务商引擎抽象类
 * +----------------------------------------------------------------------
 */

namespace chain\engine;

use exception\BaseException;
use think\Exception;
use think\Request;

abstract class Server
{
    protected $phone;  //手机号
    protected $error;

    /**
     * 构造函数
     * Server constructor.
     * @throws Exception
     */
    protected function __construct()
    {
    }

    /**
     * 创建账户
     * @return mixed
     */
    abstract protected function createIssuedUser($user_id);



    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @Notes: 记录日志错误日志的
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doBdLogs($values)
    {
        return write_log($values,RUNTIME_PATH.'/baidu');
    }


    /**
     * @Notes: 记录日志错误日志的
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doWcLogs($values)
    {
        return write_log($values,RUNTIME_PATH.'/wc');
    }

    /**
     * @Notes: 记录日志错误日志的
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doThLogs($values)
    {
        return write_log($values,RUNTIME_PATH.'/TH');
    }
}
