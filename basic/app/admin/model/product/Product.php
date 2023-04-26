<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 10:42
 */
namespace app\admin\model\product;
use app\admin\model\ProductSpec;
use app\common\model\Product as ProductModel;
use think\Db;


/**
 * 产品业务
 * Class Product
 * @package app\admin\model\product
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 10:42
 */
class Product extends ProductModel
{
    public  $page = 1;
    public  $limit = 10;


    /**
     * 关联商品规格表
     * @return \think\model\relation\HasMany
     */
    public function spec()
    {
        return $this->hasMany('app\admin\model\ProductSpec','goods_id','product_id');
    }

    /**
     * 关联商品规格关系表
     * @return \think\model\relation\BelongsToMany
     */
    public function specRel()
    {
        return $this->belongsToMany('app\admin\model\SpecValue', 'ProductSpecRel','spec_value_id','goods_id');
    }
    /**
     * 获取产品列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 13:54
     */
    public function getList($product_type)
    {
        $param = request()->param();
        $where = [];
        if (array_key_exists('product_name',$param)&&!empty($param['product_name'])){
            $where['product_name'] = ['like','%'.$param['product_name'].'%'];
        }
        $where['product_type'] = $product_type;
        if (array_key_exists('goods_status',$param)&&!empty($param['goods_status'])){
            $where['goods_status'] = $param['goods_status'];
        }
        if (array_key_exists('category_id',$param)&&!empty($param['category_id'])){
            $where['category_id'] = $param['category_id'];
        }
        $this->page = request()->param('page')?:1;//当前第几页
        $this->limit = request()->param('limit')?:10;//每页显示几条
        $data = $this->with(['image','category','spec'])->order('sort desc')->where($where)->paginate($this->limit,false,$config = ['page'=>$this->page])->toArray();
        foreach ($data['data'] as &$item){
            $item['operate'] = showNewOperate(self::makeButton($item['product_id'],$product_type));
            $item['category_name'] = $item['category']['name'];
            $item['goods_price'] = $item['spec'][0]['goods_price'];
            //计算销量
            $goods_sales = 0;
            //计算库存
            $stock_num = 0;
            foreach ($item['spec'] as $values){
                $goods_sales+=$values['goods_sales'];
                $stock_num+=$values['stock_num'];
            }
            $item['goods_sales'] = $goods_sales;
            $item['stock_num'] = $stock_num;
        }
        $arr['data'] = $data['data'];
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = $data['total'];
        return  json($arr);
    }
    private static  function makeButton($id,$product_type)
    {
        $button =  [
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ],
        ];
        if($product_type ==10){
            $button['编辑'] = [
                'href' => url('application.product/edit', ['product_id' => $id]),
                'lay-event' => '',
            ];
        }else{
            $button['编辑'] = [
                'href' => url('goods.goods/edit', ['product_id' => $id]),
                'lay-event' => '',
            ];
        }

        return $button;
    }
    /**
     * 添加产品
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 10:45
     */
    public function addProduct($param)
    {
        Db::startTrans();
        try{
            //创建产品
            $this->allowField(true)->save($param);
            //创建产品图片
            $image = [];
            foreach ($param['image'] as $item){
                array_push($image,['image'=>$item,'product_id'=>$this->getLastInsID()]);
            }
            $this->addGoodsSpec($param);
            (new ProductImage())->saveAll($image);
            Db::commit();
            return  true;
        } catch (\Exception $e) {
            Db::rollback();
            halt($e->getMessage());
            write_log($e->getMessage(),RUNTIME_PATH,'product_add');
            return false;
        }
    }
    /**
     * 添加商品规格
     * @param $data
     * @param $isUpdate
     * @throws \Exception
     */
    public function addGoodsSpec(&$data, $isUpdate = false)
    {
        // 更新模式: 先删除所有规格
        $model = new ProductSpec();
        $isUpdate && $model->removeAll($this['product_id']);
        // 添加规格数据
        if ($data['spec_type'] == '10') {
            // 单规格
            $this->spec()->save($data['spec']);
        } else if ($data['spec_type'] == '20') {
            // 添加商品与规格关系记录
            $model->addGoodsSpecRel($this['product_id'], $data['spec_many']['spec_attr']);
            // 添加商品sku
            $model->addSkuList($this['product_id'], $data['spec_many']['spec_list']);
        }
    }
    /**
     * 编辑产品
     * @param $param
     * @return bool
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 14:47
     */
    public function editProduct($param)
    {
        Db::startTrans();
        try{
            //创建产品
            $this->update($param,['product_id'=>$param['product_id']],true);
            //创建产品图片
            $image = [];
            $this->product_id = $param['product_id'];
            $this->addGoodsSpec($param, true);
            foreach ($param['image'] as $item){
                array_push($image,['image'=>$item,'product_id'=>$param['product_id']]);
            }
            //删除产品图片
            (new ProductImage())->where('product_id',$param['product_id'])->delete();
            //产品图片
            (new ProductImage())->saveAll($image);
            Db::commit();
            return  true;
        } catch (\Exception $e) {
            pre($e);
            Db::rollback();
            write_log($e->getMessage(),RUNTIME_PATH,'product_edit');
            return false;
        }
    }

    /**
     * 删除产品
     * @param $product_id
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 14:47
     */
    public function del($product_id)
    {
        $product = $this->where('product_id',$product_id)->find();
        if (!$product_id){
            return false;
        }
        Db::startTrans();
        try{
            $product->delete();
            (new ProductImage())->where('product_id',$product_id)->delete();
            Db::commit();
            return  true;
        } catch (\Exception $e) {
            Db::rollback();
            write_log($e->getMessage(),RUNTIME_PATH,'product_del');
            return false;
        }
    }

    public function getManySpecData($spec_rel, $skuData)
    {
        // spec_attr
        $specAttrData = [];
        foreach ($spec_rel->toArray() as $item) {
            if (!isset($specAttrData[$item['spec_id']])) {
                $specAttrData[$item['spec_id']] = [
                    'group_id' => $item['spec']['spec_id'],
                    'group_name' => $item['spec']['spec_name'],
                    'spec_items' => [],
                ];
            }
            $specAttrData[$item['spec_id']]['spec_items'][] = [
                'item_id' => $item['spec_value_id'],
                'spec_value' => $item['spec_value'],
            ];
        }

        // spec_list
        $specListData = [];
        foreach ($skuData->toArray() as $item) {
            $specListData[] = [
                'goods_spec_id' => $item['goods_spec_id'],
                'spec_sku_id' => $item['spec_sku_id'],
                'rows' => [],
                'form' => [
                    'goods_no' => $item['goods_no'],
                    'goods_price' => $item['goods_price'],
                    'goods_weight' => $item['goods_weight'],
                    'line_price' => $item['line_price'],
                    'group_activities_price' => $item['group_activities_price'],
                    'stock_num' => $item['stock_num'],
                    'cost_price' => $item['cost_price'],
                ],
            ];
        }
        return ['spec_attr' => array_values($specAttrData), 'spec_list' => $specListData];
    }
}