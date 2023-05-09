<?php
/**
 * @Email    :sliyusheng@foxmail.com
 * @Company  河南八六互联信息技术有限公司
 * @DateTime 2022-06-13 17:23
 */


namespace app\admin\service;

use app\common\components\helpers\StockUtils;
use app\common\model\Goods;
use app\common\model\Member as MemberModel;
use app\common\model\MemberGoods;
use app\common\model\MemberLabelList;
use app\queue\controller\SendAirJob;
use exception\BaseException;
use think\Queue;

/**
 * 用户业务控制器
 * Class Member
 * @package  app\admin\service
 * @Email    :sliyusheng@foxmail.com
 * @Company  河南八六互联信息技术有限公司
 * @DateTime 2022-06-13 17:23
 */
class Member extends BaseService
{
    /**
     * 更新用户状态
     * @return false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Email    :sliyusheng@foxmail.com
     * @Company  河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 18:07
     */
    public static function upStatus()
    {
        $id                  = request()->param('id');
        $member_data         = MemberModel::where('member_id', $id)->find();
        $status              = $member_data->status['value'] == 0 ? 1 : 0;
        $member_data->status = $status;
        return $member_data->save();
    }

    /**
     * 单个空投
     * @Email    :sliyusheng@foxmail.com
     * @Company  河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 20:24
     */
    public static function dropGoods($param)
    {
        //判断空投的数量 是否满足库存数量 满足可以空投
        $stock_number = StockUtils::getStock($param['goods_id']);
        if($stock_number<$param['airdrop_number']){
            throw new BaseException(['msg' => '空投藏品库存不足！无法完成本次空投任务！']);
        }
        for ($i = 1; $i <= $param['airdrop_number']; $i++) {
            self::drop($param['goods_id'], $param['member_id']);
        }
        return true;
    }

    /**
     * 分组空投
     * @param $param
     * @Email    :sliyusheng@foxmail.com
     * @Company  河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 20:46
     */
    public static function dropGoodsAll($param)
    {
        $goods_id = $param['goods_id'];
        if(!$goods_id){
            throw new BaseException(['msg' => '请先选择空投的藏品']);
        }
        $label_all = [];
        switch ($param['label_type'])
        {
            case "1": //根据标签空投
                if(empty($param['label_id'])){
                    throw new BaseException(['msg' => '请选择标签']);
                }
                $label_all = MemberLabelList::where('lable_id', $param['label_id'])->field('lable_id,member_id')->select()->toArray();
                $arr['airdrop_number'] = 1;
                array_walk($label_all, function (&$value, $key, $arr) {
                    $value = array_merge($value, $arr);
                }, $arr);
                break;
            case "2": //根据持有藏品空投固定数量
                if(empty($param['goods_ids'])){
                    throw new BaseException(['msg' => '请选择持有藏品']);
                }
                if(empty($param['airdrop_number'])){
                    throw new BaseException(['msg' => '请填写空投数量']);
                }
                $need_nft_ids = implode(',', $param['goods_ids']);
                //查询出来藏品持有人的列表
                $member_user_all = (new MemberGoods())->where(['goods_status'=>['in',['0','1']],'is_synthesis'=>0,'is_donation'=>0,'goods_id'=>['in',$need_nft_ids]])->field("member_id")->select()->toArray();
                foreach ($param['goods_ids'] as $key => $val) {
                    foreach ($member_user_all as $s => $d) {
                        $items = (new MemberGoods())->where(['goods_status'=>['in',['0','1']],'is_synthesis'=>0,'is_donation'=>0,'goods_id'=>$val,'member_id'=>$d['member_id']])->field("member_id")->find();
                        if (empty($items)) {
                            unset($member_user_all[$s]);
                        }
                    }
                }
                $member_user_all = array_column($member_user_all,'member_id');
                $member_user_all = array_unique($member_user_all);
                sort($member_user_all);
                if(empty($member_user_all)){
                    throw new BaseException(['msg' => '没有符合条件的会员']);
                }
                foreach ($member_user_all as $key=>$val){
                    $label_all[$key]['member_id']=$val;
                    $label_all[$key]['airdrop_number']=$param['airdrop_number'];
                }
                break;
            case "3": //根据持有藏品并计算藏品数量进行空投
                if(!$param['c_goods_id']){
                    throw new BaseException(['msg' => '请选择持有藏品']);
                }
                $label_all = [];
                $member_user_all = (new MemberGoods())->group("member_id")->where(['goods_status'=>['in',['0','1']],'is_synthesis'=>0,'is_donation'=>0,'goods_id'=>$param['c_goods_id']])->field("member_id")->select()->toArray();
                foreach ($member_user_all as $key => $value) {
                    $label_all[$key]['member_id'] = $value['member_id'];
                    $label_all[$key]['airdrop_number'] =(new MemberGoods())->where(['goods_status'=>['in',['0','1']],'is_synthesis'=>0,'is_donation'=>0,'goods_id'=>$param['c_goods_id'],'member_id'=>$value['member_id']])->count();
                }
                break;
        }
        //计算本次空投的总库存
        $score=array_column($label_all, 'airdrop_number');
        $sum_stock_number = array_sum($score);
        //计算本次空投的数量
        $now_stock = StockUtils::getStock($goods_id);
        if($sum_stock_number>$now_stock){
            throw new BaseException(['msg' => '空投藏品库存不足！无法完成本次空投任务！']);
        }
        //查询分组会员
        foreach ($label_all as $item) {
            for ($i = 1; $i <= $item['airdrop_number']; $i++) {
                self::drop($goods_id, $item['member_id']);
            }
        }
        return true;
    }

    /**
     * 批量空投藏品
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface importAllDrop
     * @Time      : 2022/8/3   14:09
     */
    public function importAllDrop($data)
    {
        $arr = array_column($data, 'number');
        if(array_sum($arr)>1000){
            throw new BaseException(['msg' => '单次空投数量不能超过1000个']);
        }
        foreach ($data as $key => $val) {
            $member_info = (new MemberModel())->where(['phone' => $val['phone']])->field("member_id,phone,real_status")->find();
            //if (!empty($member_info) && $member_info['real_status']['value'] == 2) {
                //增加一个for 循环体
                $number = empty($val['number'])?1:$val['number'];
                for ($i = 1; $i <= $number; $i++) {
                    self::drop($val['goods_id'], $member_info['member_id']);
                }
//            }else{
//                throw new BaseException(['msg' => '手机号为'.$val['phone'].'的用户不存在或未实名认证-当前用户前面的用户空投成功后面空投失败']);
//            }
        }
        return true;
    }


    /**
     * 批量修改次数
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface importAllDrop
     * @Time      : 2022/8/3   14:09
     */
    public function importBuyOrderAirdrop($data)
    {
        foreach ($data as $key => $val) {
            $member_info = (new MemberModel())->where(['phone' => $val['phone']])->field("member_id,phone,real_status")->find();
            if(!empty($member_info)){
                (new MemberModel())->where(['member_id' => $member_info['member_id']])->update(['purchase_limit'=>$val['number']]);
            }
        }
        return true;
    }


    /**
     * 空投商品
     * @param $param
     * @Email    :sliyusheng@foxmail.com
     * @Company  河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 19:36
     */
    public static function drop($goods_id, $member_id)
    {
        //增加一个判断 如果库存不足 无法空投
        $stock_number = StockUtils::getStock($goods_id);
        if($stock_number<1){
            throw new BaseException(['msg' => '空投藏品库存不足！无法完成空投！']);
        }
        //减少库存
        StockUtils::deductStock($goods_id, 1);
        $member_data = MemberModel::where('member_id', $member_id)->find();
        $goods_data = Goods::with('writer')->where('goods_id', $goods_id)->find();
        //if ($member_data['real_status']['value'] == 2) {
            $data = [
                'member_data' => $member_data,
                'goods_data' => $goods_data,
            ];
            Queue::push(SendAirJob::class, $data,'sendair');
        //}
    }
}