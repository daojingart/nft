<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   18:28
 * +----------------------------------------------------------------------
 * | className:  导航区配置
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\store;


use app\admin\controller\Controller;
use app\admin\model\store\Navigation as NavigationModel;


class Navigation extends Controller
{
    /**
     * 获取轮播图列表
     * @return mixed|\think\response\Json
     * @Time: 2022/6/16   18:30
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
		$NavigationModel = new NavigationModel();
        if($this->request->isAjax()){
            $dataList = $NavigationModel->getList(array_merge($this->request->param(),[
                'type' => 1
            ]));
            return json($dataList);
        }
        return $this->fetch();
    }

    /**
     * 添加
     * @return array|mixed
     * @Time: 2022/6/16   18:35
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add()
    {
        $NavigationModel = new NavigationModel();
        if ($this->request->isAjax()) {
            // 新增记录
            if ($NavigationModel->add($this->postData('navigation'))) {
                return $this->renderSuccess('添加成功', url('store.navigation/index'));
            }
            return $this->renderError($NavigationModel->getError() ?: '添加失败');
        }
        return $this->fetch();
    }

    /**
     * 编辑
     * @param $banner_id
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   18:35
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface renew
     */
    public function renew($id)
    {
        $model = NavigationModel::detail($id);
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('navigation'))) {
                return $this->renderSuccess('更新成功', url('store.navigation/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        return $this->fetch('renew', compact('model'));
    }

    /**
     * 删除
     * @param $banner_id
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   18:36
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface remove
     */
    public function remove($id)
    {
        $model = NavigationModel::detail($id);
        if ($model->setDelete()) {
            return $this->renderSuccess('删除成功', url('store.navigation/index'));
        }
        return $this->renderError($model->getError() ?: '删除成功');
    }

}