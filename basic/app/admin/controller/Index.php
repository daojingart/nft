<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 控制面板控制器
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller;



use app\admin\model\collection\Goods;
use app\admin\model\collection\MemberGoods;
use app\common\model\Member;
use app\common\model\Order as OrderModel;
use app\common\model\Withdraw;


class Index extends Controller
{
    /**
     * @Notes: 控制面板渲染
     * @Interface index
     * @return mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/19   1:55 下午
     */
    public function index()
    {
        $this->assign([
            'data' => $this->getDataList(),
            'upcoming' => $this->upcomingData(),
            'order_list' => $this->getForwardingOrder(),
            'member_value_list' => json_encode($this->getYearMemberSum()),
            'order_value_list' => json_encode($this->getYearOrderSum()),
//            'order_goods_value_list' => json_encode($this->getYearDateOrderGoods())
        ]);
        return $this->fetch();
    }

    /**
     * @Notes: 获取今日营业额，累计营业额，今日成交订单，累计订单，今日注册会员，累计注册
     * @Interface getDataList
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/19   1:55 下午
     */
    public function getDataList()
    {
        $data = [];
        //获取今日营业额 订单查询
        $data['today_price'] = OrderModel::whereTime('pay_time', 'today')->where(['pay_status'=>'2','order_type'=>['in',['1','2','3']]])->sum("pay_price");
        $data['sum_price'] = OrderModel::where(['pay_status'=>'2','order_type'=>['in',['1','2','3']]])->sum("pay_price");

        //获取今日成交订单
        $data['today_order_count'] = OrderModel::whereTime('pay_time', 'today')->where(['pay_status'=>'2','order_type'=>['in',['1','2','3']]])->count();
        $data['sum_order_count'] = OrderModel::where(['pay_status'=>'2','order_type'=>['in',['1','2','3']]])->count();

        //获取今日注册会员
        $data['today_member_count'] = (new Member())->whereTime('create_time', 'today')->count();
        $data['member_count'] = (new Member())->count();

        // 获取今日铸造藏品
        $data['today_goods'] = (new MemberGoods())->where(['cast_status' => 2])->whereTime('cast_time','today')->count();
        $data['sum_goods'] = (new MemberGoods())->where(['cast_status' => 2])->count();

        // 获取今日空投藏品
        $data['today_airdrop_goods'] = (new Goods())->where(['product_types' => 2])->whereTime('create_time','today')->count();
        $data['sum_airdrop_goods'] = (new Goods())->where(['product_types' => 2])->count();

        // 获取今日空投会员
        $data['today_airdrop_member'] = (new MemberGoods())->where(['source_type' => 4])->whereTime('create_time','today')->group('member_id')->count();
        $data['sum_airdrop_member'] = (new MemberGoods())->where(['source_type' => 4])->group('member_id')->count();

        return $data;
    }

    /**
     * @Notes:待办数据
     * @Interface upcomingData
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/22   10:26 下午
     */
    public function upcomingData()
    {
        $data = [];
        //待发货订单
        $data['order_forwarding'] = OrderModel::where(['pay_status'=>'2','delivery_status'=>'10'])->count();
        //待收货订单
        $data['order_receipt'] = OrderModel::where(['pay_status'=>'2','receipt_status'=>'10','delivery_status'=>'20'])->count();
        //待提现审核
        $data['apply_forwarding'] = (new Withdraw())->where(['status' => '0'])->count();
        return $data;
    }


    /**
     * @Notes:代发货订单
     * @Interface getForwardingOrder
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/23   9:24 下午
     */
    public function getForwardingOrder()
    {
        //获取代发货的订单列表
        return OrderModel::with("goods")->where(['pay_status'=>'2','delivery_status'=>'10'])->limit("0","5")->order("pay_time desc")->select();
    }


    /**
     * @Notes: 统计订单近7天的信息
     * @Interface getYearOrderSum
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/23   9:56 下午
     */
    public function getYearOrderSum()
    {
        for($i=0;$i<7;$i++){
            $data [$i] =  date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
        }
        sort($data);
        for($i=0;$i<7;$i++){
            $time_do [$i] =  date("m.d",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
        }
        sort($time_do);
        $time_do['6'] = "今天";

        $new_data = [];
        foreach($data as $k => $v){
            $values = OrderModel::where(['pay_status'=>2,'order_type'=>['in',['1','2','3']]])->where('pay_time','between',[strtotime($v),strtotime($v."+1 day")])->sum("pay_price");
            $new_data[$k]['type'] = $time_do[$k];;
            $new_data[$k]['value'] =$values;
        }

        return $new_data;
    }


    /**
     * @Notes: 统计会员注册近7天的信息
     * @Interface getYearOrderSum
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/23   9:56 下午
     */
    public function getYearMemberSum()
    {
        for($i=0;$i<7;$i++){
            $data [$i] =  date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
        }
        sort($data);
        for($i=0;$i<7;$i++){
            $time_do [$i] =  date("m.d",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
        }
        sort($time_do);
        $time_do['6'] = "今天";


        $new_data = [];
        foreach($data as $k=>$v){
            $values = (new Member())::where('create_time','between',[strtotime($v),strtotime($v."+1 day")])->count();
            $new_data[$k]['type'] = $time_do[$k];
            $new_data[$k]['value'] =$values;
        }
        return $new_data;
    }


    /**
     * @Notes: 获取最近几个月的订单情况
     * @Interface getYearDateMember
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/23   10:26 下午
     */
    public function getYearDateOrderGoods()
    {
        $today =  date("Y-m-d");
        $arr = array();
        $old_time = strtotime('-6 month',strtotime($today));
        for($i = 0;$i <7; ++$i){
            $t = strtotime("+$i month",$old_time);
            $arr[]=date('Y-m',$t);
        }
        $new_data = [];
        foreach($arr as $k=>$v){
            $values = OrderModel::where(['order_type'=> 1,'pay_status' => 20])->where('pay_time','between',[strtotime($v),strtotime($v."+1 month")])->sum("pay_price");
            $new_data[$k]['type'] = $v;
            $new_data[$k]['value'] =$values;
        }
        return $new_data;
    }

}