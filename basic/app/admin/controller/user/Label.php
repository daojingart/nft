<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2021/12/17   10:16
 * +----------------------------------------------------------------------
 * | className: 标签管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\user;

use app\admin\controller\Controller;
use app\admin\model\collection\MemberGoods;
use app\admin\model\collection\Writer as WriterModel;
use app\admin\model\user\MemberLabel;
use app\common\model\Goods;
use app\common\model\MemberLabelList;

class Label extends Controller
{
    /**
     * 标签列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2021/12/17   10:17
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $model = new MemberLabel();
            $list = $model->getList($this->request->param());
            return json($list);
        }
        return $this->fetch();
    }

    /**
     * 添加标签
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface add
     * @Time: 2021/12/17   10:24
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            return $this->fetch('add');
        }
        $model = new MemberLabel();
        if ($model->add($this->postData('label'))) {
            return $this->renderSuccess('添加成功', url('user.label/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }


    /**
     *  编辑标签
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2021/12/18   9:48
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface edit
     */
    public function edit($id)
    {
        $model = new MemberLabel();
        if (!$this->request->isAjax()) {
            $detail = $model->where('id',$id)->find();
            $goods_info = [];
            if($detail['label_type']==2){
                $goods_info = Goods::detail($detail['goods_id']);
            }
            return $this->fetch('renew',compact('detail','goods_info'));
        }

        if ($model->edit($this->postData('label'))) {
            return $this->renderSuccess('编辑成功', url('user.label/index'));
        }
        $error = $model->getError() ?: '编辑失败';
        return $this->renderError($error);
    }

    /**
     * 删除标签
     * @param $id
     * @return array
     * @Time: 2021/12/18   9:51
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface remove
     */
    public function remove($id)
    {
        $model = new MemberLabel();
        if ($model->remove($id)) {
            return $this->renderSuccess('删除成功', url('user.label/index'));
        }
        $error = $model->getError() ?: '删除失败';
        return $this->renderError($error);
    }

    /**
     * 查看会员列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface memberlist
     * @Time: 2022/6/29   18:00
     */
    public function member()
    {
        $model = new MemberLabel();
        if ($this->request->isAjax()) {
            $list = $model->getMemberList($this->request->param());
            return json($list);
        }
        //接收下标签的ID
        $label_id = $this->request->param('id');
        $label_info = $model->where(['id'=>$label_id])->find();
        $label_type = $label_info['label_type'];
        $member_ids = (new MemberLabelList())->where(['lable_id'=>$label_id])->column('member_id');
        $member_count = count($member_ids);
        $goods_count = (new MemberGoods())->where(['member_id'=>['in',$member_ids],'goods_status' => 0,'is_donation' => 0,'cast_status'=>2,'is_synthesis'=>0])->count();
        return $this->fetch('',compact('label_id','member_count','goods_count','label_type'));
    }

    /**
     * 删除标签里面的群组成员
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface memberRemove
     * @Time: 2022/7/22   11:34
     */
    public function memberRemove($id,$label_id)
    {
        $model = new MemberLabel();
        if ($model->memberRemove($id)) {
            return $this->renderSuccess('移除成功', url('user.label/member',['id'=>$label_id]));
        }
        $error = $model->getError() ?: '删除失败';
        return $this->renderError($error);
    }

    /**
     * 获取藏品列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface goods
     * @Time: 2022/8/13   14:37
     */
    public function goods()
    {
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $types = $this->request->param('types');
        $this->assign(['writer' => $writer,'types'=>$types]);
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    /**
     * 自动贴标签
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface automaticMember
     * @Time: 2022/8/14   13:35
     */
    public function automaticMember()
    {
        $param = $this->request->param();
        $member_label = (new MemberLabel())->where(['id'=>$param['label_id']])->find();
        if((new MemberLabelList())->automatic($member_label)){
            return $this->renderSuccess('同步成功', url('user.label/member',['id'=>$param['label_id']]));
        }
        return $this->renderError("同步失败");
    }

    /**
     * 表格导入
     * @return array
     * @Time: 2022/10/22   17:05
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface importMember
     */
    public function importMember()
    {
        $data = json_decode(html_entity_decode($this->request->param()['data']), true);
        if (empty($data)) {
            return $this->renderError('导入失败');
        }
        $label_id = $this->request->param('label_id');
        if((new MemberLabelList())->importLableMember($data,$label_id)){
            return $this->renderSuccess('同步成功', url('user.label/member',['id'=>$label_id]));
        }
        return $this->renderError("导入失败");

    }

}