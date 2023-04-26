<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/2/19   4:04 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 通知、公告
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class Search extends BaseModel
{
    protected $name = 'search';

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

    /**
     * 热词搜索
     * @return mixed
     * @throws \think\exception\DbException
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/7/11 15:04
     */
    public static function list($where)
    {
         return self::where($where)->order('sort asc')->field("title")->select();

    }
}