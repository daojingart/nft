<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/11   15:11
 * +----------------------------------------------------------------------
 * | className:   发售日历管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\collection;


use app\admin\controller\Controller;
use app\admin\model\collection\Calendar as CalendarModel;
use think\Exception;
use think\exception\DbException;
use app\admin\model\collection\Goods as GoodsModel;
use think\Db;


class Calendar extends Controller
{

    /**
     * 获取发售日历列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/11   15:51
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
        if($this->request->isAjax()){
            $param = input('param.');
            $selectResult  = (new CalendarModel())->getList($param);
            return json($selectResult);
        }
        return $this->fetch();
    }

    /**
     * 添加发售日历
     * @return array|mixed
     * @Time: 2022/6/11   16:36
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add()
    {
        $WriterModel = new CalendarModel();
        if($this->request->isAjax()){
            if ($WriterModel->add($this->postData('calendar'))) {
                return $this->renderSuccess('添加成功',url('collection.calendar/index'));
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
        $model = CalendarModel::detail($this->request->param("id"));
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('calendar'))) {
                return $this->renderSuccess('更新成功',url('collection.calendar/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        return $this->fetch('renew', compact('model'));
    }

    /**
     * 删除
     * @param $id
     * @return array
     * @throws DbException
     * @Time: 2022/6/11   15:10
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface remove
     */
    public function remove($id)
    {
        $model = CalendarModel::detail($id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 获取平台所有的藏品
     * @return mixed|\think\response\Json
     * @throws DbException
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @Time: 2022/6/13   16:53
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGoodsList
     */
    public function getGoodsList()
    {
        if ($this->request->isAjax()) {
            $goodsModel = (new GoodsModel());
            $list = $goodsModel->getList(array_merge($this->request->param(),['product_types' => 1]));
            return json($list);
        }
        $this->view->engine->layout(false);
        return $this->fetch('goodslist');
    }



    /**
     * 获取当前平台的藏品列表
     * @return mixed|\think\response\Json
     * @throws DbException
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @Time: 2022/6/13   16:53
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGoodsList
     */
    public function getReserveGoodsList()
    {
        if ($this->request->isAjax()) {
            $goodsModel = (new GoodsModel());
            $list = $goodsModel->getReserveGoodsList(array_merge($this->request->param()));
            return json($list);
        }
        $calendar_id = $this->request->param('calendar_id');
        $this->view->engine->layout(false);
        return $this->fetch('getreservegoodsList',compact('calendar_id'));
    }

    /**
     * 添加商品
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/2/16   22:39
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface addGoods
     */
    public function addGoods()
    {
        $param = $this->request->param();

        $goodsModel = (new GoodsModel());

        // 是否添加过此藏品
        $model = new CalendarModel();
        $info = $model->where(['id' => $param['calendar_id']])->find()->toArray();

        $new_goods_ids = [];

        Db::startTrans();
        try {
            foreach ($param['goods_ids'] as $key => $value){
                // 获取藏品信息
                $new_goods_ids[] = $value;
            }
            $new_goods_ids1 = implode(',',$new_goods_ids);
            $new_goods_ids2 = $new_goods_ids1;
            $model->where(['id' => $info['id']])->update(['goods_id' => $new_goods_ids2,'update_time' => time()]);

            Db::commit();
            return $this->renderSuccess('添加成功');
        }catch (Exception $e){
            Db::rollback();
            return $this->renderError($e->getMessage());
        }

    }
}