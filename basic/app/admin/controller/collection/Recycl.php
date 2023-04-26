<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-08-20 13:47
 */


namespace app\admin\controller\collection;


use app\admin\controller\Controller;
use app\admin\model\collection\Category as CategoryModel;
use app\admin\model\collection\Goods;
use app\admin\model\collection\Writer as WriterModel;

/**
 * 官方回收
 * Class Recycl
 * @package app\admin\controller\collection
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-08-20 13:48
 */
class Recycl extends Controller
{
    public function index()
    {
        // 商品分类
        $category = CategoryModel::where(['type' => 1,'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $this->assign(['category' => $category,'writer' => $writer]);
        return $this->fetch();
    }

    /**
     * 获取官方回收列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 11:48
     */
    public function secureOfficialDeal()
    {
        $param = request()->param();
        return (new Goods())->secureOfficialDeal($param);
    }
    public function operation()
    {
        $param = request()->param();
        $res = (new Goods())->operation($param);
        if ($res){
            return $this->renderSuccess('更新成功');
        }
        return  $this->renderError('更新失败');
    }
}