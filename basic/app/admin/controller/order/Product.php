<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-28 09:20
 */


namespace app\admin\controller\order;


use app\admin\controller\Controller;
use app\common\model\Express;
use app\common\model\ProductOrder as ProductOrderModel;
/**
 * 产品兑换订单
 * Class Product
 * @package app\admin\controller\order
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-28 09:20
 */
class Product extends Controller
{
    /**
     * 产品订单列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-28 09:20
     */
    public function index()
    {
        if (request()->isAjax()) {
            $param = request()->param();
            $type = 0;
            if (array_key_exists('type',$param)){
                $type = $param['type'];
            }
            $model = (new ProductOrderModel);
            $list = $model->getList($type);
            return json($list);
        }
        return $this->fetch();
    }

    /**
     * 产品订单详情
     * @param $order_id
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-28 10:15
     */
    public function detail($order_id)
    {
        $model = new ProductOrderModel();
        if (request()->isAjax()){
            $param = request()->param();
            //更新订单状态
            $express['express_company'] = $param['order']['express_company'];
            $express['express_no'] = $param['order']['express_no'];
            $express['delivery_status'] = 2;
            $express['delivery_time'] = time();
            if ($model::update($express,['order_id'=>$param['order_id']])){
                return  $this->renderSuccess('发货成功');
            }
            return  $this->renderError('发货失败');
        }
        $data = $model->with(['goods','member','address'])->where('order_id',$order_id)->find();
        if (!$data){
            return $this->redirect(url('index'));
        }
        //物流
        $express_list = (new Express())->select();
//        pre($data->toArray());
        $this->assign([
            'detail' => $data,
            'express_list' => $express_list
        ]);
        return  $this->fetch();
    }
}