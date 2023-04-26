<?php

namespace app\api\controller\member;

use app\common\controller\Controller;
use app\common\model\Finance as FinanceModel;
use app\common\model\Member as MemberModel;
use app\common\model\MemberGoods;

/**
 * 我的团队
 */
class Team extends Controller
{
    /**
     * 我的收益
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.team/getMyEarnings)
     * @ApiReturnParams   (name="sum_amount", type="string", description="总收益")
     * @ApiReturnParams   (name="day_amount", type="string", description="当天收益")
     * @ApiReturnParams   (name="weeks_amount", type="string", description="本周收益")
     * @ApiReturnParams   (name="month_amount", type="string", description="本月收益")
     */
    public function getMyEarnings()
    {
        $financeModel = new FinanceModel();
        $sum_amount = $financeModel->where('member_id',$this->auth->member_id)->where('type',4)->sum('amount');
        $day_amount = $financeModel->where('member_id',$this->auth->member_id)->where('type',4)->whereTime("create_time", 'd')->sum('amount');
        $weeks_amount = $financeModel->where('member_id',$this->auth->member_id)->where('type',4)->whereTime("create_time", 'w')->sum('amount');
        $month_amount = $financeModel->where('member_id',$this->auth->member_id)->where('type',4)->whereTime("create_time", 'm')->sum('amount');
        $this->success("ok",compact('sum_amount','day_amount','weeks_amount','month_amount'));
    }

    /**
     * 获取人数
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.team/getPeopleNumber)
     * @ApiParams (name="type", type="string", required=true, description="1:全部 2:当天")
     * @ApiReturnParams   (name="one_number", type="string", description="直推人数")
     * @ApiReturnParams   (name="y_one_number", type="string", description="直推有效人数")
     * @ApiReturnParams   (name="two_number", type="string", description="间推人数")
     * @ApiReturnParams   (name="y_two_number", type="string", description="间推有效")
     */
    public function getPeopleNumber()
    {
        $type = $this->request->post("type");
        if(!$type){
            $this->error("参数错误");
        }
        $memberModel = new MemberModel();
        $two_number = 0;
        $y_two_number = 0;
        if($type==2){
            $one_number = $memberModel->where('p_id',$this->auth->member_id)->whereTime("create_time", "d")->count();
            $y_one_number = $memberModel->where(['p_id'=>$this->auth->member_id,'real_status'=>2])->whereTime("create_time", "d")->count();
            $member_lists = $memberModel->where(['p_id'=>$this->auth->member_id])->whereTime("create_time", "d")->select();
            foreach ($member_lists as $key=>$val){
                //计算直推人数
                $two_number += $memberModel->where(['p_id'=>$val['member_id']])->whereTime("create_time", "d")->count();
                $y_two_number += $memberModel->where(['p_id'=>$val['member_id'],'real_status'=>2])->whereTime("create_time", "d")->count();
            }
        }else{
            $one_number = $memberModel->where('p_id',$this->auth->member_id)->count();
            $y_one_number = $memberModel->where(['p_id'=>$this->auth->member_id,'real_status'=>2])->count();
            $member_lists = $memberModel->where(['p_id'=>$this->auth->member_id])->select();
            foreach ($member_lists as $key=>$val){
                //计算直推人数
                $two_number += $memberModel->where(['p_id'=>$val['member_id']])->count();
                $y_two_number += $memberModel->where(['p_id'=>$val['member_id'],'real_status'=>2])->count();
            }
        }
        $this->success('ok',compact('one_number','y_one_number','two_number','y_two_number'));
    }


    /**
     * 获取直推列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.team/getTeamList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiParams   (name="type", type="string", required=true, description="1=全部2=本周3=本月")
     * @ApiReturnParams   (name="phone", type="string", description="手机号")
     * @ApiReturnParams   (name="name", type="string", description="昵称")
     * @ApiReturnParams   (name="avatarUrl", type="string", description="头像")
     * @ApiReturnParams   (name="real_status", type="string", description="实名认证状态")
     * @ApiReturnParams   (name="one_count", type="string", description="直推人数")
     * @ApiReturnParams   (name="hold_count", type="string", description="藏品数量")
     * @ApiReturnParams   (name="total_price", type="string", description="消费金额")
     */
    public function getTeamList()
    {
        $page = $this->request->post("page", 1);
        $type = $this->request->post("type");
        if(!$type || !$page){
            $this->error("参数错误");
        }
        $model = new MemberModel();
        switch ($type)
        {
            case "1":
                $member_lists = $model->where(['p_id'=>$this->auth->member_id])->page($page,10)->field("member_id,phone,name,avatarUrl,real_status")->select();
                break;
            case "2":
                $member_lists = $model->where(['p_id'=>$this->auth->member_id])->whereTime("create_time", "w")->page($page,10)->field("member_id,phone,name,avatarUrl,real_status")->select();
                break;
            case "3":
                $member_lists = $model->where(['p_id'=>$this->auth->member_id])->whereTime("create_time", "m")->page($page,10)->field("member_id,phone,name,avatarUrl,real_status")->select();
                break;
        }
        if(empty($member_lists)){
            $this->success("ok",[]);
        }
        $memberGoods = new MemberGoods();
        $orderModel = new \app\api\model\order\Order();
        foreach ($member_lists as $key=>$val){
            //计算直推人数
            $one_count = $model->where(['p_id'=>$val['member_id']])->count();
            //藏品数量
            $hold_count = $memberGoods->where(['member_id'=>$val['member_id'],'goods_status'=>0])->count();
            //消费金额
            $total_price = $orderModel->where(['member_id'=>$val['member_id'],'pay_status'=>2,'order_type'=>['in',['1','2']]])->sum("total_price");
            //手机号隐藏后四位
            $phone = preg_replace('/(\d{3})\d{4}(\d{4})/', '$1****$2', $val['phone']);
            $member_lists[$key]['one_count'] = $one_count;
            $member_lists[$key]['hold_count'] = $hold_count;
            $member_lists[$key]['total_price'] = $total_price;
            $member_lists[$key]['phone'] = $phone;
            unset($member_lists[$key]['member_id']);
        }
        $this->success("ok",$member_lists);
    }


    /**
     * 获取间推
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.team/getInternudgeList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiParams   (name="type", type="string", required=true, description="1=全部2=本周3=本月")
     * @ApiReturnParams   (name="phone", type="string", description="手机号")
     * @ApiReturnParams   (name="name", type="string", description="昵称")
     * @ApiReturnParams   (name="avatarUrl", type="string", description="头像")
     * @ApiReturnParams   (name="real_status", type="string", description="实名认证状态")
     * @ApiReturnParams   (name="one_count", type="string", description="直推人数")
     * @ApiReturnParams   (name="hold_count", type="string", description="藏品数量")
     * @ApiReturnParams   (name="total_price", type="string", description="消费金额")
     */
    public function getInternudgeList()
    {
        $page = $this->request->post("page", 1);
        $type = $this->request->post("type");
        if(!$type || !$page){
            $this->error("参数错误");
        }
        $model = new MemberModel();
        switch ($type)
        {
            case "1":
                $member_ids = $model->where(['p_id'=>$this->auth->member_id])->page($page,10)->field("member_id,phone,name,avatarUrl,real_status")->column('member_id');
                break;
            case "2":
                $member_ids = $model->where(['p_id'=>$this->auth->member_id])->whereTime("create_time", "w")->page($page,10)->field("member_id,phone,name,avatarUrl,real_status")->column('member_id');
                break;
            case "3":
                $member_ids = $model->where(['p_id'=>$this->auth->member_id])->whereTime("create_time", "m")->page($page,10)->field("member_id,phone,name,avatarUrl,real_status")->column('member_id');
                break;
        }
        if(empty($member_ids)){
            $this->success("ok",[]);
        }
        $memberGoods = new MemberGoods();
        $orderModel = new \app\api\model\order\Order();
        $member_lists = $model->where(['p_id'=>['in',$member_ids],'real_status'=>2])->page($page,10)->field("member_id,phone,name,real_status,avatarUrl")->select();
        foreach ($member_lists as $key=>$val){
            //计算直推人数
            $one_count = $model->where(['p_id'=>$val['member_id']])->count();
            //藏品数量
            $hold_count = $memberGoods->where(['member_id'=>$val['member_id'],'goods_status'=>0])->count();
            //消费金额
            $total_price = $orderModel->where(['member_id'=>$val['member_id'],'pay_status'=>2,'order_type'=>['in',['1','2']]])->sum("total_price");
            //手机号隐藏后四位
            $phone = preg_replace('/(\d{3})\d{4}(\d{4})/', '$1****$2', $val['phone']);
            $member_lists[$key]['one_count'] = $one_count;
            $member_lists[$key]['hold_count'] = $hold_count;
            $member_lists[$key]['total_price'] = $total_price;
            $member_lists[$key]['phone'] = $phone;
            unset($member_lists[$key]['member_id']);
        }
        $this->success("ok",$member_lists);
    }




}