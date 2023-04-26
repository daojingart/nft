<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/30   10:17
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\user;

use app\admin\controller\Controller;
use app\admin\model\order\Order;

class Box extends Controller
{
    /**
     * 盲盒了列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2022/6/30   10:17
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 获取全部盲盒的类表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface get_all
     * @Time: 2022/6/30   10:22
     */
    public function get_all()
    {
        return $this->getList([
            'o.order_type' => ['in',['2','7','9','11','10']]
        ]);
    }

    /**
     * 未开盒
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/30   11:15
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface no_open
     */
    public function no_open()
    {
        return $this->getList([
            'o.is_open' => 10,
            'o.order_type' => ['in',['2','7','9','11','10']]
        ]);
    }

    /**
     * 已开盒
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface open_box
     * @Time: 2022/6/30   11:15
     */
    public function open_box()
    {
        return $this->getList([
            'o.is_open' => 20,
            'o.order_type' => ['in',['2','7','9','11','10']]
        ]);
    }

    /**
     * 已开盒
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface open_box
     * @Time: 2022/6/30   11:15
     */
    public function airdrop_box()
    {
        return $this->getList([
            'o.order_type' => 7
        ]);
    }


    /**
     * 获取已购的盲盒列表
     * @param array $filter
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   19:02
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    private function getList($filter = [])
    {
        $model = new Order();
        return  $model->getBoxList($filter,$this->request->param());
    }

    /**
     * 删除盲盒
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface remove
     * @Time: 2022/6/30   11:43
     */
    public function remove($order_id)
    {
        $model = Order::detail($order_id);
        if (!$model->removeBox()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

}