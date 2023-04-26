<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 15:42
 */


namespace app\common\model;

/**
 * 订单产品表
 * Class ProductOrderGoods
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 15:42
 */
class ProductOrderGoods extends BaseModel
{
    protected $name = 'product_order_goods';
    public function spec()
    {
        return $this->hasMany('app\common\model\ProductSpec','spec_sku_id','sku_id');
    }
}