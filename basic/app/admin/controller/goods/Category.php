<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 11:08
 */


namespace app\admin\controller\goods;

use app\admin\controller\Controller;
use app\admin\model\product\ProductCategoryGoods as ProductCategoryModel;

/**
 * 商城分类
 * Class ProductCategory
 * @package app\admin\controller\collection
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 11:08
 */
class Category extends Controller
{
    /**
     * 分类列表
     * @return mixed|\think\response\Json
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 11:40
     */
    public function index()
    {
        if($this->request->isAjax()) {
            $param = input('param.');
            $model = new ProductCategoryModel;
            $list = $model->getListAll($param);
            return json($list);
        }
        return $this->fetch('index');
    }

    /**
     * 添加分类
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 11:19
     */
    public function add()
    {
        $model = new ProductCategoryModel;
        if ($this->request->isAjax()) {
            // 新增记录
            if ($model->add($this->postData('category'))) {
                return $this->renderSuccess('更新成功', url('index'));
            }
            return $this->renderError($model->getError() ?: '更新失败');
        }
        return $this->fetch('add');
    }

    /**
     * 更新分类
     * @param $category_id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 11:41
     */
    public function renew($category_id)
    {
        // 模板详情
        $model = ProductCategoryModel::get($category_id);
        if (!$this->request->isAjax()) {
            // 获取所有地区
            return $this->fetch('renew', compact('model'));
        }
        // 更新记录
        if ($model->edit($this->postData('category'))) {
            return $this->renderSuccess('更新成功', url('index'));
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }

    /**
     * 删除分类
     * @param $category_id
     * @return array
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 11:41
     */
    public function remove($category_id)
    {
        $model = ProductCategoryModel::get($category_id);
        if (!$model->remove($category_id)) {
            $error = $model->getError() ?: '删除失败';
            return $this->renderError($error);
        }
        return $this->renderSuccess('删除成功');
    }
}