<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/14   15:43
 * +----------------------------------------------------------------------
 * | className: 财务管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\finance;


use app\admin\controller\Controller;
use app\common\model\Finance as FinanceModel;
use app\common\model\Glory;


class Finance extends Controller
{
    /**
     * 获取账单流水列表
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getBillflowList
     * @Time: 2022/6/14   15:44
     */
    public function billflow()
    {
        $FinanceModel = new FinanceModel();
        if($this->request->isAjax()){
            $dataList = $FinanceModel->getList($this->request->param());
            return json($dataList);
        }
        return  $this->fetch('billflow_log');
    }

    /**
     * 获取荣誉值流水列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   16:54
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface glory
     */
    public function glory()
    {
        $FinanceModel = new Glory();
        if($this->request->isAjax()){
            $dataList = $FinanceModel->getList($this->request->param());
            return json($dataList);
        }
        return  $this->fetch('glory_log');
    }
}