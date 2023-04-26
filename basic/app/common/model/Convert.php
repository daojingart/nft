<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   11:53
 * +----------------------------------------------------------------------
 * | className: 藏品兑换码管理
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class Convert extends BaseModel
{
    protected $name = 'goods_convert';

    /**
     * 获取详情
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($id)
    {
        return self::get($id);
    }

}