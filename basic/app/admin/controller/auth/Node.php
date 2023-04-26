<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/13   11:44 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 节点控制器
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\auth;


use app\admin\controller\Controller;
use app\admin\model\auth\StoreNode as StoreNodeModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\response\Json;

class Node extends Controller
{
    /**
     * @Notes: 渲染节点数据
     * @Interface index
     * @return mixed|Json
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/14   1:47 下午
     */
    public function index()
    {
        if($this->request->isAjax()){
            $storeNodeModel = new StoreNodeModel();
            $storeNodeModel = $storeNodeModel->getNodeList();
            return json($storeNodeModel);
        }
        return $this->fetch();
    }


    /**
     * @Notes: 添加节点
     * @Interface add
     * @return array|mixed
     * @throws DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/14   3:50 下午
     */
    public function add()
    {
        $StoreNodeModel = new StoreNodeModel();
        if($this->request->isAjax()){
            if ($StoreNodeModel->insertNode($this->postData('node'))) {
                return $this->renderSuccess('添加成功',url('store.node/index'));
            }
            return $this->renderError($StoreNodeModel->getError() ?: '添加失败');
        }
        //关闭模板布局情况
        $this->view->engine->layout(false);
        //获取当前ID的信息
        $info = StoreNodeModel::getDetails($this->request->param("id"));
        return $this->fetch('',compact('info'));
    }

    /**
     * @Notes: 编辑节点
     * @Interface renew
     * @return array|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/14   3:29 下午
     */
    public function renew()
    {
        $StoreNodeModel = new StoreNodeModel();
        if($this->request->isAjax()){
            if ($StoreNodeModel->renew($this->postData('node'))) {
                return $this->renderSuccess('编辑成功',url('store.node/index'));
            }
            return $this->renderError($StoreNodeModel->getError() ?: '编辑失败');
        }
        //关闭模板布局情况
        $this->view->engine->layout(false);
        //获取当前ID的信息
        $info = StoreNodeModel::getDetails($this->request->param("id"));
        $nodeList = $StoreNodeModel->getNodeList(['id'=>['<>',2]])['data'];
        $this->assign([
            'info' => $info,
            'node_list' => $nodeList,
        ]);
        return $this->fetch();
    }

    /**
     * @Notes: 节点删除 软删除
     * @Interface remove
     * @param $id
     * @return array
     * @throws DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/14   3:51 下午
     */
    public function remove($id)
    {
        $model = StoreNodeModel::getDetails($id);
        if (!$model->setDel($model)) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
}