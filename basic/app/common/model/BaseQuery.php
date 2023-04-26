<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: liubinghong
 * +----------------------------------------------------------------------
 * | Time:  2021/12/7   11:33
 * +----------------------------------------------------------------------
 * | Email: 18368808324@163.com
 * +----------------------------------------------------------------------
 * | Created by PhpStorm.
 *
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


use think\db\Query;

class BaseQuery extends Query
{
    /**
     * 获取count 防止重置where条件
     *
     * @return int|string
     * @throws \think\Exception
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2021/12/7 11:38
     */
    public function getCount()
    {
        $options = $this->getOptions();
        $count = $this->count();
        $this->options($options);
        return $count;
    }

}