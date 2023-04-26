<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 18:13
 */


namespace app\common\model;

/**
 * 银行卡数据模型
 * Class MemberCard
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-17 18:13
 */
class MemberCard extends BaseModel
{

    public function category()
    {
        return $this->belongsTo('app\common\model\CardCategory','category_id','id');
    }
}