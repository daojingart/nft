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
 * | className: 用户公共模型
 * +----------------------------------------------------------------------
 */
namespace app\common\model;

use think\model\relation\BelongsTo;

class StoreUser extends BaseModel
{
    protected $name = 'user';
    protected static $status = '2';

    /**
     * @Notes: 关联应用表
     * @Interface wxapp
     * @return BelongsTo
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/15   11:56 上午
     */
    public function blapp() {
        return $this->belongsTo('app\admin\model\App');
    }

    /**
     * @Notes: 关联角色表
     * @Interface role
     * @return BelongsTo
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/15   6:23 下午
     */
    public function role() {
        return $this->belongsTo('StoreRole','role_id');
    }

    /**
     * @Notes: 数据验证保证其用户名在每个商户的唯一性
     * @Interface usernameExit
     * @param $username
     * @return float|mixed|string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/9   1:53 下午
     */
    public  function usernameExit($username)
    {
        return  static::useGlobalScope(false)
            ->where(['user_name' => $username,'status'=>['<>',self::$status]])
            ->value('id');
    }

}
