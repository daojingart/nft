<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/19   21:41
 * +----------------------------------------------------------------------
 * | className: 区块链地址表
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

class MemberChain extends BaseModel
{
    protected $name = 'member_chain';

    /**
     * 创建钱包地址
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createAccount
     * @Time: 2022/6/19   21:42
     */
    public function createAccount($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

    /**
     * 查询当前用户钱包地址是否存在
     * @param $member_id
     * @return MemberChain|null
     * @throws \think\exception\DbException
     * @Time: 2022/6/19   21:43
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface details
     */
    public static function details($data)
    {
        return self::get($data);
    }
}