<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 13:41
 */


namespace app\api\validate;

/**
 * 产品验证
 * Class ProductValidate
 * @package app\api\validate
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 13:41
 */
class ProductValidate extends BaseValidate
{
    protected $rule = [
        'product_id' => 'require',
        'sku_id' => 'require',
        'numberOf' => 'require',
        'address_id' => 'require',

    ];
    protected $message = [
        'product_id.require' => '产品id不能为空S',
        'sku_id.require' => '请选择商品规格',
        'numberOf.require' => '请选择兑换数量',
        'address_id.require' => '请选择收货地址'
    ];
    protected $scene = [
        'getProducyInfo' => ['product_id'],
        'preOrder' =>  ['product_id','sku_id','numberOf','address_id'],
    ];
}