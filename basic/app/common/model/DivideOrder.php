<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/10/14   09:44
 * +----------------------------------------------------------------------
 * | className: 订单分润
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

class DivideOrder extends BaseModel
{
    protected $name = 'divide_order';

    /**
     * 分润订单插入
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface insertData
     * @Time: 2022/10/14   09:45
     */
    public function insertData($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

}