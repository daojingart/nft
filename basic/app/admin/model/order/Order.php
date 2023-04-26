<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: liubinghong
 * +----------------------------------------------------------------------
 * | Time:  2022/6/11   15:25
 * +----------------------------------------------------------------------
 * | Email: 18368808324@163.com
 * +----------------------------------------------------------------------
 * | Created by PhpStorm.
 *
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\order;

use app\common\model\Member;
use app\common\model\MemberBox;
use app\common\model\MemberGoods;
use app\common\model\Order as OrderModel;
use think\Db;


class Order extends OrderModel
{

    /**
     * 订单列表
     *
     * @param array $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/11 16:55
     * @author Mr.Liu
     */
    public function getList(array $param)
    {
        $limit = isset($param['limit'])?$param['limit']:'0';
        $param['page'] = isset($param['page'])?$param['page']:'1';
        $offset = ($param['page'] - 1) * $limit;

        $where = [];
        $where['o.is_delete'] = 0;
        if (isset($param['order_type']) && !empty($param['order_type'])) {
            $where['o.order_type'] = $param['order_type'];
        }
        // 检索查询条件
        if (!empty($param['order_no'])) { //根据订单号筛选
            $where['o.order_no'] = $param['order_no'];
        }
        if (!empty($param['goods_name'])) { //根据订单号筛选
            $where['og.goods_name'] = $param['goods_name'];
        }
        if (!empty($param['member_id'])) { //根据订单号筛选
            $where['o.member_id'] = $param['member_id'];
        }
        if (!empty($param['sale_member_id'])) { //根据订单号筛选
            $where['o.sale_member_id'] = $param['sale_member_id'];
        }
        if (!empty($param['pay_status'])) { //根据支付状态筛选
            $where['o.pay_status'] = $param['pay_status'];
        }
        if (!empty($param['order_status'])) { //根据订单状态筛选
            $where['o.order_status'] = $param['order_status'];
        }
        if (!empty($param['refund_status'])) { //根据退款状态筛选
            $where['o.refund_status'] = $param['refund_status'];
        }
        if (!empty($param['create_time'])) { //根据下单时间筛选
            $create_time = explode('~',$param['create_time']);
            $where['o.create_time'] = ['between time',[trim($create_time[0]),trim($create_time[1])]];
        }

        $list = $this->alias('o')
            ->join('snake_order_goods og','o.order_id = og.order_id','left')
            ->where($where)
            ->order('o.order_id desc')
            ->limit($offset,$limit)
            ->select()->toArray();
        if (!empty($list)) {
            $memberId = array_column($list,'member_id');
            $memberList = (new Member())->where(['member_id' => ['in',$memberId]])->select()->toArray();
            $memberArray = array_column($memberList,null,'member_id');
            foreach ($list as &$val) {
                $saleMemberInfo = (new Member())->where(['member_id' => $val['sale_member_id']])->find();
                $val['operate'] = showNewOperate(self::makeButton($val['order_type'],$val['order_id']));
                $val['member_info'] = $memberArray[$val['member_id']]['name'].'('.$memberArray[$val['member_id']]['phone'].')';
                $val['order_status_text'] = $val['order_status']['text'] ?? '';
                $val['pay_type_text'] = $val['pay_type']['text'] ?? '';
                $val['sale_phone'] = $saleMemberInfo['phone'];
                $val['pay_time'] = !empty($val['pay_time']) ? date("Y-m-d H:i:s",$val['pay_time']) : '';
            }
        }
        $return['count'] = $this->alias('o')
            ->join('snake_order_goods og','o.order_id = og.order_id','left')
            ->where($where)
            ->count();
        $return['data'] = $list;
        $return['code'] = '0';
        $return['msg'] = 'ok';
        return $return;
    }


    /**
     * 会员盲盒列表
     * @param $order_type
     * @param $order_id
     * @return array[]
     * @author Mr.Zhang
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/15 11:28
     */
    private static  function makeBoxButton($is_open,$order_id)
    {
        $returnArray = [
            '删除盲盒' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
        return $returnArray;
    }

    /**
     * 删除盲盒
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface removeBox
     * @Time: 2022/6/30   11:45
     */
    public function removeBox()
    {
        //判断盲盒是否打开  打开了同时删除这个藏品
        if($this['is_open']==20){
            (new MemberGoods())->where(['order_id'=>$this['order_id']])->delete();
        }
        //删除这个盲盒
        (new MemberBox())->where(['order_id'=>$this['order_id']])->delete();
       return $this->where(['order_id'=>$this['order_id']])->delete();
    }

    /**
     * 订单操作
     * @param $order_type
     * @param $order_id
     * @return array[]
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/15 11:28
     */
    private static  function makeButton($order_type,$order_id)
    {
        $returnArray = [];
        switch ($order_type) {
            case 1:
                $returnArray = [
                    '查看详情' => [
                        'href' => url('order.order/detail', ['order_id' => $order_id]),
                        'lay-event' => '',
                    ]
                ];
                break;
            case 2:
                $returnArray = [
                    '查看详情' => [
                        'href' => url('order.box/detail', ['order_id' => $order_id]),
                        'lay-event' => '',
                    ]
                ];
                break;
            case 3:
                $returnArray = [
                    '查看详情' => [
                        'href' => url('order.consignment/detail', ['order_id' => $order_id]),
                        'lay-event' => '',
                    ]
                ];
                break;
            case 4:
                $returnArray = [
                    '查看详情' => [
                        'href' => url('order.backbuy/detail', ['order_id' => $order_id]),
                        'lay-event' => '',
                    ]
                ];
                break;
        }
        return $returnArray;

    }


    /**
     * 获取订单详情
     *
     * @param $order_id
     * @return Order|null
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/15 16:46
     * @author Mr.Liu
     */
    public function getDetail($order_id)
    {
        return self::detail($order_id);
    }

    /**
     * 获取盲盒列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getBoxList
     * @Time: 2022/6/30   10:25
     */
    public function getBoxList($filter,$param)
    {
        $limit = isset($param['limit'])?$param['limit']:'0';
        $param['page'] = isset($param['page'])?$param['page']:'1';
        $offset = ($param['page'] - 1) * $limit;
        $filter['pay_status'] = 2;
        if(isset($param['member_id']) && !empty($param['member_id'])){
            $filter['o.member_id'] = $param['member_id'];
        }
        if(isset($param['goods_name']) && !empty($param['goods_name'])){
            $filter['og.goods_name'] = $param['goods_name'];
        }
        if(isset($param['phone']) && !empty($param['phone'])){
            $filter['m.phone'] = $param['phone'];
        }
        if (isset($param['create_time']) && !empty($param['create_time'])) { //根据下单时间筛选
            $create_time = explode('~',$param['create_time']);
            $filter['o.create_time'] = ['between time',[trim($create_time[0]),trim($create_time[1])]];
        }
        $list = $this->alias('o')
            ->join('snake_order_goods og','o.order_id = og.order_id','left')
            ->join('member m','o.member_id = m.member_id','left')
            ->where($filter)
            ->order('o.order_id desc')
            ->field("o.order_id,o.member_id,og.goods_name,o.order_type,o.is_open,o.create_time,m.name,m.phone")
            ->limit($offset,$limit)
            ->select();
        foreach ($list as &$val) {
            $memberGoodsInfo = (new MemberGoods())->where(['order_id'=>$val['order_id']])->find();
            $val['operate'] = showNewOperate(self::makeBoxButton($val['is_open'],$val['order_id']));
            $val['is_open'] = $val['is_open']==10?'<span class="layui-badge layui-bg-orange">未开盒</span>':'<span class="layui-badge layui-bg-blue">已开盒</span>';
            $val['order_type'] = $val['order_type']==2?"用户购买":"系统空投";
            $val['open_box_time'] = '--';
            $val['open_box_goods_name'] = '--';
            if(!empty($memberGoodsInfo)){
                $val['open_box_time'] = $memberGoodsInfo['create_time'];
                $val['open_box_goods_name'] = $memberGoodsInfo['goods_name'];
            }
        }
        $return['count'] = $this->alias('o')
            ->join('snake_order_goods og','o.order_id = og.order_id','left')
            ->join('member m','o.member_id = m.member_id','left')
            ->where($filter)
            ->count();
        $return['data'] = $list;
        $return['code'] = '0';
        $return['msg'] = 'ok';
        return $return;
    }






}