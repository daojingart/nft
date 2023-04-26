<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 11:48
 */


namespace app\api\model\collection;
use app\admin\model\Setting;
use app\common\components\helpers\RedisUtils;
use app\common\model\Glory;
use app\common\model\MemberAddress;
use app\common\model\MemberGoods;
use app\common\model\Product as ProductModel;
use app\common\model\ProductOrder;
use app\common\model\ProductOrderAddress;
use app\common\model\ProductOrderGoods;
use app\common\model\ProductSpec;
use exception\BaseException;
use think\Db;
class Product extends ProductModel
{
    /**
     * 获取产品列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-27 11:50
     */
    public function getList($product_type,$category_id="",$title="")
    {
        $page = request()->param('page')?:1;//当前第几页
        $list = request()->param('limit')?:10;//每页显示几条
        $where = [];
        $where['goods_status'] = 10;
        $where['is_delete'] = 1;
        $where['product_type'] = $product_type;
        if($category_id){
            $where['category_id'] = $category_id;
        }
        if($title){
            $where['product_name'] = ['like',"%$title%"];
        }
        $data = $this->order('product_id desc')
            ->with(['spec'])
            ->where($where)
            ->field("product_id,thumbnail,product_name")
            ->paginate($list,false,$config = ['page'=>$page])
            ->each(function ($item, $key) {
                $store_name = Setting::getItem("store")['system_name'];
                $item['system_name'] = $store_name;
                return $item;
            })->toArray();

        $data['list'] = $data['data'];
        unset($data['data']);
        return $data;
    }

    /**
     * 获取产品详情
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-27 13:43
     */
    public function getProducyInfo($product_type)
    {
        $product_id = request()->param()['product_id'];
        $data =  $this->with(['image','category', 'spec', 'spec_rel.spec'])->where('product_type',$product_type)->where('product_id',$product_id)->find();
        $this->isStatus($data);
        $specData = $data['spec_type'] == 20 ? $data->getManySpecData($data['spec_rel'], $data['spec']) : null;
        $data['specData'] = $specData;
        $data['content'] = htmlspecialchars_decode($data['content']);
        $stock_num = 0;
        foreach ($data['spec'] as $values){
            $stock_num+=$values['stock_num'];
        }
        $data['inventory'] = $stock_num;
        $data['thumbnail'] = $data['image'][0]['image'];
        return $data;
    }

    /**
     * 产品预下单
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-27 14:25
     * @param $member_id
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function preOrder($member,$product_type)
    {
        $member_id = $member['member_id'];
        $param = request()->param();
        $lockKey = "createProductOrder:{$param['product_id']}";
        $product = $this->where(['product_id'=>$param['product_id'],'product_type'=>$product_type])->find();
        $this->isStatus($product);
        $address = (new MemberAddress())->where('address_id',$param['address_id'])->where('member_id',$member_id)->find();
        if (!$address){
            RedisUtils::unlock($lockKey);
            throw new BaseException(['msg'=>'收货地址不存在']);
        }
        $spec = (new ProductSpec())->where('spec_sku_id',$param['sku_id'])->where('goods_id',$param['product_id'])->find();
        if (!$spec){
            RedisUtils::unlock($lockKey);
            throw new BaseException(['msg'=>'抱歉产品不存在']);
        }
        if ($spec['stock_num']<$param['numberOf']){
            throw new BaseException(['msg'=>'抱歉库存不足']);
        }
        //所需荣誉值
        $price = bcmul($spec['goods_price'],$param['numberOf'],2);
        if ($member['glory']<$price){
            RedisUtils::unlock($lockKey);
            throw new BaseException(['msg'=>'很抱歉积分不足']);
        }
        //判断是否需要持有藏品才可以兑换
        if($product['hold_goods_status']==20){
            $member_goods_count = (new MemberGoods())->where(['member_id'=>$member_id,'goods_status'=>0,'is_donation'=>0,'goods_id'=>$product['hold_goods_id']])->count();
            if($member_goods_count==0){
                RedisUtils::unlock($lockKey);
                throw new BaseException(['msg'=>'抱歉您无法兑换该商品;请您持有权益藏品才可以兑换！']);
            }
        }
        if($product['limit_buy_number']!=0){
            $count_number = (new \app\common\model\ProductOrder())->alias('o')
                ->whereTime("pay_time", "m")
                ->join('product_order_goods og', 'o.order_id = og.order_id', 'left')
                ->where(['og.goods_id' => $param['product_id'], 'o.member_id' => $member_id, 'order_status' => ['<>', 5]])
                ->count();
            if ($count_number >= $product['limit_buy_number']) {
                RedisUtils::unlock($lockKey);
                throw new BaseException(['msg'=>'本月兑换次数已经用完！']);
            }
        }
        //兑换产品
        Db::startTrans();
        try{
            //增加销量
            $product->setInc('sales_actual',$param['numberOf']);
            //减少库存
            $spec->setDec('stock_num',$param['numberOf']);
            //添加积分记录
            $integralData = [
                'member_id' => $member_id,
                'type' => 5,
                'amount' => -$price,
                'remark' => '兑换'.$product['product_name'].'减少'.$price
            ];
            (new Glory())->allowField(true)->save($integralData);
            //添加主订单记录
            $order['order_no'] = $this->orderNo();
            $order['total_price'] = $price;
            $order['pay_time'] = time();
            $order['order_status'] = 3;
            $order['member_id'] = $member_id;
            $order['product_id'] = $param['product_id'];
            (new ProductOrder())->create($order);
            //添加地址
            $order_id = (new ProductOrder())->getLastInsID();
            $memberAddress['order_id'] = $order_id;
            $memberAddress['member_id'] = $member_id;
            $memberAddress['name'] = $address['name'];
            $memberAddress['phone'] = $address['phone'];
            $memberAddress['province_id'] = $address['province_id'];
            $memberAddress['city_id'] = $address['city_id'];
            $memberAddress['region_id'] = $address['region_id'];
            $memberAddress['province_name'] = $address['region']['province'];
            $memberAddress['city_name'] = $address['region']['city'];
            $memberAddress['region_name'] = $address['region']['region'];
            $memberAddress['detail'] = $address['detail'];
            $memberAddress['app_id'] = 10001;
            (new ProductOrderAddress())->create($memberAddress);
            //添加产品信息
            $productData['order_id'] = $order_id;
            $productData['goods_id'] = $param['product_id'];
            $productData['goods_name'] = $product['product_name'];
            $productData['goods_no'] = $product['spec'][0]['goods_no'];
            $productData['goods_image'] = $product['thumbnail'];
            $productData['sku_id'] = $param['sku_id'];
            $productData['pay_price'] = $price;
            $productData['total_price'] = $price;
            $productData['discount_price'] = 0;
            $productData['product_num'] = $param['numberOf'];
            (new ProductOrderGoods())->create($productData);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            RedisUtils::unlock($lockKey);
            Db::rollback();
        }
    }

    /**
     * 判断商品状态
     * @param $data 单个产品结果集
     * @throws BaseException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-27 14:28
     */
    public function isStatus($data)
    {
        if (!$data){
            throw new BaseException(['msg'=>'抱歉产品不存在']);
        }
        if ($data['is_delete']==0||$data['goods_status']==20){
            throw new BaseException(['msg'=>'抱歉当前产品不能查看']);
        }
    }


    protected function orderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }

}