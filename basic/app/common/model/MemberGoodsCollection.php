<?php

namespace app\common\model;

class MemberGoodsCollection extends BaseModel
{
    protected $name = 'member_goods_collection';

    /**
     * 判断某个藏品是否在收藏表中
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  ()
     * @ApiParams   (name="name", type="string", required=true, description="用户名")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function getIsCollection($member_id,$goods_id)
    {
        $data = $this->where(['member_id'=>$member_id,'goods_id'=>$goods_id])->find();
        if ($data){
            return true;
        }else{
            return false;
        }
    }

}