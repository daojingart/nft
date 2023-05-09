<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/17   9:27
 * +----------------------------------------------------------------------
 * | className: 商品模型
 * +----------------------------------------------------------------------
 */

namespace app\api\model\collection;


use app\common\model\Goods as GoodsModel;
use app\common\model\MemberGoodsCollection;
use think\Request;


class Goods extends GoodsModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'sales_initial',
        'sales_actual',
        'is_del',
        'app_id',
        'create_time',
        'update_time',
    ];

    /**
     * 商品详情：HTML实体转换回普通字符
     * @param $value
     * @return string
     */
    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * @Notes     :获取海报详情界面信息
     * @Interface getPosterDetails
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/4/16   9:42 上午
     */
    public function getPosterDetails($goods_id, $member_id)
    {
        $details               = parent::detail($goods_id);
        $details['poster_url'] = HOST . "/h5/h5.html#/pages/classify/goodsdet?id={$details['goods_id']}&member_id={$member_id}";
        return $details;
    }

    /**
     * 获取所有藏品
     * @param $param
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time      : 2022/6/20   15:46
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface getlist
     */
    public function getlist($param,$member_id)
    {
        $where = [
            'goods_status'    => 10,
            'recovery_num'    => ['>', 0],
            'recovery_status' => 1,
        ];
        if (isset($param['goods_name']) && !empty($param['goods_name'])) {
            $where['goods_name'] = ['like', '%' . $param['goods_name'] . '%'];
        }
        if(isset($param['category_id']) && $param['category_id']>0){
            $goods_ids = (new Goods())->where(['category_id'=>$param['category_id']])->column("goods_id");
            $where['goods_id'] = ['in',$goods_ids];
        }
        if(isset($param['is_concern']) && $param['is_concern']==1){
            $goods_ids = (new MemberGoodsCollection())->where(['member_id'=>$member_id])->column("goods_id");
            $where['goods_id'] = ['in',$goods_ids];
        }
        $query = self::where($where);
        if (isset($param['sort_type']) && !empty($param['sort_type'])) {
            switch ($param['sort_type']) {
                case 1:
                    $query->order('create_time desc'); //最新
                    break;
                case 2:
                    $query->where('is_recommend',10); //获取最热
                    break;
                case 3:
                    $query->order('recovery_price asc'); //价格正序
                    break;
                case 4:
                    $query->order('recovery_price desc'); //价格倒叙
                    break;
            }
        }
        $query->where('is_del', 0)
            ->field('goods_id,goods_name,recovery_price as goods_price,goods_thumb,recovery_num as stock_num,create_time,member_id');
        $list = $query->paginate($param['listRows'], false, [
            'query' => Request::instance()->request(),
        ]);
        return $list;
    }


    /**
     * 下单扣库存
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface updateGoodsStock
     * @Time      : 2022/6/23   16:55
     * 记得更新redis 缓存数据
     */
    public function updateGoodsStock($goods_id, $type)
    {
        if (in_array($type, ['1', '3', '4', '2'])) {
            $initRedis = initRedis();
            $this->where(['goods_id' => $goods_id])->setInc("sales_actual", '1');
            $this->where(['goods_id' => $goods_id])->setDec("stock_num", '1');
            //更新redis 缓存
            $initRedis->Hincrby("collection:goods_id:$goods_id", "stock_num", '-1');
        }
        return true;
    }


    /**
     * 回归库存
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface updateGoodsStock
     * @Time      : 2022/6/23   16:55
     * 记得更新redis 缓存数据
     */
    public function updateIncreaseGoodsStock($goods_id)
    {
        //为了防止 回归不该回归的库存 要判断缓存是否存在这个
        $initRedis = initRedis();
        if ($initRedis->EXISTS("collection:goods_id:$goods_id")) {
            $this->where(['goods_id' => $goods_id])->setDec("sales_actual", '1');
            $this->where(['goods_id' => $goods_id])->setInc("stock_num", '1');
            //更新redis 缓存
            $initRedis->Hincrby("collection:goods_id:$goods_id", "stock_num", '1');
        }
        return true;

    }

}
