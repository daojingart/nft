<?php

namespace app\api\controller\goods;

use app\admin\model\Setting;
use app\api\model\order\Product;
use app\api\validate\ProductValidate;
use app\common\controller\Controller;
use app\common\model\ProductCategoryGoods;
use app\api\model\collection\Product as ProductModel;

/**
 * 实物商城
 */
class Products extends Controller
{
    protected $noNeedLogin = [
        'getCategoryList','getGoodsList','getLaunchCalendar','getDetails','getBuyRule'
    ];
    /**
     * 获取分类菜单
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.products/getCategoryList)
     * @ApiReturnParams   (name="category_id", type="string", description="分类的ID")
     * @ApiReturnParams   (name="name", type="string", description="菜单名称")
     * @ApiReturnParams   (name="image_url", type="string", description="菜单图标")
     */
    public function getCategoryList()
    {
        $this->success("获取成功",(new ProductCategoryGoods())->field("category_id,name,image_url")->select());
    }


    /**
     * 实物商城列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.products/getGoodsList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiParams   (name="category_id", type="string", required=true, description="分类ID")
     * @ApiParams   (name="title", type="string", required=true, description="搜索的标题")
     * @ApiReturnParams   (name="system_name", type="string", description="系统名称")
     */
    public function getGoodsList()
    {
        $category_id = $this->request->post('category_id');
        $title = $this->request->post('title');
        $data = (new \app\api\model\collection\Product())->getList(20,$category_id,$title);
        $this->success('获取数据成功', $data);
    }



    /**
     * 实物商城详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.products/getProducyInfo)
     * @ApiParams   (name="product_id", type="string", required=true, description="产品ID")
     */
    public function getProducyInfo()
    {
        (new ProductValidate())->goCheck('getProducyInfo');
        $data = (new ProductModel())->getProducyInfo(20);
        if ($data){
            $this->success('ok',$data);
        }
        $this->error('产品不存在');
    }

    /**
     * 实物商城须知
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.products/getShopTips)
     * @ApiReturnParams   (name="data", type="string", description="实物商城须知")
     */
    public function getShopTips()
    {
        $values = Setting::getItem("read")['shop_tips'];
        $this->success("获取成功",$values);
    }

}