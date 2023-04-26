<?php

namespace app\api\model\order;

use app\api\model\goods\Delivery;
use app\api\model\goods\GoodsSpec;
use app\common\model\MemberAddress;
use app\common\model\ProductOrder;
use think\Db;

class GoodsOrder extends ProductOrder
{
    /**
     * @Notes: 订单确认-立即购买(商城模块)
     * @Interface getBuyNow
     * @param $user
     * @param $goods_id
     * @param $goods_num
     * @param $goods_sku_id
     * @param string $order_type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/6/19   11:06
     */
    public function getGoodsBuyNow($user, $goods_id, $goods_num, $goods_sku_id)
    {
        // 商品信息
        /* @var Product $goods */
        $goods = \app\common\model\Product::detail($goods_id)->hidden(['category', 'content', 'spec']);
        // 判断商品是否下架
        if ($goods['goods_status'] != 10) {
            $this->setError('很抱歉，商品信息不存在或已下架');
        }
        // 商品sku信息
        $goods['goods_sku'] = $goods->getGoodsSku($goods_sku_id);
        // 判断商品库存
        if ($goods_num > $goods['goods_sku']['stock_num']) {
            $this->setError('很抱歉，商品库存不足');
        }
        // 商品单价
        $goods['goods_price'] = $goods['goods_sku']['goods_price'];

        // 商品购买总数量
        $goods['total_num'] = $goods_num;
        //产品总几个
        $goods['total_price'] = $totalPrice = bcmul($goods['goods_price'], $goods_num, 2);

        // 商品总重量
        $goods_total_weight = bcmul($goods['goods_sku']['goods_weight'], $goods_num, 2);
        // 是否存在收货地址
        $memberAddress = (new MemberAddress())->where(['member_id'=>$user['member_id']])->find();
        $default_memberAddress = (new MemberAddress())->where(['member_id'=>$user['member_id'],'select_status'=>1])->find();
        // 当前用户收货城市id
        $cityId = !empty($default_memberAddress) ? $default_memberAddress['city_id'] : null;
        $exist_address = !empty($memberAddress);
        // 验证用户收货地址是否存在运费规则中
        if (!$intraRegion = (new Delivery())->checkAddress($cityId)) {
            $exist_address && $this->setError('很抱歉，您的收货地址不在配送范围内');
        }

        // 计算配送费用
        $expressPrice = $intraRegion ?
            (new Delivery())->calcTotalFee($goods_num, $goods_total_weight, $cityId) : 0;
        return [
            'goods_list' => [$goods],               // 商品详情
            'order_total_num' => $goods_num,        // 商品总数量
            'order_total_price' => $totalPrice,     // 商品总金额 (不含运费)
            'order_pay_price' => bcadd($totalPrice, $expressPrice, 2),  // 实际支付金额
            'address' => $default_memberAddress,  // 默认地址
            'exist_address' => $exist_address,  // 是否存在收货地址
            'express_price' => $expressPrice,    // 配送费用
            'intra_region' => $intraRegion,    // 当前用户收货城市是否存在配送规则中
            'has_error' => $this->hasError(),
            'error_msg' => $this->getError(),
        ];
    }


    /**
     * 创建订单
     * @param $member_id
     * @param $order
     * @return bool
     * @throws \Exception
     */
    public function addGoods($user, $order,$param)
    {
        if (empty($order['address'])) {
            $this->error = '请先选择收货地址';
            return false;
        }
        //判断商城产品是否需要支付积分，需要支付积分判断积分是否足够，足够方可发起支付
        if($user['account'] < $order['order_pay_price']){
            $this->error = '余额不足,无法发起支付！';
            return false;
        }
        Db::startTrans();
        try{
            // 记录订单信息
            $this->createOrder($user['member_id'],$order,$param);
            // 订单商品列表
            $goodsList = [];
            // 更新商品库存 (下单减库存)
            $deductStockData = [];
            foreach ($order['goods_list'] as $goods) {
                /* @var Goods $goods */
//                pre($goods->toArray());
                $goodsList[] = [
                    'app_id' => self::$app_id,
                    'goods_id' => $goods['product_id'],
                    'goods_name' => $goods['product_name'],
                    'goods_image' => $goods['thumbnail'],
                    'spec_type' => $goods['spec_type'],
                    'spec_sku_id' => $goods['goods_sku']['spec_sku_id'],
                    'goods_spec_id' => $goods['goods_sku']['goods_spec_id'],
                    'goods_attr' => $goods['goods_sku']['goods_attr'],
                    'content' => $goods['content'],
                    'goods_no' => $goods['goods_sku']['goods_no'],
                    'goods_price' => $goods['goods_sku']['goods_price'],
                    'goods_weight' => $goods['goods_sku']['goods_weight'],
                    'product_num' => $goods['total_num'],
                    'total_price' => $goods['total_price'],
                ];
                // 下单减库存
                 $deductStockData[] = [
                    'goods_spec_id' => $goods['goods_sku']['goods_spec_id'],
                    'stock_num' => ['dec', $goods['total_num']]
                ];
            }
            // 保存订单商品信息
            $this->OrderGoods()->saveAll($goodsList);
            // 更新商品库存
            !empty($deductStockData) && (new GoodsSpec())->isUpdate()->saveAll($deductStockData);
            // 记录收货地址
//            pre($order['address']->toArray());
            $this->address()->save([
                'member_id' => $user['member_id'],
                'app_id' => self::$app_id,
                'name' => $order['address']['name'],
                'phone' => $order['address']['phone'],
                'province_id' => $order['address']['province_id'],
                'city_id' => $order['address']['city_id'],
                'region_id' => $order['address']['region_id'],
                'detail' => $order['address']['detail'],
                'province_name' =>$order['address']['region']['province'],
                'city_name' =>$order['address']['region']['city'],
                'region_name' =>$order['address']['region']['region'],

            ]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }


    /**
     * @Notes: 创建订单入库订单
     * @Interface createOrder
     * @param $member_id
     * @param $order
     * @param $param
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/4   3:28 下午
     */
    public function createOrder($member_id,$order,$param)
    {
        return $this->save([
            'member_id' => $member_id,
            'product_id' => $param['goods_id'],
            'order_type' => $param['order_type'],
            'pay_type' =>$param['pay_type'],
            'app_id' => self::$app_id,
            'order_no' => $this->orderNo(),
            'total_price' => $order['order_total_price'],
            'pay_price' => $order['order_pay_price'],
            'freight_price' => isset($order['express_price'])?$order['express_price']:0,
        ]);
    }

    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return "GOODS".date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }





    /**
     * 设置错误信息
     * @param $error
     */
    private function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 是否存在错误
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }

}