<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2021/12/7   12:17
 * +----------------------------------------------------------------------
 * | className: 公共页面类
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller;

use app\admin\model\Setting;
use app\common\model\Member;
use tx\Vod;

class Common extends Controller
{
    /**
     * 公共分享页面
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2021/12/7   12:18
     * @todo 前端对接后完善分享链接  根据传参 进行定义链接
     */
    public function index($id,$type)
    {
        $this->view->engine->layout(false);
        $page_url = $this->getPageIndex($id,$type);
        return $this->fetch('',compact('page_url'));
    }

    /**
     * 分享页面渲染
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getPageIndex
     * @Time: 2022/3/27   21:15
     */
    public function getPageIndex($id,$type)
    {
        switch ($type)
        {
            case "teacher":
                return base_url()."h5/h5.html#/pagesD/teacher/specialTeacher?id={$id}";
                break;

        }
    }

    /**
     * 公共会员列表页面渲染
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getMemberList
     * @Time: 2021/12/8   14:50
     */
    public function getMemberList()
    {
        if ($this->request->isAjax()) {
            $model = new Member();
            $list = $model->getList($this->request->param());
            return json($list);
        }
        $this->view->engine->layout(false);
        return $this->fetch('memberlist');
    }

    /**
     * 公共知识产品渲染列表页面  多选框
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getCourseList
     * @Time: 2021/12/9   22:01
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     * show_type 1专栏  show_type 2专栏、商城
     */
    public function getCourseList($show_type =0)
    {
        if ($this->request->isAjax()) {
            $model = new Course();
            $list = $model->getList($this->request->param());
            return json($list);
        }
        $this->view->engine->layout(false);
        return $this->fetch('courselist',compact('show_type'));
    }

    /**
     * 公共知识产品渲染列表页面  多选框
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2021/12/30 16:58
     */
    public function getCourseMes()
    {
        if ($this->request->isAjax()) {
            $model = new Course();
            $list = $model->getCourseMes($this->request->param());
            return json($list);
        }
    }

    /**
     * 公共知识产品渲染列表页面  单选框
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getCourseList
     * @Time: 2021/12/9   22:01
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     * show_type 1专栏  show_type 2专栏、商城
     */
    public function getSingleCourseList($show_type =0)
    {
        if ($this->request->isAjax()) {
            $model = new Course();
            $list = $model->getList($this->request->param());
            return json($list);
        }
        $this->view->engine->layout(false);
        return $this->fetch('getsinglecourselist',compact('show_type'));
    }

    /**
     * 知识课程分类列表
     * @return mixed
     * @Time: 2021/12/28   22:35
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getCourseCategory
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getCourseCategory()
    {
        $this->view->engine->layout(false);
        return $this->fetch('getcoursecategory');
    }

    /**
     * 商城分类列表
     * @return mixed
     * @Time: 2021/12/29   17:21
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getGoodsCategory
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getGoodsCategory()
    {
        $this->view->engine->layout(false);
        return $this->fetch('goodscategory');
    }

    /**
     * 获取直播列表数据显示
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getCheckLiveList
     * @Time: 2021/12/29   15:29
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getCheckLiveList()
    {
        $this->view->engine->layout(false);
        return $this->fetch('checklivelist');
    }

    /**
     * 获取商品列表数据
     * @return mixed
     * @Time: 2021/12/30   21:36
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getGoodsList
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getGoodsList()
    {
        $this->view->engine->layout(false);
        return $this->fetch('goods');
    }

    /**
     * 包含视频、音频、图文、专栏、直播  【适用于权益配置】
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getLiveCourse
     * @Time: 2022/1/14   22:59
     */
    public function getLiveCourse()
    {
        $this->view->engine->layout(false);
        return $this->fetch('livecourse');
    }

    /**
     * 生成上传签名
     * @return false|string
     * @Time: 2021/12/5   21:39
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSignature
     */
    public function getSignature()
    {
        $values = Setting::getItem("resource");
        return getSignature($values['engine'][$values['default']]);
    }

    /**
     * 获取视频元信息
     * @param $FileId
     * @return string
     * @Time: 2021/12/5   22:34
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getDescribeMediaInfos
     */
    public function getDescribeMediaInfos($FileId)
    {
        $values = Setting::getItem("resource");
        $vod = new Vod($values['engine'][$values['default']]);
        $result = $vod->getDescribeMediaInfos($FileId);
        return $result['MediaInfoSet'][0]['MetaData']['Duration'];
    }

    /**
     * 更新状态
     * @return mixed
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-04-08 09:28
     */
    public function status()
    {
        $data = $this->request->param();
        $id = array_key_exists('id',$data)?$data['id']:'';

        if (!$id){
            return  $this->renderError('更新失败');
        }
        $path = "app\common\model\\".$data['dbname'];
        $acti = $path::where('id',$id)->find();
        if (!$acti){
            return $this->renderError('更新状态失败');
        }
        $acti->status = $acti->status?0:1;
        $res = $acti->save();
        if ($res){
            return  $this->renderSuccess('更新成功');
        }
        return  $this->renderError('更新失败');
//        return alert('更新失败',$_SERVER['HTTP_REFERER'],2);
    }

    /**
     * 更新审核状态
     * @return mixed
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-04-08 09:28
     */
    public function isverify()
    {
        $data = $this->request->param();
        $id = array_key_exists('id',$data)?$data['id']:'';
        if (!$id){
            return  $this->renderError('更新失败');
        }
        $path = "app\common\model\\".$data['dbname'];
        $acti = $path::where('id',$id)->find();
        if (!$acti){
            return $this->renderError('更新状态失败');
        }
        $acti->isverify = $acti->isverify?0:1;
        $res = $acti->save();
        if ($res){
            return  $this->renderSuccess('更新成功');
        }
        return  $this->renderError('更新失败');
//        return alert('更新失败',$_SERVER['HTTP_REFERER'],2);
    }


    /**
     * 删除
     * @return array id  dbname
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-04-08 09:53
     */
    public function del()
    {
        $data = $this->request->param();
        $id = array_key_exists('id',$data)?$data['id']:'';
        if (!$id){
            return  $this->renderError('删除失败');
        }
        $path = "app\common\model\\".$data['dbname'];
        $res = $path::destroy($data['id']);
        if ($res){
            return  $this->renderSuccess('删除成功');
        }
        return  $this->renderError('更新失败');
    }


    public static function givePrizeCount($member_id)
    {
        $count = Setting::getItem('prize')['authentication_prize_count'];
        Member::where(['member_id' => $member_id])->setInc('prize_count', $count);
    }

}