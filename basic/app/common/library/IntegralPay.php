<?php

namespace app\common\library;

use app\common\components\helpers\StockUtils;
use app\common\model\Glory;
use app\common\model\Goods;
use app\common\model\MemberBox;
use app\common\model\MemberGoods;
use app\common\model\Order;
use app\notice\model\Order as noticeOrderModel;

use think\Db;

/**
 * 积分支付
 */
class IntegralPay
{
    public $error;

    /**
     * 积分支付
     * @ApiAuthor [Mr.Zhang]
     */
    public function integralPay($params,$member_id,$glory)
    {
        $payPrice = $params['pay_price'];
        if ($params['pay_status'] == 2) {
            $this->error = "订单已支付，不可重复操作";
            return false;
        }
        if ($glory < $payPrice) {
            $this->error = "荣誉值不足，兑换失败";
            return false;
        }
        if(!StockUtils::getStock($params['goods'][0]['goods_id'])){
            $this->error = "兑换失败--错误代码[10001]";
            return false;
        }
        Db::startTrans();
        try {
            //添加积分记录
            $integralData = [
                'member_id' => $member_id,
                'type'      => 3,
                'amount'    => -$payPrice,
                'remark'    => '荣誉值兑换商品扣除',
            ];
            (new Glory())->allowField(true)->save($integralData);
            //更新订单信息
            $res = (new Order())->where(['order_id' => $params['order_id']])->update([
                'pay_status'   => 2,
                'pay_time'     => time(),
                'order_status' => 2,
            ]);
            if (false == $res) {
                $this->error = "兑换失败";
                return false;
            }
            if ($params['order_type'] == 5) {
                $goodsInfo = (new Goods())::detail($params['goods'][0]['goods_id']);
                //添加藏品信息
                $goodsData = [
                    'member_id'   => $params['member_id'],
                    'order_id'    => $params['order_id'],
                    'order_no'    => $params['order_no'],
                    'goods_id'    => $params['goods'][0]['goods_id'],
                    'phone'       => $params['user']['phone'],
                    'nickname'    => $params['user']['name'],
                    'goods_no'    => $goodsInfo['goods_no'],
                    'goods_name'  => $params['goods'][0]['goods_name'],
                    'goods_thumb' => $params['goods'][0]['goods_image'],
                    'goods_price' => $params['goods'][0]['pay_price'],
                    'total_num'   => $params['goods'][0]['product_num'],
                    'writer_id'   => $goodsInfo['writer_id'],
                    'writer_name' => $goodsInfo['writer']['name'] ?? '',
                    'hash_url'    => '',
                    'source_type' => 8,
                    'app_id'      => '10001',
                ];
                (new MemberGoods())->add($goodsData);
                //加入铸造藏品的队列
                $member_goods_insertId = (new MemberGoods())->getLastInsID();
                (new noticeOrderModel())->castingQuestionList($member_goods_insertId, $goodsInfo['goods_id'], $params['member_id'], "casting_question_list");
            }else if($params['order_type'] == 9){
                //写入盲盒表 进行数据处理 判断订单是不是已经存在 存在则不要再次写入
                $MemberBoxInfo = (new MemberBox())->where(['order_id'=>$params['order_id']])->find();
                $goodsInfo = (new Goods())::detail($params['goods'][0]['goods_id']);
                if(empty($MemberBoxInfo)){
                    (new MemberBox())->insertData([
                        'member_id' => $params['member_id'],
                        'order_id' => $params['order_id'],
                        'order_sn' => $params['order_no'],
                        'is_open' => 10,
                        'box_status' => 10,
                        'goods_id' => $goodsInfo['goods_id'],
                        'goods_name' => $goodsInfo['goods_name'],
                        'goods_thumb' => $goodsInfo['goods_thumb'],
                    ]);
                }
            }
            // 提交事务
            Db::commit();
            return [
                'order_id' => $params['order_id'],
                'order_sn' => $params['order_no'],
            ];
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error = $e->getMessage();
            return false;

        }

    }


    /**
     * 余额支付
     * @ApiAuthor [Mr.Zhang]
     */
    public function getError()
    {
        return $this->error;
    }
}