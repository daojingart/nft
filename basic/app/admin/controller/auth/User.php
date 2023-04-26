<?php
// +----------------------------------------------------------------------
// | 八六互联 [Eight six interconnection ]
// +----------------------------------------------------------------------
// | Copyright (c) 2008~2019 http://www.86itn.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zq
// +----------------------------------------------------------------------
// | ClassName: 后台用户管理
// +----------------------------------------------------------------------

namespace app\admin\controller\auth;

use app\admin\controller\Controller;
use app\admin\model\auth\StoreRole;
use app\admin\model\auth\StoreUser as StoreUserModel;
use think\exception\DbException;
use think\exception\PDOException;

class User extends Controller
{
    /**
     * @Notes: 管理员列表
     * @Interface index
     * @return mixed
     * @throws DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/9   1:36 上午
     */
    public function index()
    {
        if($this->request->isAjax()){
            $param = input('param.');
            $selectResult  = (new StoreUserModel)->getList($param);
            return json($selectResult);
        }
        return $this->fetch();
    }


    /**
     * @Notes: 增加管理员
     * @Interface add
     * @throws PDOException
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/9   9:45 上午
     * @author: Mr.Zhang
     */
    public function add()
    {
        $StoreUserModel = new StoreUserModel();
        if($this->request->isAjax()){
            if ($StoreUserModel->insertUser($this->postData('user'))) {
                return $this->renderSuccess('添加成功',url('auth.user/index'));
            }
            return $this->renderError($StoreUserModel->getError() ?: '添加失败');
        }
        $roleModel = new StoreRole();
        $roleList = $roleModel->getList();
        $this->assign([
            'roleList' => $roleList
        ]);
        return $this->fetch();
    }


    /**
     * 更新当前管理员信息
     * @return array|mixed
     * @throws DbException
     */
    public function renew()
    {
        $model = StoreUserModel::detail($this->request->param("store_user_id"));
        if ($this->request->isAjax()) {
            if ($model->renew($this->postData('user'))) {
                return $this->renderSuccess('更新成功',url('auth.user/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        $roleModel = new StoreRole();
        $roleList = $roleModel->getList();
        return $this->fetch('renew', compact('model','roleList'));
    }



    /**
     * @Notes:删除管理员
     * @Interface del
     * @param $store_user_id
     * @return array
     * @throws DbException
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   11:17 上午
     * @author: Mr.Zhang
     */
    public function remove($store_user_id)
    {
        $model = StoreUserModel::detail($store_user_id);
        if (!$model->setDel()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
}
