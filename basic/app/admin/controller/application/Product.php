<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 10:11
 */
namespace app\admin\controller\application;


use app\admin\controller\Controller;
use app\admin\model\collection\Goods as GoodsModel;
use app\admin\model\product\Product as ProductModel;
use app\admin\model\product\ProductCategory as ProductCategoryModel;
/**
 * 产品控制器
 * Class Product
 * @package app\admin\controller\product
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 10:11
 */
class Product extends Controller
{
    /**
     * 产品列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 10:12
     */
    public function index()
    {
        if (request()->isAjax()){
            return  (new ProductModel())->getList(10);
        }
        return $this->fetch();
    }

    /**
     * 添加产品
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 10:12
     */
    public function add()
    {
        if (request()->isAjax()){
            $param = request()->param();
            if ((new ProductModel())->addProduct($param['product'])){
                return  $this->renderSuccess('创建成功',url('index'));
            }
            return $this->renderError('创建失败');
        }
        return $this->fetch();
    }

    /**
     * 编辑产品
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 10:13
     */
    public function edit()
    {
        $param = request()->param();
        if (!array_key_exists('product_id',$param)){
            return  $this->redirect(url('index'));
        }
        $data = ProductModel::where('product_id',$param['product_id'])->with(['image','category', 'spec', 'spec_rel.spec'])->find();
        if (!$data){
            return  $this->redirect(url('index'));
        }
        if (request()->isAjax()){
            if ((new ProductModel())->editProduct($param['product'])){
                return  $this->renderSuccess('更新成功',url('index'));
            }
            return $this->renderError('更新失败');
        }
        if ($data['spec_type'] == 20)
            $specData = json_encode($data->getManySpecData($data['spec_rel'], $data['spec']));

        $data['goods_info'] = GoodsModel::detail($data['hold_goods_id']);
        $this->assign([
            'data' => $data,
            'specData' => isset($specData)?$specData:''
        ]);
        return $this->fetch();
    }

    /**
     * 删除产品
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 10:13
     */
    public function del()
    {
      $param = request()->param();
      if ((new ProductModel())->del($param['goods_id'])){
          return  $this->renderSuccess('删除成功',url('index'));
      }
        return $this->renderError('删除失败');
    }

    /**
     * 更新上下架
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 14:14
     */
    public function operation()
    {
        $param = request()->param();
        $product = ProductModel::where('product_id',$param['goods_id'])->find();
        if (!$product){
            return $this->renderError('更新失败');
        }
        $status = 10;
        if ($product->goods_status==10){
            $status = 20;
        }
        $product->goods_status = $status;
        if ($product->save()){
            return  $this->renderSuccess('更新成功');
        }
        return  $this->renderError('更新失败');
    }
}