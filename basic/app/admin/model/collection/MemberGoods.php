<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/13   20:20
 * +----------------------------------------------------------------------
 * | className: 会员藏品管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\collection;

use app\common\model\MemberGoods as MemberGoodsModel;
use app\common\model\Order;
use app\notice\model\Order as noticeOrderModel;
use exception\BaseException;


class MemberGoods extends MemberGoodsModel
{

    /**
     * 获取会员藏品列表
     * @param $filter
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time      : 2022/6/13   20:27
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($filter, $param)
    {
        // 按照商品名称查询
        if (!empty($param['goods_name'])) {
            $filter['goods_name'] = ['like', '%' . $param['goods_name'] . '%'];
        }
        if (!empty($param['source_type'])) {
            $filter['source_type'] = $param['source_type'];
        }
        if (!empty($param['member_id'])) {
            $filter['member_id'] = $param['member_id'];
        }
        if (!empty($param['goods_id'])) {
            $filter['goods_id'] = $param['goods_id'];
        }
        if (!empty($param['phone'])) {
            $filter['phone'] = $param['phone'];
        }
        if (isset($param['cast_status'])) {
            $filter['cast_status'] = $param['cast_status'];
        }
        if (isset($filter['types']) || !empty($filter['types'])) {
            $filter['source_type'] = $filter['types'];
            unset($filter['types']);
        }

        if (isset($param['type']) && $param['type'] == 'export') {
            $dataList = $this->where($filter)
                ->order(['id' => 'desc'])
                ->field("phone,nickname,goods_name,collection_number,list_time,id")
                ->select();
        } else {
            $limit                  = $param['limit'];
            $offset                 = ($param['page'] - 1) * $limit;
            $dataList = $this->where($filter)
                ->order(['id' => 'desc'])
                ->limit($offset, $limit)
                ->select();
        }
        foreach ($dataList as $k => $v) {
            $dataList[$k]['collection_number'] = empty($v['collection_number']) ? "--" : $v['collection_number'];
            $dataList[$k]['list_time']         = !empty($v['list_time']) ? date('Y-m-d H:i:s', $v['list_time']) : '';
            $dataList[$k]['operate']           = showNewOperate(self::makeButton($v['id']));
        }
        $return['count'] = $this->where($filter)->count();
        $return['data']  = $dataList;
        $return['code']  = 0;
        $return['msg']   = 'OK';

        return $return;
    }

    /**
     * 操作按钮
     * @param $id
     * @return array
     * @Time      : 2022/6/13   20:25
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static function makeButton($id)
    {
        return [
            '删除藏品' => [
                'href'      => "javascript:void(0)",
                'lay-event' => 'remove',
            ],
        ];
    }

    /**
     * 删除空投产品
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface remove
     * @Time      : 2022/6/27   19:09
     */
    public function remove()
    {
        if (isset($this['asset_id']) && $this['hash_url']) {
            //销毁空投产品、然后进行删除这个藏品
            (new noticeOrderModel())->destroyQuestionList($this['asset_id'], $this['shard_id'], $this['member_id']);
        }
        return $this->where(['id' => $this['id']])->delete();
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
    public function secureMemberDeal($param)
    {
        //挂售  交易 上链成功    未合成  未转增
        $where = ['goods_status' =>['in',['1','2']],'cast_status' => 2,'is_synthesis' => 0,'is_donation' => 0];
        if (!empty($param['goods_name'])){
            $where['goods_name'] = ['like','%'.$param['goods_name'].'%'];
        }
        if (!empty($param['writer_id'])){
            $where['writer_id'] = $param['writer_id'];
        }
        $page = request()->param('page')?:1;//当前第几页
        $limit = request()->param('limit')?:10;//每页显示几条
        $data = MemberGoodsModel::where($where)
        ->paginate($limit,false,$config = ['page'=>$page])
        ->toArray();
        $arr['data'] = $data['data'];
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = $data['total'];
        return  json($arr);
    }

    /**
     * 更新商品上下架
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 11:34
     */
    public function operation($param)
    {
        $goods = MemberGoodsModel::where('id',$param['goods_id'])->find();
        if (!$goods){
            throw new BaseException(['msg'=>'当前市场藏品不存在']);
        }
        if ($goods['goods_status']!=1){
            throw new BaseException(['msg'=>'当前藏品不能更改上下架状态']);
        }
        $sale_status = 10;
        if ($goods['sale_status']==10){
            $sale_status = 20;
        }
        $goods->sale_status = $sale_status;
        $goods->goods_status = 0;
        if ($goods->save()){
            return true;
        }
        return  false;
    }

    /**
     *  持仓排行榜查询
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getPositionRanking
     * @Time: 2022/8/30   14:51
     */
    public function getPositionRanking($param)
    {
        $page = request()->param('page')?:1;
        $limit = request()->param('limit')?:20;
        $filter = [];
        $filter['is_donation'] = 0;
        $filter['is_synthesis'] = 0;
        $filter['goods_status'] = ['in',['0','1']];
        if (!empty($param['goods_id'])) {
            $filter['goods_id'] =$param['goods_id'];
        }
        if (isset($param['type']) && $param['type'] == 'export') {
            $dataList = $this->alias('a')
                ->join('member b','a.member_id=b.member_id','left')
                ->where($filter)
                ->field('a.goods_name,a.member_id,a.id,a.goods_id,b.name as nickname,b.phone')
                ->group("a.member_id")
                ->limit(0,300)
                ->select()->toArray();
        }else{
            $dataList = $this->alias('a')
                ->join('member b','a.member_id=b.member_id','left')
                ->where($filter)
                ->field('a.goods_name,a.member_id,a.id,a.goods_id,b.name as nickname,b.phone')
                ->limit(0,300)
                ->group("a.member_id")->select()->toArray();
        }
        foreach ($dataList as $key=>$val){
            $goods_count = $this->where(['member_id'=>$val['member_id'],'goods_id'=>$val['goods_id'],'goods_status'=>['in',['0','1']],'is_donation'=>0,'is_synthesis'=>0])->count();
            $dataList[$key]['goods_number'] = $goods_count;
        }
        $key = array_column(array_values($dataList), 'goods_number');
        array_multisort($key, SORT_DESC, $dataList);
        $arr['data'] = $dataList;
        $arr['code'] = 0;
        $arr['msg'] = 'OK';
        $arr['count'] = 0;
        return $arr;
    }

    /**
     * 更新商品上下架
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 11:34
     */
    public function batchAll($param)
    {
        return $this->where(['id'=>['in',$param]])->update(['goods_status'=>0,'sale_status'=>20]);
    }


	/**
	 * 更新商品上下架
	 * @Email:sliyusheng@foxmail.com
	 * @Company 河南八六互联信息技术有限公司
	 * @DateTime 2022-08-20 11:34
	 */
	public function lockGoods($param,$update)
	{
		//判断这个产品是否有待支付订单 有的话禁止解锁操作
		$order_count = (new Order())->where(['sale_goods_id'=>['in',$param],'order_status'=>1])->count();
		if($order_count>0){
			$this->error = "当前藏品有待支付订单，禁止解锁操作!";
			return false;
		}
		return $this->where(['id'=>['in',$param]])->update($update);
	}

}