<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   14:57
 * +----------------------------------------------------------------------
 * | className:  往期回顾
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\application;


use app\admin\controller\Controller;
use app\admin\model\application\Period as PeriodModel;
use think\exception\DbException;


class Period extends Controller
{
    /**
     * 获取列表
     * @return mixed|\think\response\Json
     * @throws DbException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @Time: 2022/6/16   15:32
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
        if($this->request->isAjax()){
            $param = input('param.');
            $selectResult  = (new PeriodModel())->getList($param);
            return json($selectResult);
        }
        return $this->fetch();
    }


    /**
     * 添加作家
     * @return array|mixed
     * @Time: 2022/6/16   15:10
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add()
    {
        $WriterModel = new PeriodModel();
        if($this->request->isAjax()){
            if ($WriterModel->add($this->postData('period'))) {
                return $this->renderSuccess('添加成功',url('application.period/index'));
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
        $model = PeriodModel::detail($this->request->param("id"));
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('period'))) {
                return $this->renderSuccess('更新成功',url('application.period/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }

        $model['content'] = htmlspecialchars_decode($model['content']);
        return $this->fetch('renew', compact('model'));
    }

    /**
     * 删除
     * @param $id
     * @return array
     * @throws DbException
     * @Time: 2022/6/16   15:10
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface remove
     */
    public function remove($id)
    {
        $model = PeriodModel::detail($id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
}