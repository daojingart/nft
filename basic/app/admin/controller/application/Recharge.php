<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/7/28   01:16
 * +----------------------------------------------------------------------
 * | className: 充值规格
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\application;

use app\admin\controller\Controller;
use app\admin\model\application\Recharge as RechargeModel;

class Recharge extends Controller
{
    /**
     * 充值规格
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2022/7/28   01:17
     */
    public function index()
    {
        if($this->request->isAjax()) {
            $param = input('param.');
            $model = new RechargeModel();
            $list = $model->getListAll($param);
            return json($list);
        }
        return $this->fetch('index');
    }

    /**
     * 添加专题
     * @return array|mixed
     */
    public function add()
    {
        $model = new RechargeModel();
        if ($this->request->isAjax()) {
            // 新增记录
            if ($model->add($this->postData('ecard'))) {
                return $this->renderSuccess('添加成功', url('application.recharge/index'));
            }
            return $this->renderError($model->getError() ?: '添加失败');
        }
        return $this->fetch('add');
    }


    /**
     * @Notes: 编辑E卡充值
     * @Interface renew
     * @param $ecard_id
     * @return array|mixed
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/24   10:13 上午
     */
    public function renew($ecard_id)
    {
        $model = RechargeModel::get($ecard_id);
        if (!$this->request->isAjax()) {
            return $this->fetch('renew', compact('model'));
        }
        // 更新记录
        if ($model->edit($this->postData('ecard'))) {
            return $this->renderSuccess('更新成功', url('application.recharge/index'));
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }


    /**
     * @Notes: 删除E卡充值
     * @Interface remove
     * @param $ecard_id
     * @return array
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/24   10:13 上午
     * @throws \think\Exception
     */
    public function remove($ecard_id)
    {
        $model = RechargeModel::get($ecard_id);
        if (!$model->remove($ecard_id)) {
            $error = $model->getError() ?: '删除失败';
            return $this->renderError($error);
        }
        return $this->renderSuccess('删除成功');
    }


}