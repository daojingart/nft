<?php
/**
 * +----------------------------------------------------------------------
 * | ..
 * +----------------------------------------------------------------------
 * | Copyright (c) .. http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: xx
 * +----------------------------------------------------------------------
 * | Time:  2021/2/19   3:46 下午
 * +----------------------------------------------------------------------
 * | Email: ****@163.com
 * +----------------------------------------------------------------------
 * | className: 首页通知管理、在线帮助
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\application;


use app\admin\controller\Controller;
use app\admin\model\setting\Notice as NoticeModel;
use app\common\model\ProductCategory;

class Notice extends Controller
{
    /**
     * @Notes: 渲染列表
     * @Interface index
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: xx
     * @copyright: ..
     * @Time: 2020/12/20   4:17 下午
     */
    public function index()
    {
        $NoticeModel = new NoticeModel;
        if($this->request->isAjax()){
            $dataList = $NoticeModel->getDataList(array_merge($this->request->param(),['type'=>'2']));
            return json($dataList);
        }
        return $this->fetch();
    }

    /**
     * @Notes: 添加
     * @Interface add
     * @return array|mixed
     * @author: xx
     * @copyright: ..
     * @Time: 2020/12/20   4:23 下午
     */
    public function add()
    {
        $NoticeModel = new NoticeModel();
        if ($this->request->isAjax()) {
            // 新增记录
            $res = $NoticeModel->add($this->postData('notice'));
            if ($res['res']) {
                return $this->renderSuccess('添加成功', url('application.notice/index'));
            }
            return $this->renderError($NoticeModel->getError() ?: '添加失败');
        }
        $type_id = $this->request->param("type");
        $category_list = (new ProductCategory())->order("sort asc")->select();
        return $this->fetch('',compact('type_id','category_list'));
    }

    /**
     * @Notes: 编辑
     * @Interface renew
     * @param $id
     * @return array|mixed
     * @throws \think\exception\DbException
     * @author: xx
     * @copyright: ..
     * @Time: 2020/12/20   4:32 下午
     */
    public function renew($id)
    {
        $model = NoticeModel::detail($id);
        if ($this->request->isAjax()) {
            // 更新记录
            if ($model->edit($this->postData('notice'))) {
                return $this->renderSuccess('更新成功', url('application.notice/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        $category_list = (new ProductCategory())->order("sort asc")->select();
        return $this->fetch('renew', compact('model','category_list'));
    }


    /**
     * @Notes: 删除
     * @Interface remove
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     * @author: xx
     * @copyright: ..
     * @Time: 2020/12/20   4:37 下午
     */
    public function remove($id)
    {
        //访客权限添加
        // 套餐详情
        $model = NoticeModel::detail($id);
        // 更新记录
        if ($model->setDelete()) {
            return $this->renderSuccess('删除成功', url('application.notice/index'));
        }
        return $this->renderError($model->getError() ?: '删除成功');
    }

}