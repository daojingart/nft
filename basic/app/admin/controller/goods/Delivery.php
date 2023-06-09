<?php
// +----------------------------------------------------------------------
// | 八六互联 [Eight six interconnection ]
// +----------------------------------------------------------------------
// | Copyright (c) 2008~2019 http://www.86itn.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zq
// +----------------------------------------------------------------------
// | ClassName: 运费管理模板
// +----------------------------------------------------------------------

namespace app\admin\controller\goods;

use app\admin\controller\Controller;
use app\admin\model\product\Delivery as DeliveryModel;
use app\common\model\Region;


class Delivery extends Controller
{
    /**
     * @Notes: 配送模板列表
     * @Interface index
     * @return mixed|\think\response\Json
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   2:17 下午
     */
    public function index()
    {
        if($this->request->isAjax()){
            $selectResult  = (new DeliveryModel)->getList($this->request->param());
            return json($selectResult);
        }
        return $this->fetch();
    }

    /**
     * 删除模板
     * @param $delivery_id
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function delete($delivery_id)
    {
        $model = DeliveryModel::detail($delivery_id);
        if (!$model->remove()) {
            $error = $model->getError() ?: '删除失败';
            return $this->renderError($error);
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 添加配送模板
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            // 获取所有地区
            $regionData = json_encode(Region::getCacheTree());
            return $this->fetch('add', compact('regionData'));
        }
        // 新增记录
        $model = new DeliveryModel;
        if ($model->add($this->postData('delivery'))) {
            return $this->renderSuccess('添加成功', url('goods.delivery/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }

    /**
     * 编辑配送模板
     * @param $delivery_id
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function renew($delivery_id)
    {
        // 模板详情
        $model = DeliveryModel::detail($delivery_id);
        if (!$this->request->isAjax()) {
            // 获取所有地区
            $regionData = json_encode(Region::getCacheTree());
            return $this->fetch('renew', compact('regionData','model'));
        }
        // 更新记录
        if ($model->edit($this->postData('delivery'))) {
            return $this->renderSuccess('更新成功', url('store.delivery/index'));
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }

}
