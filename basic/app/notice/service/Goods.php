<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/2/13   16:05
 * +----------------------------------------------------------------------
 * | className: 藏品加入仓库 处理
 * +----------------------------------------------------------------------
 */

namespace app\notice\service;

use app\common\model\GoodsLog;
use app\common\model\Member;
use app\common\model\MemberGoods;
use app\common\model\MemberGoodsLog;

class Goods extends \think\Controller
{
    /**
     * 处理藏品加入仓库
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface joinAddGoods
     * @Time: 2023/2/13   16:06
     */
    public function joinAddGoods($Order_info,$orderGoodsInfo,$goodsInfo,$member_info)
    {
        $goodsData = [
            'member_id' => $Order_info['member_id'],
            'order_id' => $Order_info['order_id'],
            'order_no' => $Order_info['order_no'],
            'goods_id' => $orderGoodsInfo['goods_id'],
            'phone' => $member_info['phone'],
            'nickname' => $member_info['name'],
            'goods_no' => $goodsInfo['goods_no'],
            'goods_name' => $goodsInfo['goods_name'],
            'goods_thumb' => $goodsInfo['goods_thumb'],
            'goods_price' => $goodsInfo['goods_price'],
            'total_num' => 1,
            'writer_id' => $goodsInfo['writer_id'],
            'writer_name' => $goodsInfo['writer']['name'] ?? '',
            'source_type' => $this->getComeType($goodsInfo,$Order_info),
            'app_id' => '10001',
        ];
        $memberGoodsModel = new MemberGoods();
        $memberGoodsModel->add($goodsData);
        $member_goods_id = $memberGoodsModel->getLastInsID();
        //增加藏品记录
        if($Order_info['order_type'] == 1 || $Order_info['order_type'] == 3 || $Order_info['order_type'] == 5 || $Order_info['order_type'] == 6|| $Order_info['order_type'] == 12){
            $sale_type = 1;
            if($Order_info['order_type'] == 5){
                $sale_type = 8;
            }
            if($Order_info['order_type'] == 6){
                $sale_type = 7;
            }
            if($Order_info['order_type'] == 12){
                $sale_type = 2;
            }
            //首发的增加藏品记录
            (new GoodsLog())->insertData([
                'member_id' => $Order_info['member_id'],
                'nickname' => $member_info['name'],
                'goods_id' => $orderGoodsInfo['goods_id'],
                'sale_type' => $sale_type,
                'goods_name' => $goodsInfo['goods_name'],
                'goods_thumb' => $goodsInfo['goods_thumb'],
                'goods_price' => $goodsInfo['goods_price'],
                'issue_name' => $goodsInfo['issue_name']??'',
                'buy_phone' => "",
                'buy_member' => '',
                'create_time' => time(),
                'member_goods_id' => $member_goods_id,
            ]);
        }
        return $member_goods_id;
    }

    /**
     * 获取藏品来源
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getComeType
     * @Time: 2023/2/13   16:03
     */
    public function getComeType($goodsInfo,$Order_info)
    {
        $source_type = 1;
        switch ($goodsInfo['product_types'])
        {
            case "1":
                $source_type = 1;
                break;
            case "2":
                $source_type = 4;
                break;
            case "4":
                $source_type = 2;
                break;
            case "5":
                $source_type = 3;
                break;
        }
        if($Order_info['order_type'] == 5){
            $source_type = 8;
        }
        return $source_type;
    }


    /**
     * 插入流转记录
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface insertFlowRecord
     * @Time: 2023/2/13   16:17
     */
    public function insertFlowRecord($Order_info,$member_goods_insertId,$orderGoodsInfo)
    {
        //执行转增逻辑  需要判断下支付方式  如果是钱包支付的则不要走分账了
        $MemberGoodsInfo = (new MemberGoods())::detail($Order_info['sale_goods_id']);
        //处理下编码的问题  转售编码不要变
        (new MemberGoods())->where(['id'=>$member_goods_insertId])->update(['collection_number'=>$MemberGoodsInfo['collection_number']]);
        (new MemberGoods())->where(['id'=>$MemberGoodsInfo['id']])->update(['goods_status' => 3]);
        $new_member_info = (new Member())->where(['member_id'=>$Order_info['member_id']])->find();
        // 增加转增记录
        (new MemberGoodsLog())->insertData([
            'member_id' =>  $new_member_info['member_id'],
            'nickname' => $new_member_info['name'],
            'goods_id' =>$orderGoodsInfo['goods_id'],
            'type' => 1,
            'p_member_goods_id' => $Order_info['sale_goods_id'],
            'member_goods_od' => $member_goods_insertId,
            'goods_price' => $Order_info['total_price'],
        ]);
        return $MemberGoodsInfo;
    }



}