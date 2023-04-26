<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/9   10:04 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 角色公共模型
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class StoreRole extends BaseModel
{
    protected $name = 'role';

    /**
     * 关联商户表
     * @return \think\model\relation\BelongsTo
     */
    public function wxapp() {
        return $this->belongsTo('Wxapp');
    }


    /**
     * @Notes: 验证角色名称的唯一性
     * @Interface usernameExit
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/9   1:53 下午
     */
    public  function roleNameExit($role_name)
    {
        return  static::useGlobalScope(false)
            ->where(['role_name' => $role_name])
            ->value('id');
    }

}