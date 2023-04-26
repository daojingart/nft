<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/11   11:43
 * +----------------------------------------------------------------------
 * | className:   藏品管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\collection;


use app\admin\controller\Controller;
use app\admin\model\collection\Convert as ConvertModel;
use app\admin\model\collection\Goods as GoodsModel;
use app\admin\model\collection\Category as CategoryModel;
use app\admin\model\collection\Writer as WriterModel;


class Goods extends Controller
{

    /**
     * 藏品管理列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   10:33
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
        if($this->request->isAjax()){
            $list  = (new GoodsModel())->getList(array_merge($this->request->param()));
            return json($list);
        }
        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $this->assign(['category' => $category,'writer' => $writer]);
        return $this->fetch();
    }


    /**
     * 获取合成藏品的子藏品列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   10:33
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function synthesis()
    {
        if($this->request->isAjax()){
            $param = $this->request->param();
            switch ($param['types'])
            {
                case "kt": //空投 归属的藏品  //标签
                    $list  = (new GoodsModel())->getList(array_merge($this->request->param(),[
                        'types' => ['in',['1','2','3','4','5']],
                    ]));
                    break;
                case "yxg": //优先购、合成
                    $list  = (new GoodsModel())->getList(array_merge($this->request->param(),[
                        'types' => ['in',['1','3']],
                    ]));
                    break;
                case "exchange": //选择藏品门槛
                    $list  = (new GoodsModel())->getList(array_merge($this->request->param(),[
                        'types' => ['in',['1','2','4','5','6']],
                    ]));
                    break;
                default:
                    $list  = (new GoodsModel())->getList(array_merge($this->request->param(),[
                        'types' => ['in',['1','5','2']],
                    ]));
                    break;
            }
            return json($list);
        }
        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $this->assign(['category' => $category,'writer' => $writer]);
        return $this->fetch();
    }





    /**
     * 添加藏品
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws `\think\exception\DbException
     * @Time: 2022/6/13   15:55
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add()
    {
        $WriterModel = new GoodsModel();
        if($this->request->isAjax()){
            if ($WriterModel->add($this->postData('goods'))) {
                return $this->renderSuccess('添加成功',url('collection.goods/index'));
            }
            return $this->renderError($WriterModel->getError() ?: '添加失败');
        }
        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0,'status'=>0])->field('id,name')->select();
        return $this->fetch('',compact('category', 'writer'));
    }

    /**
     * 编辑藏品
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   10:09
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface renew
     */
    public function renew()
    {
        $model = GoodsModel::detail($this->request->param("goods_id"));
        if ($this->request->isAjax()) {
            if ($model->edit($this->postData('goods'))) {
                return $this->renderSuccess('更新成功',url('collection.goods/index'));
            }
            pre($model->getError());
            return $this->renderError($model->getError() ?: '更新失败');
        }
        // 开售时间
        $model['start_time'] = date('Y-m-d H:i:s',$model['start_time']);
        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0,'status'=>0])->field('id,name')->select();
        return $this->fetch('renew', compact('model','category','writer'));
    }

    /**
     * 删除藏品
     * @param $goods_id
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   10:08
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface remove
     */
    public function remove($goods_id)
    {
        $model = GoodsModel::detail($goods_id);
        if (!$model->setDelete($model['product_types'])) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 操作商品上下架
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   15:55
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface operation
     */
    public function operation()
    {
        $param = $this->request->param();
        if((new GoodsModel())->updateGoodsStatus($param)){
            return $this->renderSuccess('处理成功');
        }
        return $this->renderError("操作失败");
    }

    /**
     * 获取藏品对应的兑换码列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   11:57
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getConvertList
     */
    public function getConvertList()
    {
        if($this->request->isAjax()){
            $list  = (new ConvertModel())->getList($this->request->param());
            return json($list);
        }
        $this->assign(['goods_id' => $this->request->param('goods_id')]);
        return $this->fetch('convert');
    }

    /**
     * 添加兑换码
     * @return array
     * @throws \Exception
     * @Time: 2022/6/16   14:04
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface addConvert
     */
    public function addConvert()
    {
        $model = new ConvertModel();
        $param = $this->request->param();
        if($param['num'] < 0){
            return $this->renderError("库存数量必须大于0");
        }
        if($param['num'] > 200){
            return $this->renderError("请输入1-200的整数");
        }

        if ($model->addConvert($param)) {
            return $this->renderSuccess('设置成功');
        }
        return $this->renderError($model->getError() ?: '设置失败');
    }

    /**
     * 删除兑换码
     * @param $convert_id
     * @return array
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   14:46
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface removeConvert
     */
    public function removeConvert($convert_id)
    {
        // 判断当前兑换码状态 , 如果已使用 不可删除
        $model = ConvertModel::detail($convert_id);
        if($model['status'] != 0){
            return $this->renderError("此兑换码已使用,不可删除");
        }

        if (!$model->delConvert($convert_id)) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 上架
     *
     * @param $goods_id
     * @return array
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/22 10:54
     * @author Mr.Liu
     */
    public function putaway($goods_id)
    {
        $model = GoodsModel::detail($goods_id);
        if (!$model->putaway()) {
            return $this->renderError($model->getError() ?: '上架失败');
        }
        return $this->renderSuccess('上架成功');
    }

    /**
     * 修改售罄状态
     * @param $goods_id
     * @return array
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/22 10:54
     * @author Mr.Liu
     */
    public function soldstatus()
    {
        $param = $this->request->param();
        if((new GoodsModel())->soldstatus($param)){
            return $this->renderSuccess('处理成功');
        }
        return $this->renderError("操作失败");
    }


    /**
     * 藏品推荐列表
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   10:33
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function recommend()
    {
        if($this->request->isAjax()){
            $list  = (new GoodsModel())->getRecommendList(array_merge($this->request->param()));
            return json($list);
        }
        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $this->assign(['category' => $category,'writer' => $writer]);
        return $this->fetch();
    }


    /**
     * 修改推荐状态
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function editRecommend()
    {
        $goods_ids = $this->request->param()['goods_ids'];
        (new GoodsModel())->where(['goods_id'=>['in',$goods_ids]])->update(['is_recommend'=>10]);
        return $this->renderSuccess("修改成功");
    }

    /**
     * 删除推荐
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function removeRecommend()
    {
        $goods_id = $this->request->get("goods_id");
        (new GoodsModel())->where(['goods_id'=>$goods_id])->update(['is_recommend'=>20]);
        return $this->renderSuccess("删除推荐成功");
    }

}