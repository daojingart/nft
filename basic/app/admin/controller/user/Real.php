<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 13:46
 */


namespace app\admin\controller\user;


use app\admin\controller\Common;
use app\admin\controller\Controller;
use app\api\model\task\AwardSetting;
use app\common\controller\Task;
use app\common\model\Glory;
use app\common\model\Member;
use app\common\model\MemberReal;
use app\common\model\Notice;

/**
 * 用户实名表
 * Class Real
 * @package app\admin\controller\user
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 13:46
 */
class Real extends Controller
{
    /**
     * 实名列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 13:47
     */
    public function index()
    {
        if (request()->isAjax()){
            $data = MemberReal::getList($this->request->param());
            return json($data);
        }
        return $this->fetch();
    }

    /**
     * 审核
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 14:01
     */
    public function audit()
    {
        $id=input('id');
        $status=input('status');
        if($status==2){
            $content='您的实名认证申请已通过';
        }else{
            $content='您的实名认证申请被拒绝，请重新申请';
        }
        $notice=[
            'member_id'=>$id,
            'type'=>1,
            'title'=>'系统消息',
            'content'=>$content,
            'create_time'=>time(),
            'status'=>0,
        ];
        $res  = Notice::create($notice);
        $res1 = Member::update(['real_status'=>$status],['member_id'=>$id]);
        //创建区块链账户地址
        if($status ==2){
            //赠送荣誉值
            $value = \app\admin\model\Setting::getItem("todaytask");
            if($value['honor_num']>0){
                $integralData = [
                    'member_id' => $id,
                    'type' => 1,
                    'amount' => $value['honor_num'],
                    'remark' => '实名认证'
                ];
                (new Glory())->allowField(true)->save($integralData);
            }
            ///注册成功赠送荣誉值
            $member_info = (new Member())->where(['member_id'=>$id])->find();
            if($member_info['p_id']){
                (new AwardSetting())->getTaskList($member_info['p_id']);
                if(isset($value['condition_task']) && $value['condition_task']==2){
                    (new Member())->where(['member_id'=>$member_info['p_id']])->setInc("invitations_number",1);
                }
            }
            (new Task())->createAccount($id);
            Common::givePrizeCount($id);
        }
        if ($res && $res1){
            return  $this->renderSuccess('审核成功');
        }
        return  $this->renderError('审核失败');
    }


    /**
     * 批量审核拒绝
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface batchAudit
     * @Time: 2022/7/20   10:15
     */
    public function batchAudit()
    {
        $param = $this->request->param();
        if($param['is_rack']==10){
            $content='您的实名认证申请已通过';
            $status = '2';
        }else{
            $content='您的实名认证申请被拒绝，请重新申请';
            $status = '3';
        }
        foreach ($param['id'] as $key=>$val){
            $member_rel = (new MemberReal())->where(['id'=>$val])->find();
            $member_info = (new Member())->where(['member_id'=>$member_rel['member_id']])->find();
            if($member_info['real_status']['value']==1){
                $notice=[
                    'member_id'=>$member_rel['member_id'],
                    'type'=>1,
                    'title'=>'系统消息',
                    'content'=>$content,
                    'create_time'=>time(),
                    'status'=>0,
                ];
                $res  = Notice::create($notice);
                $res1 = Member::update(['real_status'=>$status],['member_id'=>$member_rel['member_id']]);
                if($status ==2){
                    (new Task())->createAccount($member_rel['member_id']);
                    //赠送荣誉值
                    $value = \app\admin\model\Setting::getItem("todaytask");
                    if($value['honor_num']>0){
                        $integralData = [
                            'member_id' => $member_rel['member_id'],
                            'type' => 1,
                            'amount' => $value['honor_num'],
                            'remark' => '实名认证'
                        ];
                        (new Glory())->allowField(true)->save($integralData);
                        ///注册成功赠送荣誉值
                        $member_info = (new Member())->where(['member_id'=>$member_rel['member_id']])->find();
                        if($member_info['p_id']){
                            (new AwardSetting())->getTaskList($member_info['p_id']);
                        }
                    }
                }
            }
        }
        return  $this->renderSuccess('审核成功');
    }
}