<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 09:35
 */


namespace app\common\model;


class ProductSpecRel extends BaseModel
{
    public function spec()
    {
        return $this->belongsTo('Spec');
    }
}