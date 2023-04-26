<?php

namespace app\api\controller\member;

use app\common\controller\Controller;
use app\common\model\MemberGoodsCollection;

/**
 * 收藏
 */
class Collection extends Controller
{
    /**
     * 收藏\取消收藏
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.collection/getCollection)
     * @ApiParams   (name="goods_id", type="string", required=true, description="藏品的ID")
     */
    public function getCollection()
    {
        $goods_id = $this->request->post('goods_id');
        if(!$goods_id){
            $this->error('参数错误');
        }
        //判断是否存在
        $res = (new MemberGoodsCollection())->where(['goods_id'=>$goods_id,'member_id'=>$this->auth->member_id])->find();
        if($res){
             (new MemberGoodsCollection())->where(['goods_id'=>$goods_id,'member_id'=>$this->auth->member_id])->delete();
             $this->success('取消收藏成功');
        }
        $data = [
            'goods_id' => $goods_id,
            'member_id' => $this->auth->member_id,
            'create_time' => time(),
            'update_time' => time(),
            'app_id' => $this->app_id,
        ];
        $res = MemberGoodsCollection::create($data);
        if ($res) {
            $this->success('收藏成功');
        } else {
            $this->error('收藏失败');
        }
    }
}