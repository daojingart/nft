<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/2/13   15:01
 * +----------------------------------------------------------------------
 * | className: 易宝订单处理
 * +----------------------------------------------------------------------
 */

namespace app\notice\service;

use app\common\model\DivideOrder;
use think\Controller;

class YeepayOrder extends Controller
{
    /**
     * 写入易宝分润订单
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface profitsharingOrder
     * @Time: 2023/2/13   15:02
     */
    public function profitsharingOrder($Order_info,$arr)
    {
        if (($Order_info['order_type'] == 3 || $Order_info['order_type'] == 11) && $Order_info['pay_type']['value']=='17' ) {
            //避免一次订单插入多次 做唯一值处理
            (new DivideOrder())->insertData([
                'order_id' => $Order_info['order_id'],
                'order_sn' => $Order_info['order_no'],
                'yeep_order_sn' => $arr['transaction_id']
            ]);
        }
    }

}