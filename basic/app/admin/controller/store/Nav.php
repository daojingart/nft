<?php

namespace app\admin\controller\store;

use app\admin\controller\Controller;
use app\common\model\Nav as NavModel;
use Monolog\Handler\IFTTTHandler;

class Nav extends Controller
{
    public function index()
    {
        $NavigationModel = new NavModel();
        if($this->request->isAjax()){
            $dataList = $NavigationModel->getList();
            return json($dataList);
        }
        $this->assign(['count'=>$NavigationModel->count()]);
        return $this->fetch();
    }

    public function add()
    {
        $NavigationModel = new NavModel();
        if ($this->request->isAjax()) {
            // 新增记录
            if ($NavigationModel->count()>=5){
                return $this->renderError('底部导航最多五条');
            }
            if ($NavigationModel->add($this->postData('navigation'))) {
                return $this->renderSuccess('添加成功', url('store.nav/index'));
            }
            return $this->renderError($NavigationModel->getError() ?: '添加失败');
        }
        return $this->fetch();
    }


    public function renew($id)
    {
        $model = NavModel::get($id);
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('navigation'))) {
                return $this->renderSuccess('更新成功', url('store.nav/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        return $this->fetch('renew', compact('model'));
    }
}