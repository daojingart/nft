<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: liubinghong
 * +----------------------------------------------------------------------
 * | Time:  2021/12/6   15:44
 * +----------------------------------------------------------------------
 * | Email: 18368808324@163.com
 * +----------------------------------------------------------------------
 * | Created by PhpStorm.
 *
 * +----------------------------------------------------------------------
 */

namespace app\common\server\base;


use app\common\model\BaseQuery;
use think\db\Expression;
use think\db\Query;
use think\Session;

class BaseService
{
    /**
     * @var BaseService $_instance ;
     */
    private static $_instance;
    /**
     * @var array 当前登录用户
     */
    public static $userInfo;
    /**
     * @var null|int 当前登录appId
     */
    public static $appId;
    /**
     * @var null 当前登录用户
     */
    public static $memberId;
    /**
     * @var null|int 当前登录用户
     */
    public static $storeUserId;

    /**
     * @var null|int 当前登录用户
     */
    public static $storeUserName;

    public static $error;
    /**
     * 构造方法
     */
    public function __construct()
    {
        // 商家登录信息
        self::$userInfo = Session::get('blhlstore');
        self::$appId = self::$userInfo['user']['app_id'] ?? '10001';
        self::$memberId = self::$userInfo['user']['member_id'] ?? 0;
        self::$storeUserId = self::$userInfo['user']['store_user_id'] ?? 0;
        self::$storeUserName = self::$userInfo['user']['user_name'] ?? 'admin';
    }

    /**
     * service统一访问接口
     *
     * @return static
     * @author xyq
     */
    final public static function service()
    {
        $class = get_called_class();
        if (!isset(self::$_instance[$class]) || !(self::$_instance[$class] instanceof BaseService)) {
            self::$_instance[$class] = new static();
        }
        return self::$_instance[$class];
    }

    /**
     * 设置登录后的用户信息
     *
     * @param array $userInfo
     * @author xyq
     */
    final public static function setUserInfo(array $userInfo)
    {
        self::$userInfo = $userInfo;
        if (isset($userInfo['user']['app_id']) && $userInfo['user']['app_id'] > 0) {
            self::$appId = intval($userInfo['user']['app_id']);
        }
        if (isset($userInfo['user']['member_id']) && $userInfo['user']['member_id'] > 0) {
            self::$memberId = intval($userInfo['user']['member_id']);
        }
        if (isset($userInfo['user']['store_user_id']) && $userInfo['user']['store_user_id'] > 0) {
            self::$storeUserId = intval($userInfo['user']['store_user_id']);
        }
    }

    /**
     * 前后端分页页码方法
     *
     * @param $obj
     * @param int $page
     * @param int $limit
     * @param string $column
     * @return array
     * @throws \think\Exception
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2021/12/21 13:54
     */
    final protected function page($obj, $page = 1, $limit = 10, $column = '*')
    {
        if ($obj instanceof BaseQuery) {
            $itemCount = $obj->getCount();
        } else if ($obj instanceof Query) {
            $itemCount = $obj->count(new Expression($column));
        } else {
            $itemCount = 0;
        }
        $pageCount = ceil($itemCount / $limit);
        $pageCount = ($pageCount <= 0) ? 1 : $pageCount;
        return ['current' => $page, 'total' => $pageCount, 'count' => $itemCount];
    }

    /**
     * 前后端分页数据
     *
     * @param $obj
     * @param int $page
     * @param int $limit
     * @return array|bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2021/12/6 15:59
     * @author Mr.Liu
     */
    final protected function pagingList($obj, $page = 1, $limit = 10)
    {
        $offset = ($page > 1 ? (($page - 1) * $limit) : 0);
        if ($obj instanceof Query) {
            $itemList = $obj->limit($offset,$limit)->select();
        } else {
            $itemList = [];
        }
        return $itemList;
    }

    /**
     * 前后端分页数据-获取所有数据
     *
     * @param $obj
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2021/12/21 11:38
     * @author Mr.Liu
     */
    final protected function getAllList($obj)
    {
        if ($obj instanceof Query) {
            $itemList = $obj->select();
        } else {
            $itemList = [];
        }
        return $itemList;
    }

}