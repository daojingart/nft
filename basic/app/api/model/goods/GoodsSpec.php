<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/12   6:14 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 商品规格模型
 * +----------------------------------------------------------------------
 */
namespace app\api\model\goods;

use app\common\model\ProductSpec as GoodsSpecModel;


class GoodsSpec extends GoodsSpecModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'app_id',
        'create_time',
        'update_time'
    ];


}