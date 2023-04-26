<?php
// +----------------------------------------------------------------------
// | 八六互联 [Eight six interconnection ]
// +----------------------------------------------------------------------
// | Copyright (c) 2008~2019 http://www.86itn.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zq
// +----------------------------------------------------------------------
// | ClassName: 后台登录控制器
// +----------------------------------------------------------------------'

namespace app\admin\controller;

use app\admin\model\auth\StoreUser;
use app\admin\model\Setting;
use think\Session;


class login extends Controller
{
    /**
     * 后台登录
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login()
    {
        if ($this->request->isAjax()) {
            $model = new StoreUser;
            if ($model->login($this->postData('User'))) {
                return $this->renderSuccess('登录成功', url('index/index'));
            }
            return $this->renderError($model->getError() ?: '登录失败');
        }
        $this->view->engine->layout(false);
        $setting = setting::getAll()['store'] ?: null;
        $this->assign([
            'setting' => $setting,        // 当前登录系统的配置
        ]);
        return $this->fetch('login');
    }


    /**
     * @Notes: 退出
     * @Interface logout
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/15   11:58 上午
     */
    public function logout()
    {
        Session::clear('blhlstore');
        $this->redirect('login/login');
    }

}
