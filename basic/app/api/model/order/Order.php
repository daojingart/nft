<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/29   2:14 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 订单模型
 * +----------------------------------------------------------------------
 */

namespace app\api\model\order;

use app\admin\model\Setting;
use app\api\model\collection\Goods;
use app\api\model\collection\Goods as GoodsModel;
use app\common\components\helpers\StockUtils;
use app\common\constants\GoodsConst;
use app\common\controller\Task;
use app\common\model\Appointment;
use app\common\model\Blindbox;
use app\common\model\GloryGoods;
use app\common\model\GoodsPrecedence;
use app\common\model\Member;
use app\common\model\MemberBox;
use app\common\model\MemberGoods;
use app\common\model\OrderGoods;
use app\common\model\Order as OrderModel;
use app\notice\model\Order as OrderNoticeModel;
use exception\BaseException;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;


class Order extends OrderModel
{


    /**
     * 创建订单
     * @param $user
     * @param $params
     * @return bool
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public function add($user, $params)
    {
        Db::startTrans();
        try {
            $goodsInfo = (new Goods())::detail($params['goods_id']);
            //非二级市场的交易
            if ($params['order_type'] != 3 && $params['order_type'] != 11) {
                if (in_array($goodsInfo['product_types'], ['1', '3', '4'])) {
                    if (!StockUtils::getStock($params['goods_id'])) {
                        $this->error = "可购买库存不足请稍后重试！";
                        return false;
                    }
                }
            }
            //判断订单是否限购 限购需要判断处理下购买的数量参数
            if ($goodsInfo['buy_num'] != 0 && $params['order_type'] != 3 && $params['order_type'] != 5 && $params['order_type'] != 11)  {
                //计算订单数量
                $count_number = (new OrderModel())->alias('o')
                    ->join('order_goods og', 'o.order_id = og.order_id', 'left')
                    ->where(['og.goods_id' => $params['goods_id'], 'o.member_id' => $user['member_id'], 'order_status' => ['<>', 5], 'o.order_type' => $params['order_type']])
                    ->count();
                //判断是否在优先购增加次数内 在的话减少这个名单内的 可以购买次数
                $buy_number = getFirstInfoNumber($params['goods_id'],$user['member_id']);
                if($buy_number>0){
                    $count_number = bcsub($count_number,$buy_number);
                    $params['is_limit'] = 30;
                }
                if ($count_number >= $goodsInfo['buy_num']) {
                    //因为限购 次数过了 判断下会员表中的限购次数是否存在  判断藏品是否开启这个藏品可以使用限购次数
                    if ($goodsInfo['is_limit_number'] == 20) {
                        if ($user['purchase_limit'] > 0) { //表示有次数购买 增加一个标识
                            $params['is_limit'] = 20;
                        } else {
                            $this->error = "商品限购无法重复购买";
                            return false;
                        }
                    } else {
                        $this->error = "商品限购无法重复购买";
                        return false;
                    }
                }
            }

            //寄售订单  查看寄售的价格
            if ($params['order_type'] == 3 || $params['order_type']==11) {
                if($params['order_type']==3){
                    $memberGoods = (new MemberGoods())->where(['id'=>$params['id']])->find();
                }else{
                    $memberGoods = (new MemberBox())->details(['id'=>$params['id']]);
                }
                $goodsInfo['goods_price'] = $memberGoods['sale_price'];
                $params['sale_member_id'] = $memberGoods['member_id'];
            }

            //兑换订单
            if ($params['order_type'] == 5) {
                //订单创建 进行锁单处理 防止重复下单
                $res = (new GloryGoods())->where(['goods_id' => $params['goods_id']])->find();
                if (!empty($res)) {
                    $goodsInfo['goods_price'] = $res['price'];
                }
                $params['order_type'] = 5;
                if ($res['type'] == 2) {
                    $params['order_type'] = 9;
                }
            }

            // 记录订单信息
            $this->createOrder($user['member_id'], $goodsInfo, $params);
            $goodsList = [
                'order_id'    => $this['order_id'],
                'member_id'   => $user['member_id'],
                'app_id'      => self::$app_id,
                'goods_id'    => $goodsInfo['goods_id'],
                'goods_name'  => $goodsInfo['goods_name'],
                'goods_image' => $goodsInfo['goods_thumb'],
                'goods_no'    => $goodsInfo['goods_no'],
                'pay_price'   => $goodsInfo['goods_price'],
                'total_price' => $goodsInfo['goods_price'],
                'product_num' => 1,
                'order_type'  => $params['order_type'] ?? 1,
            ];
            //保存订单商品信息
            (new OrderGoods())->allowField(true)->save($goodsList);
            //减少一个限购次数
            if (isset($params['is_limit']) && $params['is_limit'] == 20 && $user['purchase_limit'] > 0) {
                (new Member())->where(['member_id' => $user['member_id']])->setDec('purchase_limit', 1);
            }
            if($params['order_type']==3){
                (new MemberGoods())->where(['id'=>$params['id']])->update(['goods_status'=>2]);
            }
            if($params['order_type']==11){
                (new MemberBox())->where(['id'=>$params['id']])->update(['box_status'=>30]);
            }
            if($params['order_type']!=3 && $params['order_type']!=11){
                if (!StockUtils::deductStock($goodsInfo['goods_id'], 1)) {
                    $this->error = "内部错误-下单失败--2";
                    return false;
                }
            }
            if ($params['order_type'] == 6) {
                //添加藏品信息
                $goodsData = [
                    'member_id'   => $user['member_id'],
                    'order_id'    => $this['order_id'],
                    'order_no'    => $this['order_no'],
                    'goods_id'    => $goodsInfo['goods_id'],
                    'phone'       => $user['phone'],
                    'nickname'    => $user['name'],
                    'goods_no'    => $goodsInfo['goods_no'],
                    'goods_name'  => $goodsInfo['goods_name'],
                    'goods_thumb' => $goodsInfo['goods_thumb'],
                    'goods_price' => $goodsInfo['goods_price'],
                    'total_num'   => 1,
                    'writer_id'   => $goodsInfo['writer_id'],
                    'writer_name' => $goodsInfo['writer']['name'] ?? '',
                    'hash_url'    => '',
                    'source_type' => '7',
                    'app_id'      => '10001',
                ];
                (new MemberGoods())->add($goodsData);
                (new Member())->where(['member_id' => $user['member_id']])->setDec('volume_drop', 1);
                $member_goods_insertId = (new MemberGoods())->getLastInsID();
                //加入铸造藏品的队列
                (new OrderNoticeModel())->castingQuestionList($member_goods_insertId, $goodsInfo['goods_id'], $user['member_id'], "casting_question_list");
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    //空投盲盒订单
    public function addBoxOrder($user, $params)
    {
        $goodsInfo = (new Goods())::detail($params['goods_id']);
        if (empty($goodsInfo)) {
            $this->error = "商品信息不存在";
            return false;
        }
        $this->createOrder($user['member_id'], $goodsInfo, $params);
        $order_id = $this->getLastInsID();
        // 记录订单信息
        $goodsList = [
            'order_id'    => $this['order_id'],
            'member_id'   => $user['member_id'],
            'app_id'      => '10001',
            'goods_id'    => $goodsInfo['goods_id'],
            'goods_name'  => $goodsInfo['goods_name'],
            'goods_image' => $goodsInfo['goods_thumb'],
            'goods_no'    => "",
            'pay_price'   => $goodsInfo['goods_price'],
            'total_price' => $goodsInfo['goods_price'],
            'product_num' => 1,
            'order_type'  => $params['order_type'] ?? 1,
        ];
        //保存订单商品信息
        (new OrderGoods())->allowField(true)->save($goodsList);
        return $order_id;
    }


    /**
     * 创建订单
     * @param $member_id
     * @param $order
     * @return bool
     * @throws \Exception
     */
    public function addGoods($user, $order, $param)
    {
        if (empty($order['address'])) {
            $this->error = '请先选择收货地址';
            return false;
        }
        //判断商城产品是否需要支付积分，需要支付积分判断积分是否足够，足够方可发起支付
        if ($param['pay_type'] == '4') {
            if ($user['account'] < $order['order_pay_price']) {
                $this->error = '余额不足,无法发起支付！';
                return false;
            }
        }
        Db::startTrans();
        try {
            // 记录订单信息
            $this->createOrder($user['member_id'], $order, $param);
            // 订单商品列表
            $goodsList = [];
            // 更新商品库存 (下单减库存)
            $deductStockData = [];
            foreach ($order['goods_list'] as $goods) {
                //                pre($goods->toArray());
                /* @var Goods $goods */
                $goodsList[] = [
                    'member_id'         => $user['member_id'],
                    'app_id'            => self::$app_id,
                    'goods_id'          => $goods['goods_id'],
                    'goods_name'        => $goods['goods_name'],
                    'image'             => $goods['image'][0]['file_path'],
                    'deduct_stock_type' => $goods['deduct_stock_type'],
                    'spec_type'         => $goods['spec_type'],
                    'spec_sku_id'       => $goods['goods_sku']['spec_sku_id'],
                    'goods_spec_id'     => $goods['goods_sku']['goods_spec_id'],
                    'goods_attr'        => $goods['goods_sku']['goods_attr'],
                    'content'           => $goods['content'],
                    'goods_no'          => $goods['goods_sku']['goods_no'],
                    'goods_price'       => $goods['goods_sku']['goods_price'],
                    'score_price'       => $goods['goods_sku']['score_price'],
                    'cost_price'        => $goods['goods_sku']['cost_price'],
                    'goods_weight'      => $goods['goods_sku']['goods_weight'],
                    'total_num'         => $goods['total_num'],
                    'total_price'       => $goods['total_price'],
                    'total_score_price' => 0,
                    'is_tripartite'     => 0,
                    'coupons_id'        => 0,
                    'discount_number'   => 0,
                    'goods_type'        => 0,
                ];
                // 下单减库存
                $goods['deduct_stock_type'] == 10 && $deductStockData[] = [
                    'goods_spec_id' => $goods['goods_sku']['goods_spec_id'],
                    'stock_num'     => ['dec', $goods['total_num']],
                ];
            }
            // 保存订单商品信息
            $this->goods()->saveAll($goodsList);
            // 更新商品库存
            !empty($deductStockData) && (new GoodsSpec())->isUpdate()->saveAll($deductStockData);
            // 记录收货地址
            $this->address()->save([
                'member_id'   => $user['member_id'],
                'app_id'      => self::$app_id,
                'name'        => $order['address']['name'],
                'phone'       => $order['address']['phone'],
                'province_id' => $order['address']['province_id'],
                'city_id'     => $order['address']['city_id'],
                'region_id'   => $order['address']['region_id'],
                'detail'      => $order['address']['detail'],
            ]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            pre($e);
            Db::rollback();
            return $e->getMessage();
        }
    }


    /**
     * @Notes     : 创建订单入库订单
     * @Interface createOrder
     * @param $member_id
     * @param $order
     * @param $param
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/1/4   3:28 下午
     */
    public function createOrder($member_id, $goodsInfo, $param)
    {
        if ($param['order_type'] == 6) {
            $order_status = 2;
            $pay_status   = 2;
        }
        return $this->save([
            'member_id'      => $member_id,
            'order_type'     => $param['order_type'],
            'app_id'         => '10001',
            'order_no'       => $this->orderNo(),
            'total_price'    => $goodsInfo['goods_price'],
            'pay_price'      => $goodsInfo['goods_price'],
            'order_status'   => $order_status ?? 1,
            'pay_status'     => $pay_status ?? 1,
            'sale_member_id' => $param['sale_member_id'] ?? 0,
            'sale_goods_id'  => $param['id'] ?? 0,
            'is_limit'       => isset($param['is_limit']) ? $param['is_limit'] : 10,
        ]);
    }


    /**
     * 用户中心订单列表
     * @param        $member_id
     * @param string $type
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($member_id = 0, $type = 1, $page)
    {
        // 筛选条件
        $filter = [];
        $member_id > '0' && $filter['member_id'] = $member_id;
        $filter['order_type'] = ['in', [1, 2, 3, 4,12,11]];
        $filter['is_delete'] = 0;
        // 订单数据类型
        switch ($type) {
            case '2': //待付款订单
                $filter['pay_status']   = 1;
                $filter['order_status'] = 1;
                break;
            case '3': //已付款订单
                $filter['pay_status']   = 2;
                $filter['order_status'] = 2;
                break;
            case '4': //已取消
                $filter['order_status'] = 5;
                break;
        }
        return $this->getALlGoodsOrderList($filter, $page)->toArray();
    }

    /**
     * @Notes     : 商城订单列表
     * @Interface getALlGoodsOrderList
     * @param $member_id
     * @param $filter
     * @return bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/1/6   4:20 下午
     */
    public function getALlGoodsOrderList($filter, $page = 1)
    {
        $values = Setting::getItem("order");
        return $this->with(['goods'])
            ->order(['create_time' => 'desc'])
            ->where($filter)
            ->field("order_id,order_no,order_status,pay_status,create_time,total_price,pay_price,order_type")
            ->page($page, 10)
            ->select()->each(function ($item, $key) use ($values) {
                //判断是不是第三方的订单
                $item['create_time'] = strtotime($item['create_time']);
                $item['now_time'] = time();
                $item['end_time'] = strtotime($item['create_time'])+$values['pay_time']*60;
                return $item;
            });
    }


    /**
     * 取消订单
     * @return bool|false|int
     * @throws \Exception
     */
    public function cancel()
    {
        if ($this['pay_status']['value'] == 2) {
            $this->error = '已付款订单不可取消';
            return false;
        }
        if ($this['order_status']['value'] == 5) {
            $this->error = '订单已取消不能进行取消!';
            return false;
        }
        memberOrderLock($this['member_id']);
        //判断现在的产品是不是寄售专区的产品是的话需要修改寄售状态
        if ($this['order_type'] == 3 || $this['order_type']==11) {
            //删除加锁
            $redis = initRedis();
            $locakReidsOrder = 'local_redis_member_id_' . $this['member_id'];
            $redis->del($locakReidsOrder);
            if($this['order_type'] == 3){
                (new MemberGoods())->where(['id' => $this['sale_goods_id']])->update(['goods_status' => 1]);
            }
            if($this['order_type'] == 11){
                (new MemberBox())->where(['id' => $this['sale_goods_id']])->update(['box_status' => 20]);
            }
        } else {
            StockUtils::increaseStock($this['goods'][0]['goods_id'], 1);
            StockUtils::addSoldStock($this['goods'][0]['goods_id'], -1);
        }
        if ($this['is_limit'] == 20) {
            (new Member())->where(['member_id' => $this['member_id']])->setInc('purchase_limit', 1);
        }
        return $this->where(['order_id' => $this['order_id']])->update(['order_status' => 5]);
    }

    /**
     * @Notes     : 删除订单
     * @Interface deleteOrder
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/5   2:46 下午
     */
    public function deleteOrder()
    {
        (new OrderGoods())->where(['order_id' => $this['order_id']])->delete();
        return $this->where(['order_id' => $this['order_id']])->delete();
    }

    /**
     * 回退商品库存
     * @param $goodsList
     * @return array|false
     * @throws \Exception
     */
    private function backGoodsStock(&$goodsList)
    {
        foreach ($goodsList as $goods) {
            (new Goods())->where(['goods_id' => $goods['goods_id']])->setInc('stock_num', 1);
            (new Goods())->where(['goods_id' => $goods['goods_id']])->setDec('sales_actual');
        }
    }

    /**
     * 确认收货
     * @return bool|false|int
     */
    public function receipt()
    {
        if ($this['delivery_status']['value'] == 10 || $this['receipt_status']['value'] == 20) {
            $this->error = '该订单不合法';
            return false;
        }
        return $this->allowField(true)->save([
            'receipt_status' => 20,
            'receipt_time'   => time(),
        ]);
    }

    /**
     * 获取订单总数
     * @param        $member_id
     * @param string $type
     * @return int|string
     */
    public function getCount($member_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = 10;
                break;
            case 'delivery';
                $filter['pay_status']      = 20;
                $filter['delivery_status'] = 10;
                break;
            case 'received';
                $filter['pay_status']      = 20;
                $filter['delivery_status'] = 20;
                $filter['receipt_status']  = 10;
                break;
        }
        return $this->where('member_id', '=', $member_id)
            ->where('order_status', '<>', 20)
            ->where($filter)
            ->count();
    }

    /**
     * 订单详情 orderType 1/2
     * @param      $order_id
     * @param null $member_id
     * @return null|static
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public static function getUserOrderDetail($order_id, $member_id)
    {
        if (!$order = self::get([
            'order_id'  => $order_id,
            'member_id' => $member_id,
        ])) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        return self::getGoodsOrderDetails($order_id, $member_id);
    }


    /**
     * @Notes     : 获取主表订单
     * @Interface getTheLordOrderInfo
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/1/5   10:06 下午
     */
    public static function getTheLordOrderInfo($order_id, $member_id)
    {

        if (!$order = self::get([
            'order_id'     => $order_id,
            'member_id'    => $member_id,
            'order_status' => ['<>', 20],
        ])) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        return self::getGoodsOrderDetails($order_id, $member_id);
    }

    /**
     * @Notes     : 获取商城订单
     * @Interface getGoodsOrderDetails
     * @param $order_id
     * @param $member_id
     * @return Order|null
     * @throws BaseException
     * @throws \think\exception\DbException
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/1/5   10:29 下午
     */
    public static function getGoodsOrderDetails($order_id, $member_id)
    {
        $value = Setting::getItem('order');
        if (!$order = self::get([
            'order_id'  => $order_id,
            'member_id' => $member_id,
        ], ['goods'])) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        $new_time = time();
        if ($order['pay_status']['value'] == 1) {
            $order['pay_time'] = '';
        } else {
            $order['pay_time'] = date("Y-m-d H:i:s", $order['pay_time']);
        }
        $order['create_str_time'] = strtotime($order['create_time']);
        $order['order_num']       = $order['goods'][0]['product_num'] ?? 1;
        $order['wait_pay_time']   = $order['create_str_time'] + 60 * $value['pay_time'];
        $order['new_time']        = $new_time;
        return $order;
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
