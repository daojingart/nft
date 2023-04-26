<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/6/30   00:35
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: E 卡充值订单
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class RechargeOrder extends BaseModel
{
    protected $name = 'recharge_order';

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo('app\common\model\Member');
    }

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function orderEcard()
    {
        return $this->belongsTo('app\common\model\RechargeOrderEcard',"order_id","order_id");
    }


}