<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/17   11:19
 * +----------------------------------------------------------------------
 * | className:  发售日历
 * +----------------------------------------------------------------------
 */

namespace app\api\model\collection;

use app\common\model\Calendar as CalendarModel;
use think\Request;
use app\api\model\collection\Goods;


class Calendar extends CalendarModel
{

    /**
     * 获取发售日历
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     * @Time: 2022/6/17   11:21
     */
    public function getList($page)
    {
        $list = self::where(['disabled' => 0,'is_del' => 0])
            ->field('id,calendar_time,goods_id')
            ->order('calendar_time asc')
            ->page($page,10)
            ->select();
        $new_goods_list = [];
        $goodsModel = new Goods();
        foreach ($list as $key => $value){
            $goods = $goodsModel->with('writer')->where(['goods_id' => ['in',explode(',',$value['goods_id']),'goods_status'=>10],'start_time'=>['>=',time()]])->field("goods_id,goods_name,original_number,FROM_UNIXTIME(start_time,'%m月%d日 %H:%i开售') as start_time_date,goods_price,writer_id,goods_thumb")->select()->toArray();
            $new_goods_list[] = $goods;
        }
        return $new_goods_list;
    }

}