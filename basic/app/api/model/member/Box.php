<?php

namespace app\api\model\member;

use app\api\model\order\Order;
use app\common\model\Goods;
use app\common\model\MemberBox;
use think\Request;

class Box extends MemberBox
{
    /**
     *  验证盲盒有效
     * @param $box_id
     * @param $member_id
     * @Time: 2022/9/2   20:10
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface verifyGoods
     */
    public function verifyGoods($box_id,$member_id)
    {
        $box_details = $this->details(['id'=>$box_id,'member_id'=>$member_id],"id,order_id,order_sn,goods_name,goods_thumb,goods_id,is_open,box_status,create_time");
        //判断是否可以寄售
        if(empty($box_details)){
            $this->error = "未找到先关盲盒信息";
            return false;
        }
        if($box_details['is_open']==20){
            $this->error = "盲盒错误,无法挂售";
            return false;
        }
        if($box_details['box_status']!=10){
            $this->error = "非法调用";
            return false;
        }
        if($box_details['box_status']!=10){
            $this->error = "非法调用";
            return false;
        }
        $goods_info = (new Goods())->where(['goods_id'=>$box_details['goods_id']])->field("goods_price,is_open_consignment,consignment_minute,limit_consignment_open,top_price_limit,minimum_consignment")->find();
        if ($goods_info['is_open_consignment'] == 20) { //单独设置转增时间
            $sale_time = $goods_info['consignment_minute'];
        }
        if ($goods_info['is_open_consignment'] == 30) { //单独设置转增时间
            $this->error = "不符合挂售规则;无法进行挂售";
            return false;
        }
        //判断寄售时间
        if (strtotime($box_details['create_time']) + $sale_time * 60 > time()) {
            $this->error = "冻结时间未到无法寄售";
            return false;
        }
        return true;
    }

    /**
     * 获取已经挂售的订单列表
     * @param $param
     * @param $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/9/14   17:12
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSaleGoodsList
     */
    public function getSaleGoodsList($param,$where)
    {
        $query = $this->where($where);
        $query->field('id,goods_id,goods_name,goods_thumb,sale_price as goods_price,box_status');
        $list = $query->paginate($param['listRows'],false,[
            'query' => Request::instance()->request()
        ])->each(function ($item) use ($where){
            if($where['goods_status']==3){
                $order_info = (new Order())->where(['sale_goods_id'=>$item['id'],'pay_status'=>2])->field("order_no")->find();
                $member_info = Member::detail($order_info['sale_member_id']);
                $item['pay_name'] = $member_info['name'];
            }else{
                $item['pay_name'] = '--';
            }
        });
        return $list;
    }

}