<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/14   10:32
 * +----------------------------------------------------------------------
 * | className: 申购管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\collection;


use app\admin\controller\Controller;
use app\admin\model\collection\Goods as GoodsModel;
use app\admin\model\collection\Writer as WriterModel;
use app\admin\model\collection\Category as CategoryModel;
use app\common\model\Appointment;
use app\common\model\Purchase as PurchaseModel;


class Purchase extends Controller
{
    /**
     * 申购藏品列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   14:32
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
        if($this->request->isAjax()){
            $list  = (new GoodsModel())->getList(array_merge($this->request->param(),[
                'types' => 4
            ]));
            return json($list);
        }

        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();

        $this->assign(['category' => $category,'writer' => $writer]);
        return $this->fetch();
    }

    /**
     * 添加
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   14:34
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add()
    {
        $WriterModel = new GoodsModel();
        if($this->request->isAjax()){
            if ($WriterModel->add($this->postData('purchase'),$this->postData('time'))) {
                return $this->renderSuccess('添加成功',url('collection.purchase/index'));
            }
            return $this->renderError($WriterModel->getError() ?: '添加失败');
        }

        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();

        $this->assign(['category' => $category,'writer' => $writer]);

        return $this->fetch();
    }

    /**
     * 编辑
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   14:34
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface renew
     */
    public function renew()
    {
        $model = GoodsModel::detail($this->request->param("goods_id"));
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('purchase'),$this->postData('time'))) {
                return $this->renderSuccess('更新成功',url('collection.purchase/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }

        // 开售时间
        $model['start_time'] = date('Y-m-d H:i:s',$model['start_time']);

        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();

        // 获取预约时间
        $purchase_info = (new PurchaseModel())->where(['goods_id' => $model['goods_id']])->find()->toArray();
        $model['appointment_start_time'] = date('Y-m-d H:i:s',$purchase_info['appointment_start_time']);
        $model['appointment_end_time'] = date('Y-m-d H:i:s',$purchase_info['appointment_end_time']);
        $model['draw_time'] = date('Y-m-d H:i:s',$purchase_info['draw_time']);
        $model['init_booking_num'] = $purchase_info['init_booking_num'];

        return $this->fetch('renew', compact('model','category','writer'));
    }

    /**
     * 删除
     * @param $goods_id
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   14:35
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
     * 操作商品上下架
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   14:35
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


    /**
     * 获取预约记录列表
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getAppointmentList
     * @Time: 2022/6/21   19:20
     */
    public function getAppointmentList()
    {
        if($this->request->isAjax()){
            $list  = (new Appointment())->getList(array_merge($this->request->param()));
            return json($list);
        }
        $goods_id = $this->request->param('goods_id');
        return $this->fetch('',compact('goods_id'));
    }

}