<?php

namespace app\api\controller\activity;

use app\api\model\order\Order;
use app\common\components\helpers\RedisUtils;
use app\common\components\helpers\StockUtils;
use app\common\controller\Controller;
use app\common\model\Goods;
use app\common\model\GoodsLog;
use app\common\model\Setting;

/**
 * 空投卷
 */
class Airdrop extends Controller
{
    /**
     * 获取空投信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.airdrop/getAirDropInfo)
     * @ApiReturnParams   (name="volume_drop", type="string", description="空投卷次数")
     * @ApiReturnParams   (name="drop_obtain", type="string", description="空投怎么获得")
     * @ApiReturnParams   (name="drop_shelves", type="string", description="空投下发的时间说明")
     * @ApiReturnParams   (name="drop_order", type="string", description="空投的发放顺序说明")
     */
    public function getAirDropInfo()
    {
        $returnArray                 = [];
        $memberInfo                  = $this->auth->getUser();
        $returnArray['volume_drop']  = $memberInfo['volume_drop'];
        $value                       = Setting::getItem('read');
        $returnArray['drop_obtain']  = $value['drop_obtain'];
        $returnArray['drop_shelves'] = $value['drop_shelves'];
        $returnArray['drop_order']   = $value['drop_order'];
        $this->success('获取成功', $returnArray);
    }

    /**
     * 执行立即空投
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.airdrop/executeAirDrop)
     */
    public function executeAirDrop()
    {
        $memberInfo = $this->auth->getUser();
        if ($memberInfo['volume_drop'] < 1) {
            $this->error('空投卷不足');
        }
        $goodsList = (new Goods())->where(['product_types' => 2, 'goods_status' => 10, 'is_del' => 0,'stock_num'=>['>',0]])->select()->toArray();
        if (empty($goodsList)) {
            $this->error('活动暂未开始,敬请期待');
        }
        $goodsIds = array_column($goodsList, 'goods_id');
        $key      = array_rand($goodsIds, 1);
        $goodsId  = $goodsIds[$key];
        $lockKey = "executeAirDrop:{$memberInfo['member_id']}";
        if (RedisUtils::lock($lockKey, 5)) {
             $this->error('网络繁忙，请稍后再试');
        }
        if (!StockUtils::getStock($goodsId)) {
             $this->error('库存不足,无法购买');
        }
        $model    = new Order();
        $param    = [
            'goods_id'   => $goodsId,
            'order_type' => 6,
        ];
        $res      = $model->add($memberInfo, $param);
        if ($res) {
            //增加转增记录
            $this->success('空投奖励已送到仓库');
        } else {
            $this->error($model->getError() ?? '下单失败');
        }
    }

}