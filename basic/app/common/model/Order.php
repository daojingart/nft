<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/13   18:39
 * +----------------------------------------------------------------------
 * | className:  订单管理模型
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


use think\Hook;

class Order extends BaseModel
{
    protected $name = 'order';


    /**
     * 订单模型初始化
     */
    public static function init()
    {
        parent::init();
        // 监听订单处理事件
        $static = new static;
        Hook::listen('order', $static);
    }

    /**
     * 商城订单列表
     * @return \think\model\relation\HasMany
     */
    public function goods()
    {
        return $this->hasMany('app\api\model\order\OrderGoods',"order_id",'order_id');
    }

//    /**
//     * 关联订单收货地址表
//     * @return \think\model\relation\HasOne
//     */
//    public function address()
//    {
//        return $this->hasOne('OrderAddress');
//    }

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('app\common\model\Member');
    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getPayStatusAttr($value)
    {
        $status = [1 => '待付款', 2 => '已付款'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 订单状态
     * @param $value
     * @return array
     */
    public function getOrderStatusAttr($value)
    {
        $status = [1 => '待付款', 2 => '已支付',3 => '进行中',4 => '已完成',5 => '已取消'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 发货状态
     * @param $value
     * @return array
     */
    public function getDeliveryStatusAttr($value)
    {
        $status = [1 => '待发货', 2 => '已发货'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 收货状态
     * @param $value
     * @return array
     */
    public function getReceiptStatusAttr($value)
    {
        $status = [1 => '待收货', 2 => '已收货'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 退款状态
     * @param $value
     * @return array
     */
    public function getRefundStatusAttr($value)
    {
        $status = [1 => '正常交易', 2 => '退款中',3 => '退款成功',4 => '退款失败'];
        return ['text' => $status[$value], 'value' => $value];
    }


    /**
     * 支付类型
     * @param $value
     * @return array
     */
    public function getPayTypeAttr($value)
    {
        $status = [1 =>'微信支付' , 2=>'微信支付' ,3=>'APP微信',4=>'支付宝H5',5=>'余额支付',6=>'荣誉值兑换',7=>'微信H5',8=>'支付宝APP',9=>'连连银联支付',10=>'杉德银联支付',11=>'adaPay',12=>'汇元支付',13=>'连连快捷',14=>'汇元快捷',15=>'汇付钱包',16=>'杉德钱包'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 支付终端
     * @param $value
     * @return array
     */
    public function getClientTypeAttr($value)
    {
        $status = [1 => '公众号', 2 => '小程序' , 3=>'APP'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }

    /**
     * 订单详情
     * @param $order_id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($order_id)
    {
        return self::get($order_id, ['goods','user']);
    }

    /**
     * @Notes: 获取订单数量
     * @Interface getOrderCount
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/14   2:56 下午
     */
    public static function getOrderCount($where)
    {
        return self::where($where)->count();
    }

}