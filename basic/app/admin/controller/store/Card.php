<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-20 17:15
 */


namespace app\admin\controller\store;


use app\admin\controller\Controller;
use app\common\model\CardCategory;

/**
 * 银行配置
 * Class Card
 * @package app\admin\controller\store
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-20 17:16
 */
class Card extends Controller
{
    /**
     * 银行列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-20 17:16
     */
    public function index()
    {
        if (request()->isAjax()){
            $data = CardCategory::getList();
            return  json($data);
        }
        return $this->fetch();
    }

    /**
     * 添加银行
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-20 17:16
     */
    public function add()
    {
        if (request()->isAjax()){
            $param = $this->request->param();
            if (CardCategory::create($param['card'])){
                return  $this->renderSuccess('创建成功',url('index'));
            }
            return  $this->renderError('创建失败');
        }
        return $this->fetch('add');
    }

    /**
     * 编辑银行
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-20 17:16
     */
    public function edit($id)
    {
        if (request()->isAjax()){
            $param = request()->param();
            if (CardCategory::update($param['card'],['id'=>$param['id']])){
                return  $this->renderSuccess('更新成功',url('index'));
            }
            return  $this->renderError('更新失败');
        }
        $data = CardCategory::where('id',$id)->find();
        $this->assign('data',$data);
        return $this->fetch();
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
        $model = CardCategory::detail($id);
        if ($model->setDelete()) {
            return $this->renderSuccess('删除成功', url('index'));
        }
        return $this->renderError($model->getError() ?: '删除成功');
    }
}