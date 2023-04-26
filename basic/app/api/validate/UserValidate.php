<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 14:46
 */


namespace app\api\validate;

/**
 * 用户相关验证
 * Class UserValidate
 * @package app\api\validate
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 14:46
 */
class UserValidate extends BaseValidate
{
    protected $rule = [
        'avatarUrl' => 'require',
        'name' => 'require',
        'password' => 'require',
        'passwords' => 'require',
        'card' => 'require',
        'phone' => 'require',
        'code' => 'require',
    ];
    protected $message = [
        'avatarUrl.require' => '头像不能为空',
        'name.require' => '昵称不能为空',
        'password.require' => '密码不能为空',
        'passwords.require' => '重复密码不能为空',
        'card.require' => '身份证不能为空',
        'phone.require' => '手机号不能为空',
        'code.require' => '验证码不能为空',
    ];

    protected $scene = [
        'avatarUrl' => ['avatarUrl'],
        'name' => ['name'],
        'password' => ['password','passwords'],
        'card' => ['name','card','phone','code'],
        'phone' =>['phone']
    ];
}