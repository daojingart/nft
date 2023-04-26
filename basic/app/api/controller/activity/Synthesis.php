<?php

namespace app\api\controller\activity;

use app\common\components\helpers\RedisUtils;
use app\common\controller\Controller;
use app\api\model\collection\GoodsSynthetic as GoodsSyntheticModel;
use app\common\model\GoodsSynthetic;
use app\api\validate\IDMustBePositiveInt;
use app\common\model\GoodsSyntheticCount;
use app\common\model\MemberGoods;
use app\common\model\Setting;


/**
 * 合成活动
 */
class Synthesis extends Controller
{
    protected $noNeedLogin = [
        'getList','syntheticInstructions'
    ];

    /**
     * 合成列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.synthesis/getList)
     * @ApiParams   (name="page", type="string", required=true, description="页码")
     * @ApiParams   (name="type", type="string", required=true, description="type  1=全部 2=进行中 3=未开始 4=已结束")
     * @ApiReturnParams   (name="id", type="string", description="合成活动id")
     * @ApiReturnParams   (name="count", type="string", description="合成的数量")
     * @ApiReturnParams   (name="exchange", type="string", description="已经合成的数量")
     * @ApiReturnParams   (name="name", type="string", description="合成活动名称")
     * @ApiReturnParams   (name="start_time", type="string", description="开始时间")
     * @ApiReturnParams   (name="end_time", type="string", description="结束时间")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="活动的封面图")
     * @ApiReturnParams   (name="strtotime_start_time", type="string", description="开始活动的时间戳")
     * @ApiReturnParams   (name="strtotime_end_time", type="string", description="活动结束时间戳")
     * @ApiReturnParams   (name="now_time", type="string", description="现在时间戳")
     */
    public function getList()
    {
        $page = $this->request->post('page', 1);
        $type = $this->request->post('type', 1);
        if(!$page || !$type){
            $this->error('参数错误');
        }
        $this->success('获取数据成功',GoodsSyntheticModel::getList($type));
    }

    /**
     * 合成活动的详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.synthesis/getDetails)
     * @ApiParams   (name="id", type="string", required=true, description="合成活动的ID")
     * @ApiReturnParams   (name="id", type="string", description="合成活动的ID")
     * @ApiReturnParams   (name="goods_id", type="string", description="合成商品的ID")
     * @ApiReturnParams   (name="count", type="string", description="剩余数量")
     * @ApiReturnParams   (name="name", type="string", description="合成活动的名称")
     * @ApiReturnParams   (name="content", type="string", description="合成活动的简介")
     * @ApiReturnParams   (name="exchange", type="string", description="已经兑换的产品ID")
     * @ApiReturnParams   (name="strtotime_start_time", type="string", description="开始活动的时间戳")
     * @ApiReturnParams   (name="strtotime_end_time", type="string", description="结束时间的时间戳")
     * @ApiReturnParams   (name="now_time", type="string", description="现在时间")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="封面图")
     * @ApiReturnParams   (name="is_suffix", type="string", description="10  gif   20/3D")
     * @ApiReturnParams   (name="counts", type="array", description="材料列表")
     */
    public function getDetails()
    {
        (new IDMustBePositiveInt())->goCheck();
        $id = $this->request->post('id');
        if(!$id){
            $this->error('参数错误');
        }
        $this->success('',GoodsSynthetic::getDetails($id,$this->auth->member_id));
    }

    /**
     * 合成材料的列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.synthesis/getProductList)
     * @ApiParams   (name="id", type="string", required=true, description="活动的ID")
     * @ApiParams   (name="goods_id", type="string", required=true, description="碎片藏品的ID")
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="id", type="string", description="材料的ID")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="封面图片")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品名称")
     * @ApiReturnParams   (name="collection_number", type="string", description="藏品编号")
     */
    public function getProductList()
    {
        $id = $this->request->post('id');
        $goods_id = $this->request->post('goods_id');
        $page = $this->request->post('page',1);
        if(!$id || !$goods_id || !$page){
            $this->error('参数错误');
        }
        $data = (new GoodsSyntheticCount())->where(['synthetic_id'=>$id,'goods_id'=>$goods_id])->find();
        if(empty($data)){
            $this->error('参数错误');
        }
        //获取会员是否存在这个藏品
        $data_list = (new MemberGoods())->where(['member_id'=>$this->auth->member_id,'goods_id'=>$goods_id,'goods_status'=>0,'cast_status'=>2,'is_synthesis'=>0,'is_donation'=>0])
            ->field("id,goods_thumb,goods_name,collection_number")
            ->page($page,10)
            ->select();
        $this->success('',$data_list);
    }


    /**
     * 合成须知
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.synthesis/syntheticInstructions)
     */
    public function syntheticInstructions()
    {
        $this->success('',['data'=>Setting::getItem('read')['synthetic']]);
    }

    /**
     * 发起合成活动
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.synthesis/synthetic)
     * @ApiParams   (name="id", type="string", required=true, description="活动的ID")
     * @ApiParams   (name="material_id", type="string", required=true, description="材料的ID,多个ID 使用,隔开例如：1,2,3,4")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function synthetic()
    {
        (new IDMustBePositiveInt())->goCheck();
        $id = $this->request->param('id');
        $material_id = $this->request->param('material_id');
        if(!$id || !$material_id){
            $this->error('缺少合成碎片信息');
        }
        $lockKey = "SyntheticCollection:{$id}:{$this->auth->member_id}";
        if (RedisUtils::lock($lockKey,2)) {
            $this->error('网络繁忙，请稍后再试');
        }
        try {
            $res = GoodsSyntheticModel::synthetic($id,$this->auth->member_id,$material_id);
        } catch (\Exception $e) {
            RedisUtils::unlock($lockKey);
            $this->error($e->getMessage());
        }
        RedisUtils::unlock($lockKey);
        if ($res){
            $this->success('合成成功');
        }
        $this->error('合成失败');
    }

}