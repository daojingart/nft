<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 18:15
 */


namespace app\api\validate;


/**
 * 银行卡验证
 * Class CardValidate
 * @package app\api\validate
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 18:15
 */
class CardValidate extends BaseValidate
{
    protected $rule = [
        'card_no' => 'require',
        'card_name' => 'require',
        'bank' => 'require',
        'phone' => 'require',
        'code' => 'require',
    ];
    protected $message = [
        'card_no.require' => '银行卡不能为空',
        'bank.require' => '银行名不能为空',
        'card_name.require' => '持卡人姓名不能为空',
        'phone.require' => '手机号不能为空',
        'code.require' => '验证码不能为空',

    ];
    protected $scene = [
        'addCard' => ['phone','card_no','bank','card_name','code'],
    ];
}