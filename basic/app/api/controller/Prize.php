<?php

namespace app\api\controller;

use app\admin\model\Setting;
use app\admin\model\store\Banner;
use app\common\components\helpers\RedisUtils;
use app\common\controller\Controller;
use app\common\model\Member;
use app\common\model\MemberGoods;
use app\common\model\Prize as PrizeModel;
use app\common\model\PrizeLog;
use app\notice\model\Order as noticeOrderModel;
use think\Db;
use think\Env;

/**
 * 奖品基础控制器
 */
class Prize extends Controller
{
    /**
     * 获取抽奖配置
     * @ApiAuthor 2023/4/6-liyusheng
     * @ApiRoute  (/api/prize/getPrizeConf)
     * @ApiReturnParams (name="prize_data", type="integer", required=true, description="奖品数据")
     * @ApiReturnParams (name="prize_data.name", type="integer", required=true, description="奖品名称")
     * @ApiReturnParams (name="prize_data.image", type="integer", required=true, description="奖品图片")
     * @ApiReturnParams (name="prize_data.level", type="integer", required=true, description="等级类型:0=普通,1=一等奖,2=二等奖,3=三等奖")
     * @ApiReturnParams (name="rule", type="integer", required=true, description="抽奖规则")
     * @ApiReturnParams (name="member_goods_status", type="integer", required=true, description="抽奖次数")
     */
    public function getPrizeConf()
    {
        $prize_data          = PrizeModel::field('name,image,id,level')->order('level desc')->select();
        $conf                = Setting::getItem('prize');
        $member_goods_status = MemberGoods::where(['member_id' => $this->auth->member_id, 'is_synthesis' => 0, 'goods_status' => 0, 'goods_id' => ['in', $conf['goods_id']]])->count();
        $member_goods_status += $this->auth->prize_count;
        $data                = [
            'prize_data'          => $prize_data,
            'rule'                => $conf['rule'],
            'member_goods_status' => $member_goods_status,
        ];
        $this->success($data);
    }

    /**
     * 获取抽奖记录
     * @ApiAuthor 2023/4/6-liyusheng
     * @ApiRoute  (/api/prize/getPrizeList)
     * @ApiParams (name="type", type="integer", required=true, description="记录类型:1=所有人,2=本人")
     * @ApiParams (name="page", type="integer", required=true, description="页")
     * @ApiParams (name="page_size", type="integer", required=true, description="页数量")
     * @ApiReturnParams (name="prize.name", type="integer", required=true, description="奖品名称")
     * @ApiReturnParams (name="member.name", type="integer", required=true, description="中奖用户昵称")
     * @ApiReturnParams (name="member.name", type="integer", required=true, description="中奖用户头像")
     */
    public function getPrizeList()
    {
        $param = request()->param();
        if (!isset($param['page']) || !isset($param['page_size'])) {
            $this->error('参数错误');
        }
        $where = [];
        if ($param['type'] == 2) {
            $where['member_id'] = $this->auth->member_id;
        }
        $paginate = PrizeLog::with(['prize' => function ($query) {
            $query->field('name,id,image');
        }])
            ->with(['member' => function ($query) {
                $query->field('name,member_id');
            }])
            ->field('id,member_id,prize_id,create_time')
            ->where($where)
            ->paginate($param['page_size']);
        $this->success(['list' => $paginate->items(), 'total' => $paginate->total(), 'last_page' => $paginate->lastPage()]);
    }


    /**
     * 抽奖
     * @ApiAuthor 2023/4/6-liyusheng
     * @ApiRoute  (/api/prize/luckDraw)
     * @ApiParams (name="type", type="integer", required=true, description="抽奖类型:1=单抽,2=10连")
     * @ApiReturnParams (name="type", type="integer", required=true, description="奖品类型:1=红包,2藏品,3=产品,4=空奖")
     * @ApiReturnParams (name="image", type="integer", required=true, description="奖品图片")
     * @ApiReturnParams (name="name", type="integer", required=true, description="奖品名称")
     * @ApiReturnParams (name="level", type="integer", required=true, description="等级类型:0=普通,1=一等奖,2=二等奖,3=三等奖")
     */
    public function luckDraw()
    {
        $type = request()->param('type', 1);
        $key  = "luckDraw:user:{$this->auth->member_id}";
//        if (RedisUtils::lock($key, 60)) {
//            $this->error('操作频繁');
//        }
        $conf = Setting::getItem('prize');
        if ($conf['status'] != 1) {
            RedisUtils::unlock($key);
            $this->error('本期抽奖活动已结束，敬请期待下一期');
        }
        $member_goods_status = MemberGoods::where(['member_id' => $this->auth->member_id, 'is_synthesis' => 0, 'goods_status' => 0, 'goods_id' => ['in', $conf['goods_id']]])->count();
        $member_prize_count  = $member_goods_status + $this->auth->prize_count;
        /* 抽奖 */
        $data             = PrizeModel::field('probability,id')->where('stock', '>', 0)->select();
        $prize_data_count = PrizeModel::field('probability,id')->sum('stock');
        /* 中奖数据 */
        $prize_data = [];
        /* 使用的自身抽奖次数 */
        $self_member_prize_count = 0;
        /* 使用的藏品抽奖次数 */
        $self_member_goods_prize_count = 0;
        if ($type == 1) {
            /* 处理单抽判断 */
            if ($member_prize_count == 0) {
                RedisUtils::unlock($key);
                $this->error('持有藏品不足');
            }
            if ($prize_data_count == 0) {
                RedisUtils::unlock($key);
                $this->error('转盘库存不足请联系管理进行补货');
            }
            $prize_data[] = Draw($data, 1);
            /* 自身抽奖次数不足消耗藏品进行抽奖 */
            if ($this->auth->prize_count != 0) {
                $self_member_prize_count = 1;
            } else {
                $self_member_goods_prize_count = 1;
            }
        } else {
            /* 处理10连判断 */
            if ($member_prize_count < 10) {
                RedisUtils::unlock($key);
                $this->error('持有藏品不足');
            }
            if ($prize_data_count < 10) {
                RedisUtils::unlock($key);
                $this->error('转盘库存不足请联系管理进行补货');
            }
            for ($i = 1; $i <= 10; $i++) {
                $prize_data[] = Draw($data, 1);
            }
            /* 自身大于10次消耗自身10次抽奖次数 */
            if ($this->auth->prize_count >= 10) {
                $self_member_prize_count = 10;
            } else {
                /* 计算消耗的藏品和自身的抽奖次数 */
                if ($this->auth->prize_count >= 1) {
                    $self_member_prize_count       = $this->auth->prize_count;
                    $self_member_goods_prize_count = 10 - $self_member_prize_count;
                }
            }
        }
		$data_list  = PrizeModel::field('probability,id,name,image,type')->where('stock', '>', 0)->select()->toArray();
		$data_list = array_column($data_list, null, 'id');
        Db::startTrans();
        try {
            foreach ($prize_data as $prize_item) {
				//根据ID 获取奖品的信息 写入数据库
				$goods_info = $data_list[$prize_item];
                PrizeLog::create([
                    'member_id' => $this->auth->member_id,
                    'prize_id'  => $prize_item,
					'prize_name' => $goods_info['name'],
					'prize_thumb' => $goods_info['image'],
					'prize_type' => $goods_info['type'],
                ]);
                PrizeModel::where(['id' => $prize_item])->setDec('stock');
            }
            /* 减少自身的抽奖次数 */
            Member::where('member_id', $this->auth->member_id)->setDec('prize_count', $self_member_prize_count);
            /* 创建抽奖藏品记录 */
            for ($g = 0; $g < $self_member_goods_prize_count; $g++) {
                $member_goods                 = MemberGoods::where(['member_id' => $this->auth->member_id, 'is_synthesis' => 0, 'goods_status' => 0, 'goods_id' => ['in', $conf['goods_id']]])->find();
                $member_goods->is_synthesis   = 1;
                $member_goods->synthesis_time = time();
                $member_goods->save();
                /* 销毁藏品 */
                (new noticeOrderModel())->destroyQuestionList($member_goods['asset_id'], $member_goods['shard_id'], $this->auth->member_id);
            }
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            RedisUtils::unlock($key);
            if (Env::get('app.debug')) {
                halt($exception->getMessage());
            }
            $this->error('内部错误');
        }
        RedisUtils::unlock($key);
        $res_data = [];
        foreach ($prize_data as $prize_item) {
            $res_data[] = PrizeModel::whereIn('id', $prize_item)->field('type,image,name,create_time,level,id')->find();
        }
        $this->success('抽奖成功', [
            'data' => $res_data
        ]);
    }

}