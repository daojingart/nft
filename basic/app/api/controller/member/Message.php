<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/6/4   05:57
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 站内信
 * +----------------------------------------------------------------------
 */

namespace app\api\controller\member;

use app\common\controller\Controller;
use app\common\model\Notice as MessageModel;

/**
 * 站内信
 */
class Message extends Controller
{
    protected $noNeedLogin = [
        'getMessageCount'
    ];
    /**
     * 获取未读消息的条数
     * @ApiAuthor [Mr.Li]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.message/getMessageCount)
     * @ApiReturnParams   (name="count_number", type="string", description="未读消息条数")\
     */
    public function getMessageCount()
    {
        $count = 0;
        if($this->auth->isLogin()){
            $member_id = $this->auth->getUser()['member_id'];
            $count = (new MessageModel())->where(['member_id'=>$member_id,'status'=>0,'disabled'=>10,'type'=>1])->count();
        }
        $this->success('ok',['count_number'=>$count]);
    }

    /**
     * 获取站内信列表
     * @ApiAuthor [Mr.Li]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.message/getMessageList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     */
    public function getMessageList()
    {
        $member_id = $this->auth->getUser()['member_id'];
        $page = $this->request->post('page')?$this->request->post('page'):1;
        if(!$page){
            $this->error('参数传值错误');
        }
        $this->success('ok',['list'=>(new MessageModel())->NoticeList($member_id,1,$page)]);
    }

    /**
     * 获取站内信的详情
     * @ApiAuthor [Mr.Li]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.message/noticeInfo)
     * @ApiParams   (name="id", type="string", required=true, description="站内信详情ID")
     */
    public function noticeInfo()
    {
        $id = $this->request->param('id');
        if(!$id){
            $this->error('参数传值错误');
        }
        $data = MessageModel::where('id',$id)->find();
        if ($data){
            if ($data['type'] = 2) {
                $data['content'] = htmlspecialchars_decode($data['content']);
            }
            MessageModel::update(['status'=>1],['id'=>$id]);
            $this->success('ok',$data);
        }
        $this->error($data);
    }

    /**
     * 一键已读
     * @ApiAuthor [Mr.Li]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.message/readNotice)
     */
    public function readNotice()
    {
        $member_id = $this->auth->getUser()['member_id'];
        if (MessageModel::update(['status'=>1],['member_id'=>$member_id])){
            $this->success('已读成功');
        }
        $this->error('读取失败');
    }



}