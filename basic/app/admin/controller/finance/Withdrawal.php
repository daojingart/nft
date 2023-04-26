<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/14   17:01
 * +----------------------------------------------------------------------
 * | className: 提现管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\finance;


use app\admin\controller\Controller;
use app\admin\model\finance\Withdraw as WithdrawModel;


class Withdrawal extends Controller
{

    public function index()
    {
        return $this->fetch();
    }

    /**
     * 全部
     * @Interface all_list
     */
    public function all_list()
    {
        return $this->getList();
    }

    /**
     * 未审核
     * @Interface examine_list
     */
    public function examine_list()
    {
        return $this->getList(['a.status' => 0]);
    }

    /**
     * 已通过
     * @Interface used_list
     */
    public function used_list()
    {
        return $this->getList(['a.status' => 1]);
    }

    /**
     * 已拒绝
     * @Interface refuse_list
     */
    public function refuse_list()
    {
        return $this->getList(['a.status' => 2]);
    }


    /**
     * 获取列表
     * @param array $filter
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   20:06
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    private function getList($filter = [])
    {
        $model = new WithdrawModel();
        return  $model->getList($filter,$this->request->param());
    }


    /**
     * 修改审核状态
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   20:20
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface upStatus
     */
    public function upStatus()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $data['status'] = $param['status'];
        $data['reason'] = isset($param['reason'])?$param['reason']:'';
        $data['pay_type'] = isset($param['pay_type'])?$param['pay_type']:'';
        $data['update_time'] = time();
        $info = WithdrawModel::detail($id);
        $model = new WithdrawModel();
        if($model->upStatus($data,$info)){
            return $this->renderSuccess('成功');
        }else {
            return $this->renderError($model->getError());
        }
    }
}