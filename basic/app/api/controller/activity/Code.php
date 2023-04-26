<?php

namespace app\api\controller\activity;

use app\common\components\helpers\RedisUtils;
use app\common\controller\Controller;
use app\common\model\GoodsConvert;


/**
 * 兑换码
 */
class Code extends Controller
{
    /**
     * 兑换藏品
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute (/api/activity.Code/conversionCode)
     * @ApiParams   (name="code", type="string", required=true, description="兑换码")
     */
    public function conversionCode()
    {
        $memberInfo = $this->auth->member_id;

        $params     = $this->request->param();
        $member_lock_key = "member_lock_member_id:conversionCode".$memberInfo;
        if (RedisUtils::lock($member_lock_key, 10)) {
             $this->error('点击的太快了,请10S后重试');
        }
        $res        = GoodsConvert::exchange($params, $memberInfo);
        if ($res) {
             $this->success("兑换成功");
        }
         $this->error('兑换失败');
    }


    /**
     * 兑换码使用记录
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.Code/getRedemptionCodeRecord)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     */
    public function getRedemptionCodeRecord($page)
    {
        $list      = (new GoodsConvert())->where(['used_member_id' => $this->auth->member_id])->page($page, 10)->field("code_num,used_time,id")->select();
        foreach ($list as $key => $val) {
            $list[$key]['used_time'] = date("Y-m-d H:i:s", $val['used_time']);
        }
        $this->success($list);
    }

}