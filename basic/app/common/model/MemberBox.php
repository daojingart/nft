<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/9/2   19:33
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

use app\api\model\collection\Goods;
use think\Request;

class MemberBox extends BaseModel
{
    protected $name = 'member_box';

    /**
     * 商城订单列表
     * @return \think\model\relation\HasMany
     */
    public function goods()
    {
        return $this->hasMany('app\api\model\order\OrderGoods',"order_id",'order_id');
    }

    /**
     * 插入盲盒数据
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface insertData
     * @Time: 2022/9/2   19:40
     */
    public function insertData($data)
    {
        $data['app_id'] = '10001';
        return $this->allowField(true)->save($data);
    }

    /**
     * 获取盲盒寄售详情
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface details
     * @Time: 2022/9/2   19:53
     */
    public function details($field,$filed_data="*")
    {
        return $this->where($field)->field($filed_data)->find();
    }


    /**
     * 市场藏品列表
     * @param $param
     * @param $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/6/17   17:04
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGoodsList
     */
    public function getMarketGoodsList($param,$where,$member_id)
    {
        if (isset($param['goods_name']) && !empty($param['goods_name'])) {
            $where['goods_name'] = ['like','%'.$param['goods_name'].'%'];
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
                    $query->order('sale_price asc'); //价格正序
                    break;
                case 4:
                    $query->order('sale_price desc'); //价格倒叙
                    break;
            }
        }
        $query->field('id,goods_id,goods_name,goods_thumb,sale_price as goods_price,create_time');
        $list = $query->group("goods_id")->paginate($param['listRows'],false,[
            'query' => Request::instance()->request()
        ])->each(function ($item) use ($member_id) {
            //查询这个藏品的发售  以及藏品在售数量
            $count = $this->where(['goods_id'=>$item['goods_id'],'box_status'=>['in',['20','30']]])->count();
            $sale_price = $this->where(['goods_id'=>$item['goods_id'],'box_status'=>['in',['20','30']]])->min('sale_price');
            $item['consignment_number'] = $count;
            $circulate = $this->circulate($item['goods_id']);
            $item['circulate'] = $circulate; //流通数量
            $item['goods_price'] = $sale_price;
            $collection = false;
            if($member_id){
                $collection = (new MemberGoodsCollection())->getIsCollection($member_id, $item['goods_id']);
            }
            $item['is_collection'] =$collection;
            return $item;
        });
        return $list;
    }

    /**
     * 市场某个藏品的列表
     * @param $param
     * @param $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/6/17   17:04
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGoodsList
     */
    public function getMarketDetailsGoodsList($param,$where)
    {
        $where['goods_id'] = $param['goods_id'];
        $query = self::where($where);
        if (isset($param['sort_type']) && !empty($param['sort_type'])) {
            switch ($param['sort_type']) {
                case 1:
                    $query->order('create_time asc');
                    break;
                case 2:
                    $query->order('create_time desc');
                    break;
                case 3:
                    $query->order('sale_price asc');
                    break;
                case 4:
                    $query->order('sale_price desc');
                    break;
            }
        }
        $query->field('id,goods_id,goods_name,goods_thumb,sale_price as goods_price,create_time,box_status');
        $list = $query->paginate($param['listRows'],false,[
            'query' => Request::instance()->request()
        ]);
        return $list;
    }



    /**
     * 获取用户交易市场数据
     * @param $param
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 11:33
     */
    public function getMarketList($param)
    {
        $limit                  = $param['limit'];
        $offset                 = ($param['page'] - 1) * $limit;
        //挂售  交易 上链成功    未合成  未转增
        $where = ['box_status' =>['in',['20','30']],'is_open' => 10];
        if (!empty($param['goods_name'])){
            $where['goods_name'] = ['like','%'.$param['goods_name'].'%'];
        }
        if (!empty($param['phone'])){
            $where['b.phone'] = $param['phone'];
        }
        $data = $this->alias("a")->join("member b","a.member_id = b.member_id")->where($where)
            ->limit($offset, $limit)
            ->select();
        foreach ($data as $key=>$val){
            $data[$key]['box_status_text'] = $val['box_status']==20?"寄售中":"交易中";
        }
        $arr['data'] = $data;
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = $this->alias("a")->join("member b","a.member_id = b.member_id")->where($where)->count();
        return  $arr;
    }

    /**
     * 更新商品上下架
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 11:34
     */
    public function operation($param)
    {
        $boxInfo = $this->where('id',$param['goods_id'])->find();

        if (empty($boxInfo)){
            $this->error = "盲盒市场不存在这个商品无法下架";
            return false;
        }
        if ($boxInfo['box_status']!=20){
            $this->error = "当前商品无法修改下架状态";
            return false;
        }

        return $this->where('id',$param['goods_id'])->update(['box_status'=>10]);
    }

    /**
     * 藏品流通量
     */
    public function circulate($goods_id)
    {
        return $this->where(['goods_id'=>$goods_id,'box_status'=>['in',['10','20']],'is_open'=>10])->count();
    }

}