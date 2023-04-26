<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   18:32
 * +----------------------------------------------------------------------
 * | className:  轮播图管理
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class Banner extends BaseModel
{
    protected $name = 'banner';
    protected static $is_del = 1; //软删除数据

    /**
     * 详情
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($id)
    {
        return self::get($id);
    }

}