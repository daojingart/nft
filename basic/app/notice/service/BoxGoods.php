<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/2/13   16:10
 * +----------------------------------------------------------------------
 * | className: 盲盒发送
 * +----------------------------------------------------------------------
 */

namespace app\notice\service;

use app\common\model\MemberBox;

class BoxGoods
{
    /**
     * 加入盲盒仓库
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface joinBoxGood
     * @Time: 2023/2/13   16:10
     */
    public function joinBoxGood($Order_info,$orderGoodsInfo)
    {
        $MemberBoxInfo = (new MemberBox())->where(['order_id'=>$Order_info['order_id']])->find();
        if(empty($MemberBoxInfo)){
            (new MemberBox())->insertData([
                'member_id' => $Order_info['member_id'],
                'order_id' => $Order_info['order_id'],
                'order_sn' => $Order_info['order_no'],
                'is_open' => 10,
                'box_status' => 10,
                'goods_id' => $orderGoodsInfo['goods_id'],
                'goods_name' => $orderGoodsInfo['goods_name'],
                'goods_thumb' => $orderGoodsInfo['goods_image'],
            ]);
        }
    }

    /**
     * 修改盲盒的状态
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface updateBoxStatus
     * @Time: 2023/2/13   16:28
     */
    public function updateBoxStatus($sale_goods_id)
    {
        $model = (new MemberBox())->details(['id'=>$sale_goods_id]);
        $model->save(['box_status' => 40]);
    }

}