<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: sliyusheng@foxmail.com
 * +----------------------------------------------------------------------
 * | Time: 2022-06-16 14:52
 * +----------------------------------------------------------------------
 * | className: 须知
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\store;


use app\admin\controller\Controller;
use app\admin\model\Setting as SettingModel;


class Read extends Controller
{
    public function read()
    {
        return $this->updateEvent('read');
    }

    /**
     * 更新配置
     * @param $key
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    private function updateEvent($key)
    {
        if (!$this->request->isAjax()) {
            $values = SettingModel::getItem($key);
            return $this->fetch($key, compact('values'));
        }
        $model = new SettingModel;
        if ($model->edit($key, $this->postData($key))) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失败');
    }
}