<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/6/30   00:36
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: E 卡订单附表记录
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class RechargeOrderEcard extends BaseModel
{
    protected $name = 'recharge_order_ecard';

    /**
     * @Notes: 记录附表信息
     * @Interface add
     * @param $orderId
     * @param $data
     * @return false|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/30   00:38
     */
    public function add($data)
    {
        return $this->allowField(true)->save($data);
    }
}