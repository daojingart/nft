<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/1/5   19:54
 * +----------------------------------------------------------------------
 * | className:发放空投记录，走异步队列
 * +----------------------------------------------------------------------
 */

namespace app\queue\controller;

use app\common\helpers\Tools;
use app\common\model\JobError;
use app\common\model\MemberGoods;
use app\notice\model\Order as OrderNoticeModel;
use app\api\model\order\Order as OrderModel;
use app\common\model\MemberBox;
use think\queue\Job;
use think\Db;

class SendAirJob
{
    public function fire(Job $job, $data)
    {
        Tools::show_msg('空投/白名单开始任务' . json_encode($data, 320));
        Db::startTrans();
        try {
            //执行具体的空投任务
            $goods_data = $data['goods_data'];
            $member_data = $data['member_data'];
            if ($goods_data['product_types'] != 3) {
                (new MemberGoods())->insertGoodsData(array_merge($goods_data, $member_data, [
                    'nickname' => $member_data['name'],
                    'total_num' => 1,
                    'writer_name' => $goods_data['writer']['name'],
                    'source_type' => 4,
                ]));
                $member_goods_insertId = (new MemberGoods())->getLastInsID();
                //加入铸造藏品的队列
                (new OrderNoticeModel())->castingQuestionList($member_goods_insertId, $goods_data['goods_id'], $member_data['member_id'], "casting_question_list");
            } else {   //空投创建订单    //执行创建订单的接口
                $model = new OrderModel;
                $order_id = $model->addBoxOrder($member_data, [
                    'goods_id'     => $goods_data['goods_id'],
                    'order_type'   => 7,
                    'order_status' => 2,
                ]);
                if($order_id){
                    //根据订单ID查询订单详情 写入盲盒表
                    $order_info = $model->where(['order_id'=>$order_id])->find();
                    $MemberBoxInfo = (new MemberBox())->where(['order_id'=>$order_id])->find();
                    if(empty($MemberBoxInfo)){
                        (new MemberBox())->insertData([
                            'member_id' => $member_data['member_id'],
                            'order_id' => $order_id,
                            'order_sn' => $order_info['order_no'],
                            'is_open' => 10,
                            'box_status' => 10,
                            'goods_id' => $goods_data['goods_id'],
                            'goods_name' => $goods_data['goods_name'],
                            'goods_thumb' => $goods_data['goods_thumb'],
                        ]);
                    }
                    $model->where(['order_id' => $order_id])->update(['pay_status' => 2]);
                }
            }
            Db::commit();
            $job->delete();
            Tools::show_msg('任务结束');
        } catch (\Exception $e) {
            Tools::show_msg('任务出错' . $e->getMessage());
            if ($job->attempts() > 3) {
                JobError::create([
                    'type' => 'send-air',
                    'content' => json_encode($data),
                    'error_msg' => $e->getMessage()
                ]);
                //通过这个方法可以检查这个任务已经重试了几次了
                $job->delete();
            } else {
                // 也可以重新发布这个任务
                $job->release(); //$delay为延迟时间
            }
            Db::rollback();
        }
    }

    public function failed($data)
    {
        // ...任务达到最大重试次数后，失败了
    }

}