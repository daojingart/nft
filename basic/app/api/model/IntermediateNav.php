<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/6/3   21:36
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 中部导航菜单栏目
 * +----------------------------------------------------------------------
 */

namespace app\api\model;


use app\common\model\Intermediate;

class IntermediateNav extends Intermediate
{
    /**
     * @Notes: 获取数据列表
     * @Interface getDataList
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/3   21:36
     */
    public function getDataList()
    {
        return $this->field("id,nav_title,subtitle")->select();
    }

    /**
     * @Notes: 获取数据列表
     * @Interface getTypeList
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/3   23:39
     */
    public function getTypeList($id)
    {
        return $this->where(['id'=>$id])->find();
    }
}