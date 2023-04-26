<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 15:41
 */


namespace app\common\model;

/**
 * 产品订单表
 * Class ProductOrder
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 15:41
 */
class ProductOrder extends BaseModel
{
    protected $name = 'product_order';

    public function address()
    {
        return $this->hasOne('app\common\model\ProductOrderAddress','order_id','order_id');
    }
    public function goods()
    {
        return $this->hasOne('app\common\model\ProductOrderGoods','order_id','order_id');
    }

    /**
     * 商城订单列表
     * @return \think\model\relation\HasMany
     */
    public function OrderGoods()
    {
        return $this->hasMany('app\api\model\order\OrderProductGoods',"order_id",'order_id');
    }

    public function member()
    {
        return $this->hasOne('member','member_id','member_id');
    }
    /**
     * 获取订单列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-28 09:21
     */
    public function getList($type = 0)
    {
        $param = request()->param();
        $where = [];
        switch ($type){
            case 1:
                //待发货为未发货
                $where['a.delivery_status'] = 1;
                break;
            case 2:
                //已经发货  未收货
                $where['a.delivery_status'] = 2;
                $where['a.receipt_status'] = 1;
                break;
            case 3:
                //直接判断订单状态为完成
                $where['a.order_status'] = 4;
                break;
        }
        if (array_key_exists('goods_name',$param)&&!empty($param['goods_name'])){
             $where['b.goods_name'] = $param['goods_name'];
        }
        if (array_key_exists('order_no',$param)&&!empty($param['order_no'])){
            $where['a.order_no'] = $param['order_no'];
        }
        if (array_key_exists('member_id',$param)&&!empty($param['member_id'])){
            $where['c.member_id'] = $param['member_id'];
        }
        $page = request()->param('page')?:1;//当前第几页
        $list = request()->param('limit')?:20;//每页显示几条
        $data = $this->alias('a')
            ->where($where)
            ->join('snake_product_order_goods b','a.order_id = b.order_id')
            ->join('snake_member c','a.member_id = c.member_id')
            ->order('a.create_time desc')
            ->paginate($list,false,$config = ['page'=>$page])->toArray();
        $arr['data'] = $data['data'];
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = $data['total'];
        //组装数据
        foreach ($arr['data'] as &$item){
            $text = '';
            if ($item['delivery_status']==1){
                $text = '待发货';
            }else{
                $text = '待收货';
            }
            if ($item['order_status']==4){
                $text = '已完成';
            }
            $item['product_status_text'] = $text;
            $item['operate'] = showNewOperate(self::makeButton($item['order_id']));
            $item['pay_time'] = date("Y-m-d H:i:s",$item['pay_time']);
        }
        return  $arr;
    }

    private static  function makeButton($order_id)
    {
        $returnArray = [
            '查看详情' => [
                'href' => url('order.product/detail', ['order_id' => $order_id]),
                'lay-event' => '',
            ]
        ];
        return $returnArray;
    }
}