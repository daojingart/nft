<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/13   10:52
 * +----------------------------------------------------------------------
 * | className:  藏品分类管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\collection;


use app\admin\controller\Controller;
use think\exception\DbException;
use app\admin\model\collection\Category as CategoryModel;


class Category extends Controller
{
    /**
     * 获取分类列表
     * @return mixed|\think\response\Json
     * @throws DbException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @Time: 2022/6/13   18:35
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
        if($this->request->isAjax()){
            $param = input('param.');
            $selectResult  = (new CategoryModel())->getList($param);
            return json($selectResult);
        }
        return $this->fetch();
    }

    /**
     * 添加作家
     * @return array|mixed
     * @Time: 2022/6/11   14:42
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add()
    {
        $WriterModel = new CategoryModel();
        if($this->request->isAjax()){
            if ($WriterModel->add($this->postData('category'))) {
                return $this->renderSuccess('添加成功',url('collection.category/index'));
            }
            return $this->renderError($WriterModel->getError() ?: '添加失败');
        }
        return $this->fetch();
    }

    /**
     * 更新
     * @return array|mixed
     * @throws DbException
     */
    public function renew()
    {
        $model = CategoryModel::detail($this->request->param("category_id"));
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('category'))) {
                return $this->renderSuccess('更新成功',url('collection.category/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        return $this->fetch('renew', compact('model'));
    }

    /**
     * 删除
     * @param $category_id
     * @return array
     * @throws DbException
     * @Time: 2022/6/11   15:10
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface remove
     */
    public function remove($category_id)
    {
        $model = CategoryModel::detail($category_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

}