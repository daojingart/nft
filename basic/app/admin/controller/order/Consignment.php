<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: liubinghong
 * +----------------------------------------------------------------------
 * | Time:  2022/6/11   11:06
 * +----------------------------------------------------------------------
 * | Email: 18368808324@163.com
 * +----------------------------------------------------------------------
 * | Created by PhpStorm.
 *
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\order;


use app\admin\controller\Controller;
use app\admin\model\order\Order as OrderModel;
use app\common\constants\Constant;
use Exception;

class Consignment extends Controller
{

    /**
     * 寄售订单
     *
     * @return array|mixed
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/15 11:09
     * @author Mr.Liu
     */
    public function index()
    {
        if (false == $this->request->isAjax()) {
            return $this->fetch('index');
        }
        try {
            $params = $this->get();
            $params['order_type'] = ['in',['3','11']];
            $result = (new OrderModel())->getList($params);
            return json($result);
            //return $this->renderSuccess('获取成功', '', $result);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * 已支付
     *
     * @return array|mixed|\think\response\Json
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/15 15:14
     * @author Mr.Liu
     */
    public function payList()
    {
        if (false == $this->request->isAjax()) {
            return $this->fetch('index');
        }
        try {
            $params = $this->get();
            $params['order_type'] = 3;
            $params['pay_status'] = 2;
            $result = (new OrderModel())->getList($params);
            return json($result);
            //return $this->renderSuccess('获取成功', '', $result);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * 已取消
     *
     * @return array|mixed|\think\response\Json
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/15 15:14
     * @author Mr.Liu
     */
    public function cancelList()
    {
        if (false == $this->request->isAjax()) {
            return $this->fetch('index');
        }
        try {
            $params = $this->get();
            $params['order_type'] = 3;
            $params['order_status'] = 5;
            $result = (new OrderModel())->getList($params);
            return json($result);
            //return $this->renderSuccess('获取成功', '', $result);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * 已退款
     *
     * @return array|mixed|\think\response\Json
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/15 15:14
     * @author Mr.Liu
     */
    public function refundList()
    {
        if (false == $this->request->isAjax()) {
            return $this->fetch('index');
        }
        try {
            $params = $this->get();
            $params['order_type'] = 3;
            $params['refund_status'] = 3;
            $result = (new OrderModel())->getList($params);
            return json($result);
            //return $this->renderSuccess('获取成功', '', $result);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * 订单详情
     *
     * @return mixed
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/15 16:39
     * @author Mr.Liu
     */
    public function detail()
    {
        try {
            $orderId = $this->request->param("order_id");
            if (!isset($orderId) || empty($orderId)) {
                throw new Exception('缺少比传参数-订单ID');
            }
            $detail = (new OrderModel())->getDetail($orderId);
            return $this->fetch('detail',compact('detail'));
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

}