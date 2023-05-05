<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/2/22   20:56
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\api\controller\member;

use app\common\controller\Controller;
use app\common\model\Member as MemberModel;
use app\common\model\MemberGoods;
use app\admin\model\Setting as SettingModel;
use think\Cache;
use think\Db;

/**
 * 排行榜
 */
class Ranking extends Controller
{
    /**
     * 消费排行榜
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.ranking/getConsumptionLeaderboard)
     * @ApiReturnParams   (name="my_number", type="string", description="邀请人数")
     * @ApiReturnParams   (name="name", type="name", description="昵称")
     * @ApiReturnParams   (name="avatarUrl", type="string", description="头像")
     * @ApiReturnParams   (name="code", type="string", description="邀请码")
     * @ApiReturnParams   (name="index_number", type="string", description="当前排名")
     */
    public function getConsumptionLeaderboard()
    {
        $member_list = (new MemberModel())->order("amount_spent desc")->limit(0,10)->field("name,avatarUrl,amount_spent,member_id")->select()->toArray();
		$member_lists = array_column($member_list, 'member_id');
        $index = array_search($this->auth->member_id,$member_lists);
        if(is_int($index)){
            $index = $index+1;
        }else{
            $index = "暂无排名";
        }
        $this->success("ok",[
            'list' => $member_list,
            'index_number' => $index,
            'my_number' => $this->auth->amount_spent,
            'name' => $this->auth->name,
            'avatarUrl' => $this->auth->avatarUrl,
            'code' => $this->auth->code,
        ]);
    }

    /**
     * 邀请排行榜
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.ranking/getInviteLeaderboards)
     * @ApiReturnParams   (name="my_number", type="string", description="邀请人数")
     * @ApiReturnParams   (name="name", type="name", description="昵称")
     * @ApiReturnParams   (name="avatarUrl", type="string", description="头像")
     * @ApiReturnParams   (name="code", type="string", description="邀请码")
     * @ApiReturnParams   (name="index_number", type="string", description="当前排名")
     */
    public function getInviteLeaderboards()
    {
//        $model = new memberModel();
//        $member_list = $model->order("invitations_number desc")->limit(0,100)->field("name,avatarUrl,invitations_number,member_id")->select()->toArray();
//        foreach ($member_list as $key=>$val){
//            $member_list[$key]['invitations_number'] = float_number($val['invitations_number']);
//        }
//        $member_lists = array_column($member_list, 'member_id');
//        $index = array_search($this->auth->member_id,$member_lists);
//        if (is_int($index)){
//            $index = $index+1;
//        } else {
//            $index = "暂无排名";
//        }
//        $this->success("ok",[
//            'list' => $member_list,
//            'index_number' => $index,
//            'my_number' => $this->auth->invitations_number,
//            'name' => $this->auth->name,
//            'avatarUrl' => $this->auth->avatarUrl,
//            'code' => $this->auth->code,
//        ]);
        $model = new memberModel();
        $member_list = $model->order("invitations_num desc")->limit(0,100)->field("name,avatarUrl,invitations_num,member_id")->select()->toArray();
        foreach ($member_list as $key=>$val){
            $member_list[$key]['invitations_number'] = float_number($val['invitations_num']);
        }
        $member_lists = array_column($member_list, 'member_id');
        $index = array_search($this->auth->member_id,$member_lists);
        if (is_int($index)){
            $index = $index+1;
        } else {
            $index = "暂无排名";
        }

        $values_setting = SettingModel::getItem("todaytask");
        $notice_name = "";
        $notice_address = "";
        if(isset($values_setting['notice_name'])){
            $notice_name = $values_setting['notice_name'];
        }
        if(isset($values_setting['notice_address'])){
            $notice_address = $values_setting['notice_address'];
        }

        $mynum_sql = 'select * from snake_member where real_status = 2 and p_id = '.$this->auth->member_id;
        $result = Db::query($mynum_sql);

        $this->success("ok",[
            'list' => $member_list,
            'index_number' => $index,
            'my_number' => count($result),
            'name' => $this->auth->name,
            'avatarUrl' => $this->auth->avatarUrl,
            'code' => $this->auth->code,
            'notice_name' => $notice_name,
            'notice_address' => $notice_address
        ]);
    }

    /**
     * 根据会员ID获得排行榜
     * @ApiAuthor [Mr.Wei]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.ranking/getLeaderboardsByMemberId)
     * @ApiParams   (name="member_id", type="string", required=true, description="会员ID")
     */
    public function getLeaderboardsByMemberId()
    {
        //$member_id = $this->request->post('member_id');
        $member_id = $this->auth->member_id;
        $sql = "select member_id,create_time,real_status from snake_member where p_id = {$member_id}";
        $result = Db::query($sql);
        foreach($result as $key => &$value){
            $value['create_time'] = date('Y-m-d',$value['create_time']);
            $pay_sql = "select member_id from snake_order where member_id = ".$value['member_id']." and order_type = 2 and pay_status = 2 and order_status = 2";
            $pay_result = Db::query($pay_sql);
            if($value['real_status'] == 2){
                $value['bool_real'] = 1;
            }else{
                $value['bool_real'] = 0;
            }
            $value['bool_buy'] = 0;
            if(count($pay_result)){
                $value['bool_buy'] = 1;
            }
        }
        $this->success("ok",[
            'list' => $result,
        ]);
    }

    /**
     * 持仓排行榜
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.ranking/getPositionLeaderboards)
     * @ApiReturnParams   (name="my_number", type="string", description="邀请人数")
     * @ApiReturnParams   (name="name", type="name", description="昵称")
     * @ApiReturnParams   (name="avatarUrl", type="string", description="头像")
     * @ApiReturnParams   (name="code", type="string", description="邀请码")
     * @ApiReturnParams   (name="index_number", type="string", description="当前排名")
     */
    public function getPositionLeaderboards()
    {
        $filter = [];
        $filter['is_donation'] = 0;
        $filter['is_synthesis'] = 0;
        $filter['goods_status'] = ['in',['0','1']];
        $filter['goods_id'] = Cache::get("positionLeaderboardId");
        $dataList = (new MemberGoods)->alias('a')
            ->join('member b','a.member_id=b.member_id','left')
            ->where($filter)
            ->field('a.member_id,a.id,a.goods_id,b.name as nickname,b.phone')
            ->group("a.member_id")
            ->limit(0,10)
            ->select()->toArray();
        foreach ($dataList as $key=>$val){
            $goods_count = (new MemberGoods)->where(['member_id'=>$val['member_id'],'goods_id'=>$val['goods_id'],'goods_status'=>['in',['0','1']],'is_donation'=>0,'is_synthesis'=>0])->count();
            $dataList[$key]['goods_number'] = $goods_count;
        }
        $key = array_column(array_values($dataList), 'goods_number');
        array_multisort($key, SORT_DESC, $dataList);
        $member_lists = array_column($dataList, 'member_id');
        $index = array_search($this->auth->member_id,$member_lists);
        if(is_int($index)){
            $index = $index+1;
        }else{
            $index = "暂无排名";
        }
        $goods_count = (new MemberGoods)->where(['member_id'=>$this->auth->member_id,'goods_id'=>$val['goods_id'],'goods_status'=>['in',['0','1']],'is_donation'=>0,'is_synthesis'=>0])->count();
        $this->success("ok",[
            'list' => $dataList,
            'index_number' => $index,
            'my_number' => $goods_count,
            'name' => $this->auth->name,
            'avatarUrl' => $this->auth->avatarUrl,
            'code' => $this->auth->code,
        ]);

    }

}