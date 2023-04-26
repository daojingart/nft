<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 17:40
 */


namespace app\api\model\order;


use app\common\model\ProductOrder;
use app\common\model\SpecValue;
use exception\BaseException;

class Product extends ProductOrder
{
    /**
     * 获取我的订单列表
     * @param $member
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-27 17:21
     */
    public function toObtainListOrder($member)
    {
        $where = [];
        $page = request()->param('page')?:1;
        $limit = request()->param('limit')?:20;
        $param = request()->param();
        $member_id = $member['member_id'];
        //0全部1待发货2待收货3已完成
        switch ($param['type']){
            case 2:
                //待付款
                $where['order_status'] = 0;
                break;
            case 3:
                //待发货为未发货
                $where['delivery_status'] = 1;
                break;
            case 4:
                //已经发货  未收货
                $where['delivery_status'] = 2;
                $where['receipt_status'] = 1;
                break;
            case 5:
                //直接判断订单状态为完成
                $where['order_status'] = 4;
                break;
        }
        $data = $this
            ->with(['goods'])
            ->order('order_id desc')
            ->where('member_id',$member_id)
            ->where($where)
            ->field("order_id,order_no,total_price,order_type,order_status,delivery_status,receipt_status")
            ->paginate($limit,false,$config = ['page'=>$page])
            ->toArray();
        $spceValueModel = new SpecValue();
        //需要sku数据 待修改成关联查询
        foreach ($data['data'] as &$item){
            $text = '';
            $text_values = '';
            if ($item['delivery_status']==1){
                $text = '待发货';
                $text_values = 1;
            }
            if ($item['delivery_status']==2){
                $text = '已发货';
                $text_values = 1;
            }
            if ($item['receipt_status']==1 && $item['delivery_status']==2){
                $text = '待收货';
                $text_values = 1;
            }
            if ($item['receipt_status']==2 && $item['delivery_status']==2){
                $text = '已收货';
                $text_values = 1;
            }
            if ($item['order_status']==4){
                $text = '已完成';
                $text_values = 3;
            }
            if ($item['order_status']==0){
                $text = '待付款';
                $text_values = 4;
            }
            $sku_text = '';
            //处理sku信息展示
            $item['status_text'] = $text;
            $item['status_values'] = $text_values;
            $sku_ids = array_filter(explode('_',$item['goods']['sku_id']));
            $sku_data = $spceValueModel->with('spec')->whereIn('spec_value_id',$sku_ids)->select()->toArray();
            foreach ($sku_data as $sku_value){
                $sku_text .= $sku_value['spec']['spec_name'].':'.$sku_value['spec_value'];
            }
            $item['sku_text'] = $sku_text;
        }
        return $data['data'];
    }

    /**
     * 提醒发货
     * @param $member
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-27 18:09
     */
    public function remindDelivery($member)
    {
        $member_id = $member['member_id'];
        $order_id = request()->param()['order_id'];
        $order =  $this->where(['order_id'=>$order_id,'member_id'=>$member_id])->find();
        if (!$order){
            throw new BaseException(['msg'=>'订单不存在']);
        }
        if ($order->remind==1){
            throw new BaseException(['msg'=>'已经提醒过啦']);
        }
        $order->remind = 1;
        $order->save();
    }

    /**
     * 确认收货
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-27 18:53
     */
    public function confirmGoods($member)
    {
        $order_id = request()->param('order_id');
        if(!$order_id){
            throw new BaseException(['msg'=>'订单不存在']);
        }
        $order = $this->where('order_id',$order_id)->where('member_id',$member['member_id'])->find();
        if (!$order){
            throw new BaseException(['msg'=>'订单不存在']);
        }

        if ($order->delivery_status==1){
            throw new BaseException(['msg'=>'当前订单暂未发货']);
        }
        $order->receipt_status = 2;
        $order->order_status = 4;
        $order->save();
    }

    /**
     * 获取订单详情
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-28 08:54
     */
    public function orderDetails($member)
    {
        $order_id = request()->param('order_id');
        $order = $this->with(['goods','address'])->where('order_id',$order_id)->where('member_id',$member['member_id'])->find();
        if (!$order){
            throw new BaseException(['msg'=>'订单不存在']);
        }
        //获取sku信息
        $sku_text = '';
        $spceValueModel = new SpecValue();
        $sku_ids = array_filter(explode('_',$order->goods->sku_id));
        $sku_data = $spceValueModel->with('spec')->whereIn('spec_value_id',$sku_ids)->select()->toArray();
        foreach ($sku_data as $sku_value) {
            $sku_text .= $sku_value['spec']['spec_name'] . ':' . $sku_value['spec_value'];
        }
        $order['sku_text'] = $sku_text;
        //订单状态
        $text = '';
        $text_values = '';
        if ($order['delivery_status']==1){
            $text = '待发货';
            $text_values = 1;
        }else{
            $text = '待收货';
            $text_values = 2;
        }
        if ($order['order_status']==4){
            $text = '已完成';
            $text_values = 3;
        }
        if ($order['order_status']==0){
            $text = '待付款';
            $text_values = 4;
        }
        $order['order_status_text'] = $text;
        $order['status_values'] = $text_values;
        return $order;

    }

}