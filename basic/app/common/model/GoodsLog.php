<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/20   17:44
 * +----------------------------------------------------------------------
 * | className:  流转记录
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


use think\Request;

class GoodsLog extends BaseModel
{
    protected $name = 'goods_log';


    /**
     *  插入记录
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface insertData
     * @Time: 2022/9/14   14:22
     */
    public function insertData($data)
    {
        $data['app_id'] = '10001';
        return $this->allowField(true)->save($data);
    }
    /**
     * 获取藏品记录列表
     * @param $param
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/6/21   10:41
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getDonationList
     */
    public function getDonationList($member_id,$type, $page)
    {
        // 查询条件
        $where1 = [];
        switch ($type){
            case "1":
                $where['sale_type'] = ['<>',-1]; // 藏品记录
                $where['member_id'] = $member_id;
                break;
            case "2":
                $where['sale_type'] = 9; // 藏品记录
                $where['member_id'] = $member_id;
                break;
            case "3":
                $where['sale_type'] = -1;
                $where['member_id'] = $member_id;
                break;
        }
        $list = self::where($where)
            ->whereOr($where1)
            ->field('id,goods_name,goods_thumb,create_time,sale_type,buy_phone,buy_member,member_id,member_goods_id')
            ->order('id desc')
            ->page($page,10)
            ->select()->each(function ($item) use($member_id,$type) {
                $memberGoodsInfo = (new MemberGoods())->where(['id'=>$item['member_goods_id']])->field('collection_number')->find();
                if($type ==2){
                    $member_info = (new Member())->where(['member_id'=>$item['member_id']])->field("name")->find();
                    $item['name'] = $member_info['name'];
                }
                if($type == 3){
                    $member_info = (new Member())->where(['member_id'=>$item['buy_member']])->field("name")->find();
                    $item['name'] = $member_info['name'];
                }
                $item['collection_number'] = $memberGoodsInfo['collection_number'];
                unset($item['member_id']);
                unset($item['buy_phone']);
            });
        return $list;
    }

    /**
     * 获取藏品流转记录
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getCollectionCirculationRecord
     * @Time: 2022/9/2   22:20
     */
    public function getCollectionCirculationRecord($param)
    {
        $list = self::where(['member_goods_id'=>$param['goods_id']])
            ->field('id,goods_name,goods_thumb,goods_price,create_time,sale_type,issue_name,buy_phone,member_id,member_goods_id')
            ->order('id desc')
            ->paginate($param['listRows'],false,[
                'query' => Request::instance()->request()
            ])->each(function ($item) use($param) {
                $member_info = (new Member())->where(['member_id'=>$item['member_id']])->field("phone")->find();
                $member_goods = (new MemberGoods())->where(['id'=>$item['member_goods_id']])->field('collection_number')->find();
                $item['giver_phone'] = phone_substr_replace($member_info['phone']);
                $item['take_over_phone'] = phone_substr_replace($item['buy_phone']);
                $item['type_name'] = ($item['member_id']=$item['member_id'])?1:2;
                $item['collection_number'] = $member_goods['collection_number'];
                unset($item['member_id']);
                unset($item['buy_phone']);
            });

        return $list;
    }


}