<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   16:24
 * +----------------------------------------------------------------------
 * | className:  荣誉值兑换
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\collection;


use app\admin\controller\Controller;
use app\admin\model\collection\Exchange as ExchangeModel;
use app\admin\model\collection\Goods as GoodsModel;
use think\Db;
use think\Exception;

class Exchange extends Controller
{

    /**
     * 获取兑换列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   16:51
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
        if($this->request->isAjax()){
            $param = input('param.');
            $selectResult  = (new ExchangeModel())->getList($param);
            return json($selectResult);
        }
        return $this->fetch();
    }

    /**
     * 添加
     * @return array|mixed
     * @Time: 2022/6/16   16:36
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function renew()
    {
        $model = ExchangeModel::detail($this->request->param("id"));
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('exchange'))) {
                return $this->renderSuccess('更新成功',url('collection.exchange/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        $model['goods_info'] = GoodsModel::detail($model['hold_goods_id']);
        return $this->fetch('renew', compact('model'));
    }

    /**
     * 删除
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   16:37
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface remove
     */
    public function remove($id)
    {
        $model = ExchangeModel::detail($id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 获取盲盒列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   17:08
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getblindboxlist
     */
    public function getblindboxlist()
    {
        if ($this->request->isAjax()) {
            $goodsModel = (new GoodsModel());
            $list = $goodsModel->getList(array_merge($this->request->param(),['types' => 3]));
            return json($list);
        }
        $this->view->engine->layout(false);
        return $this->fetch('goodslist');
    }

    /**
     * 添加藏品/盲盒
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   17:10
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface addGoods
     */
    public function addGoods()
    {
        $param = $this->request->param();

        // 是否添加过此藏品
        $model = new ExchangeModel();
        Db::startTrans();
        try {
            $data = [];
            foreach ($param['goods_ids'] as $value){
                // 判断此藏品是否已添加过
                $isHave = $model->where(['goods_id' => $value,'is_del'=>0])->find();
                if(empty($isHave)){
                    $data[] = [
                        'goods_id' => $value,
                        'type' => $param['type'],
                        'app_id' => 10001
                    ];
                }
            }

            if(count($data) > 0){
                $model->allowField(true)->saveAll($data);
            }

            Db::commit();
            return $this->renderSuccess('添加成功');
        }catch (Exception $e){
            Db::rollback();
            return $this->renderError($e->getMessage());
        }
    }

}