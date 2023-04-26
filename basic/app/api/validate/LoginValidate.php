<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 09:51
 */


namespace app\api\validate;

/**
 * 登陆相关
 * Class Login
 * @package app\api\validate
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 09:51
 */
class LoginValidate extends BaseValidate
{
    protected $rule = [
        'phone' => 'require',
        'password' => 'require',
        'code' => 'require',
        'terminal' => 'require',
        'passwords' => 'require',
    ];
    protected $message = [
        'phone.require' => '手机号不能为空',
        'password.require' => '密码不能为空',
        'code.require' => '验证码不能为空',
        'terminal' => '登陆类型不能为空',
        'passwords' => '密码不能为空'
    ];
    protected $scene = [
        'login' => ['phone'],
        'register' => ['phone','password','code'],
        'code' => ['code','password'],
        'check' => ['code','phone'],
        'forgotPassword' => ['code','phone','password','passwords'],
    ];
}