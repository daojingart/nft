<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-21 10:37
 */


namespace app\api\model\collection;
use app\common\model\GoodsSynthetic as GoodsSyntheticModel;

/**
 * 合成列表模型
 * Class GoodsSynthetic
 * @package app\api\model\collection
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-21 10:38
 */
class GoodsSynthetic extends GoodsSyntheticModel
{

    /**
     * 获取合成列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-21 10:40
     */
    public static function getList($type)
    {
        $page = request()->param('page')?:1;//当前第几页
        $list = request()->param('limit')?:10;//每页显示几条
        $where = [];
        switch ($type)
        {
            case "1": //查询全部
                break;
            case "2": //进行中
                $where['start_time'] = ['<=',time()];
                $where['end_time'] = ['>=',time()];
                break;
            case "3": //查询未开始
                $where['start_time'] = ['>',time()];
                break;
            case "4": //查询结束
                $where['end_time'] = ['<',time()];
                break;
        }
        $data = self::order('id desc')->where($where)->field("id,count,exchange,name,goods_id,start_time,end_time")->paginate($list,false,$config = ['page'=>$page])->each(function ($item) {
            $goods_id = explode(',', $item['goods_id']);
            $item['goods_thumb'] = Goods::where('goods_id', $goods_id[0])->value('goods_thumb');
            $item['strtotime_start_time'] = strtotime($item['start_time']);
            $item['strtotime_end_time'] = strtotime($item['end_time']);
            $item['now_time'] = time();
            return $item;
        })->toArray();
        return $data['data'];
    }


}