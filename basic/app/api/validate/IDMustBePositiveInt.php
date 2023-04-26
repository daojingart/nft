<?php
namespace app\api\validate;

/**
 * 验证id
 * Class IDMustBePositiveInt
 * @package app\api\validate
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 16:37
 */
class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
    ];
    protected $message = [
        'id.require' => 'id不能为空'
    ];
}
