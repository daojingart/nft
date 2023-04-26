<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/12   6:26 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 角色管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\auth;


use app\admin\controller\Controller;
use app\admin\model\auth\StoreNode as StoreNodeModel;
use app\admin\model\auth\StoreRole;
use app\admin\model\auth\StoreRole as StoreRoleModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;

class Role extends Controller
{
    /**
     * @Notes: 角色列表
     * @Interface index
     * @return mixed
     * @throws DbException
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   6:28 下午
     */
    public function index()
    {
        if($this->request->isAjax()){
            $param = input('param.');
            $selectResult  = (new StoreRoleModel)->getListPage($param);
            return json($selectResult);
        }
        return $this->fetch();
    }

    /**
     * @Notes: 增加角色信息
     * @Interface add
     * @return array|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/15   3:49 下午
     */
    public function add()
    {
        $StoreRoleModel = new StoreRoleModel();
        if($this->request->isAjax()){
            if ($StoreRoleModel->insertUser($this->postData('role'))) {
                return $this->renderSuccess('添加成功',url('auth.role/index'));
            }
            return $this->renderError($StoreRoleModel->getError() ?: '添加失败');
        }
        //查询所有节点信息
        $storeNode = new StoreNodeModel();
        $this->assign([
            'node_list' => getTree($storeNode->getNodeTreeList()->toArray())
        ]);
        return $this->fetch();
    }

    /**
     * 更新角色信息
     * @return array|mixed
     * @throws DbException
     */
    public function renew()
    {
        $model = StoreRoleModel::detail($this->request->param("id"));
        if ($this->request->isAjax()) {
            if ($model->renew($this->postData('role'))) {
                return $this->renderSuccess('更新成功',url('auth.role/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        //查询所有节点信息
        $storeNode = new StoreNodeModel();
        $this->assign([
            'node_list' => getTree($storeNode->getNodeTreeList()->toArray()),
            'model' => $model
        ]);
        return $this->fetch('renew');
    }

    /**
     * @Notes:删除角色信息
     * @Interface del
     * @param $id
     * @return array
     * @throws DbException
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   11:17 上午
     * @author: Mr.Zhang
     */
    public function remove($id)
    {
        $model = StoreRoleModel::detail($id);
        if (!$model->setDel()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }


}