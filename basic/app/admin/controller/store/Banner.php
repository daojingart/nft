<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   18:28
 * +----------------------------------------------------------------------
 * | className:  轮播图管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\store;


use app\admin\controller\Controller;
use app\admin\model\store\Banner as BannerModel;


class Banner extends Controller
{
    /**
     * 获取轮播图列表
     * @return mixed|\think\response\Json
     * @Time: 2022/6/16   18:30
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
        $BannerModel = new BannerModel();
        if($this->request->isAjax()){
            $dataList = $BannerModel->getList(array_merge($this->request->param(),[
                'type' => ['in',['1','2']]
            ]));
            return json($dataList);
        }
        return $this->fetch();
    }

    /**
     * 添加
     * @return array|mixed
     * @Time: 2022/6/16   18:35
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add()
    {
        $BannerModel = new BannerModel();
        if ($this->request->isAjax()) {
            // 新增记录
            if ($BannerModel->add($this->postData('banner'))) {
                return $this->renderSuccess('添加成功', url('store.banner/index'));
            }
            return $this->renderError($BannerModel->getError() ?: '添加失败');
        }
        return $this->fetch();
    }

    /**
     * 编辑
     * @param $banner_id
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   18:35
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface renew
     */
    public function renew($banner_id)
    {
        $model = BannerModel::detail($banner_id);
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('banner'))) {
                return $this->renderSuccess('更新成功', url('store.banner/index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        return $this->fetch('renew', compact('model'));
    }

    /**
     * 删除
     * @param $banner_id
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   18:36
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface remove
     */
    public function remove($banner_id)
    {
        $model = BannerModel::detail($banner_id);
        if ($model->setDelete()) {
            return $this->renderSuccess('删除成功', url('store.banner/index'));
        }
        return $this->renderError($model->getError() ?: '删除成功');
    }

}