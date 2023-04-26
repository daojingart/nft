<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/14   9:10
 * +----------------------------------------------------------------------
 * | className:  盲盒管理
 * +----------------------------------------------------------------------
 */
namespace app\admin\controller\collection;


use app\admin\controller\Controller;
use app\admin\model\collection\Goods as GoodsModel;
use app\admin\model\collection\Category as CategoryModel;
use app\admin\model\collection\Blindbox as BlindboxModel;
use app\admin\model\collection\Writer as WriterModel;
use app\admin\model\Setting;


class Blindbox extends Controller
{

    /**
     * 获取盲盒列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   9:56
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
        if($this->request->isAjax()){
            $list  = (new GoodsModel())->getList(array_merge($this->request->param(),[
                'types' => 3
            ]));
            return json($list);
        }

        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        $this->assign(['category' => $category]);

        return $this->fetch();
    }

    /**
     * 添加盲盒
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   9:56
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add()
    {
        $WriterModel = new GoodsModel();
        if($this->request->isAjax()){
            // 获取提交数据
            $param = $this->postData('blindbox');
            $param['d_images'] = implode(',',$param['d_images']);
            if ($WriterModel->add($param)) {
                return $this->renderSuccess('添加成功',url('collection.blindbox/index'));
            }
            return $this->renderError($WriterModel->getError() ?: '添加失败');
        }
        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $this->assign(['category' => $category,'writer'=>$writer]);
        return $this->fetch();
    }

    /**
     * 编辑盲盒
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   9:57
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface renew
     */
    public function renew()
    {
        $model = GoodsModel::detail($this->request->param("goods_id"));
        if ($this->request->isAjax()) {
            // 获取提交数据
            $param = $this->postData('blindbox');
            $param['d_images'] = implode(',',$param['d_images']);
            if ($model->edit($param)) {
                return $this->renderSuccess('更新成功',url('collection.blindbox/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        // 盲盒轮播图
        $model['d_images'] = explode(',',$model['d_images']);
        $model['start_time'] = date('Y-m-d H:i:s',$model['start_time']);

        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();

        return $this->fetch('renew', compact('model','category','writer'));
    }

    /**
     * 删除盲盒
     * @param $goods_id
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   9:58
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface remove
     */
    public function remove($goods_id)
    {
        $model = GoodsModel::detail($goods_id);
        if (!$model->setDelete($model['product_types'])) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 操作盲盒上下架
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   9:58
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface operation
     */
    public function operation()
    {
        $param = $this->request->param();
        if((new GoodsModel())->updateGoodsStatus($param)){
            return $this->renderSuccess('处理成功');
        }
        return $this->renderError("操作失败");
    }




    /*****************************************盲盒商品管理********************************************************/


    /**
     * 盲盒里面的藏品列表
     * @param $goods_id
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   10:40
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGoodsList
     */
    public function getGoodsList()
    {
        if($this->request->isAjax()){
            $list  = (new BlindboxModel())->getList($this->request->param());
            return json($list);
        }
        $this->assign(['blindbox_id' => $this->request->param('goods_id')]);
        return $this->fetch('goods');
    }


    /**
     * 添加盲盒商品
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface addgoods
     * @Time: 2022/6/23   11:52
     */
    public function addgoods()
    {
        $WriterModel = new GoodsModel();
        if($this->request->isAjax()){
            if ($WriterModel->add($this->postData('goods'))) {
                return $this->renderSuccess('添加成功',url('collection.goods/index'));
            }
            return $this->renderError($WriterModel->getError() ?: '添加失败');
        }
        $blindbox_id = $this->request->param('blindbox_id');
        $this->view->engine->layout(false);
        // 商品分类
        $open_box_type = $WriterModel->where(['goods_id'=>$blindbox_id])->field('open_box_type')->find()['open_box_type'];
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $blockchain = Setting::getItem('blockchain');
        return $this->fetch('',compact('category', 'writer','blindbox_id','blockchain','open_box_type'));
    }

    /**
     * 编辑 盲盒商品信息
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface renewGoods
     * @Time: 2022/6/23   14:43
     */
    public function renewGoods($goods_id)
    {
        $this->view->engine->layout(false);
        $model = GoodsModel::detail($goods_id);
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('goods'))) {
                return $this->renderSuccess('更新成功',url('collection.goods/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        // 开售时间
        $model['start_time'] = date('Y-m-d H:i:s',$model['start_time']);
        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $info = (new BlindboxModel())->where(['goods_id'=>$goods_id])->find();
        $model['probability'] = $info['probability'];
        $open_box_type = (new GoodsModel())->where(['goods_id'=>$info['blindbox_id']])->field('open_box_type')->find()['open_box_type'];

        return $this->fetch('', compact('model','category','writer','open_box_type'));
    }

    /**
     * 删除盲盒藏品
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   11:09
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface removeGoods
     */
    public function removeGoods($id)
    {
        $model = BlindboxModel::detail($id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }


}