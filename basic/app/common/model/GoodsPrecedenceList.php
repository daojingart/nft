<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/9/3   14:54
 * +----------------------------------------------------------------------
 * | className: 持有藏品优先购买表
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

class GoodsPrecedenceList extends BaseModel
{
    protected $name = 'goods_precedence_list';

    /**
     * 持有藏品规则添加
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface insertData
     * @Time: 2022/9/3   14:55
     */
    public function insertData($data)
    {
        foreach ($data as $key=>$value){
            $data[$key]['app_id'] = self::$app_id;
        }
        return $this->allowField(true)->saveAll($data);
    }

    /**
     * 持有藏品规则查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getDetails
     * @Time: 2022/9/3   14:57
     */
    public function getDetails($field)
    {
        return $this->where($field)->find();
    }

}