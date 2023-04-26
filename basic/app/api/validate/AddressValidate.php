<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 15:51
 */


namespace app\api\validate;

/**
 * 地址验证
 * Class AddressValidate
 * @package app\api\validate
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 15:52
 */
class AddressValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'phone' => 'require',
        'region' => 'require',
        'detail' => 'require',
        'select_status' => 'require',
        'id' => 'require',


    ];
    protected $message = [
        'name.require' => '请先填写收货人姓名',
        'phone.require' => '请先填写收件人手机号',
        'region.require' => '请先完成地址填写',
        'detail.require' => '请先填写详细信息',
        'select_status.require' =>'请选择是否为默认地址',
        'id.require' =>'请输入需要编辑的地址id'

    ];
    protected $scene = [
        'add' => ['name','phone','region','detail','select_status'],
        'edit' => ['name','phone','region','detail','select_status','id'],
    ];
}