<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/6/26   11:00
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 充值
 * +----------------------------------------------------------------------
 */

namespace app\api\model;

use app\common\model\Recharge as RechargeModel;

class Recharge extends RechargeModel
{
    /**
     * @Notes: 获取充值列表
     * @Interface getDataList
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/26   11:01
     */
    public static function getDataList()
    {
        return self::where(['diasbled'=>'10'])->order("sort asc")->field("ecard_id,face_value")->select();
    }

    /**
     * @Notes: 充值套餐详情
     * @Interface detail
     * @param $ecard_id
     * @return Ecard|null
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/30   00:07
     */
    public static function detail($ecard_id)
    {
        return self::get($ecard_id);
    }

}