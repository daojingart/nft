<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/11   17:39
 * +----------------------------------------------------------------------
 * | className:  藏品管理
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class Goods extends BaseModel
{
    protected $name = 'goods';
    protected static $is_del = 1; //软删除数据

    /**
     * 获取商品详情
     * @param $goods_id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($goods_id)
    {
        return self::get($goods_id,['category','writer']);
    }

    /**
     * 关联商品分类表
     * @return \think\model\relation\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('app\admin\model\collection\Category');
    }

    /**
     * 关联作家表
     * @return \think\model\relation\BelongsTo
     */
    public function writer()
    {
        return $this->belongsTo('app\common\model\Writer');
    }

    /**
     * 藏品状态
     * @param $value
     * @return mixed
     */
    public function getGoodsStatusAttr($value)
    {
        $status = [10 => '显示', 20 => '隐藏'];
        return ['text' => $status[$value], 'value' => $value];
    }
}