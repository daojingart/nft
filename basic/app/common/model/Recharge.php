<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:   2021/6/24   10:13 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 充值规格模型
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class Recharge extends BaseModel
{
    protected $name = 'recharge';
    protected $disabled = '0';

    /**
     * 获取全部
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static  function getAll()
    {
        return self::where(['disabled'=>'0'])->order(['ecard_id' => 'asc'])->select();
    }
}