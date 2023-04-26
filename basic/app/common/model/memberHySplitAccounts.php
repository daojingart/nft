<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/22   15:27
 * +----------------------------------------------------------------------
 * | className: 汇元支付
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

class memberHySplitAccounts extends BaseModel
{
    protected $name = 'member_hy_split_accounts';

    /**
     * 分账记录表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface insertData
     * @Time: 2022/8/22   15:28
     */
    public function insertData($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

}