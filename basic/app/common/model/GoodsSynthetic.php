<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-15 10:54
 */


namespace app\common\model;
use app\admin\model\application\GoodsSyntheticWhitelist;
use app\admin\model\Setting;
use app\api\model\collection\Goods;
use app\api\model\order\Order as OrderModel;
use app\common\controller\Task;
use app\common\model\Member as MemberModel;
use app\notice\model\Order as noticeOrderModel;
use exception\BaseException;
use think\Db;

/**
 * 商品合成数据模型
 * Class GoodsSynthetic
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-15 10:54
 */
class GoodsSynthetic extends BaseModel
{
    public function getStartTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    public function getEndTimeAttr($value)
    {
        return date('Y-m-d H:i:s' ,$value);
    }

    /**
     * 藏品关联
     * @return \think\model\relation\BelongsTo
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 11:07
     */
    public function goods()
    {
        return $this->belongsTo('goods','goods_id','goods_id');
    }

    /**
     * 关联合成条件表
     * @return int|string|void
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-21 11:16
     */
    public function counts()
    {
        return $this->hasMany('GoodsSyntheticCount','synthetic_id','id');
    }



    /**
     * 获取合成商品详情
     * @param $id
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-21 11:20
     */
    public static function getDetails($id,$member_id)
    {
        $data = self::where('id',$id)->field("id,goods_id,count,name,content,exchange,start_time,end_time,chance,whitelist_time")->find();
        $start_time = strtotime($data['start_time']);
        //判断当前请求的会员是否是白名单会员 判断白名单会员是否达到提前进场的时间
        $goodsSyntheticWhitelist = (new GoodsSyntheticWhitelist())->where(['member_id'=>$member_id,'synthetic_id'=>$id])->find();
        if(!empty($goodsSyntheticWhitelist)){
            $start_time = bcsub($start_time,$data['whitelist_time']*60);
        }

        $data['strtotime_start_time'] = $start_time;
        $data['strtotime_end_time'] = strtotime($data['end_time']);
        $data['now_time'] = time();
        $data['content'] = htmlspecialchars_decode($data['content'],1);
        //获取合成的产品
        $goods_ids = explode(',', $data['goods_id']);
        $goods_list = (new Goods())->where(['goods_id'=>['in',$goods_ids]])->field("goods_thumb,is_suffix")->select();
        if (!$goods_list){
            throw new BaseException(['msg'=>'当前合成不存在']);
        }
        //获取合成的材料列表
        $counts = GoodsSyntheticCount::where('synthetic_id',$id)->field("goods_id,count")->select();
        foreach ($counts as $key => $value){
            $goods = (new Goods())->where('goods_id',$value['goods_id'])->field("goods_id,goods_name,goods_thumb,is_suffix")->find();
            $counts[$key]['goods_name'] = $goods['goods_name'];
            $counts[$key]['goods_thumb'] = $goods['goods_thumb'];
        }
        $data['counts'] = $counts;
        $data['count'] = $data['count']-$data['exchange'];
        $data['goods_thumb'] = $goods_list;
        return $data;
    }

    /**
     * 合成商品
     * @param $id
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-21 13:36
     */
    public static function synthetic($id,$member_id,$material_id)
    {
        $data = self::getDetails($id,$member_id)->toArray();
        //判断选择的材料是否符合合成条件
        $material_ids = explode(',', $material_id);
        if(empty($material_ids)){
            throw new BaseException(['msg'=>'请先选择合成材料！']);
        }
        $my_goods_is = (new MemberGoods())->where(['member_id'=>$member_id,'id'=>['in',$material_id],'goods_status'=>0,'cast_status'=>2,'is_synthesis'=>0,'is_donation'=>0])->column('goods_id');
        if(empty($my_goods_is)){
            throw new BaseException(['msg'=>'合成材料不足！']);
        }
        $my_goods_id_count = array_count_values($my_goods_is);
        foreach ($data['counts'] as $key => $value) {
            if($my_goods_id_count[$value['goods_id']]<$value['count']){
                throw new BaseException(['msg'=>'选择的合成材料数量不足！请重新选择~！']);
            }
        }
        $goods_ids = array_column($data['counts'], 'goods_id');
        if(!empty(array_diff($goods_ids,$my_goods_is))){
            throw new BaseException(['msg'=>'合成材料不足！']);
        }
        //判断当前选择的传值数量是否
        $Synthetic = GoodsSynthetic::where('id',$data['id'])->find()->toArray();
        $Synthetic_id = $data['id'];
        if ($Synthetic['remain']<1){
            throw new BaseException(['msg'=>'藏品数量不足合成失败!']);
        }
        $start_time = strtotime($data['start_time']);
        //判断当前请求的会员是否是白名单会员 判断白名单会员是否达到提前进场的时间
        $goodsSyntheticWhitelist = (new GoodsSyntheticWhitelist())->where(['member_id'=>$member_id,'synthetic_id'=>$id])->find();
        if(!empty($goodsSyntheticWhitelist)){
            $start_time = bcsub($start_time,$data['whitelist_time']*60);
        }
        $now_time = time();
        //判断当前合成时间是否在开启时间段内
        if($start_time > $now_time || strtotime($Synthetic['end_time']) < $now_time){
            throw new BaseException(['msg'=>'活动未开始请等待!']);
        }
        //用户数据
        $member_data = Member::where('member_id',$member_id)->find()->toArray();
        //先去随机计算需要合成的藏品ID
        $goods_array = explode(',',$data['goods_id']);
        $goods_key = array_rand($goods_array);
        $data['goods_id'] = $goods_array[$goods_key];
        $goods_data = Goods::with('writer')->where('goods_id',$data['goods_id'])->find();
        //计算合成概率问题
        $is_win = self::compositeProbabilityAlgorithm($data['chance']);
        if($is_win==2){  //合成失败  减去消耗的藏品  然后记录
            foreach ($data['counts'] as &$item){
                //查询出来藏品 先把藏品销毁  然后在写入链中 去销毁
                $goodsData = MemberGoods::where(['goods_id'=>$item['goods_id'],'member_id'=>$member_id,'goods_status'=>0,'is_synthesis'=>0,'is_donation'=>0,'cast_status'=>2])->limit(0,$item['count'])->select()->toArray();
                $ids = array_column($goodsData,'id');
                MemberGoods::update(['is_synthesis'=>1,'synthesis_time'=>time()],['id'=>['in',$ids]]);
                foreach ($ids as $key => $val){
                    $memberGoodsInfo = (new MemberGoods())->where(['id'=>$val])->find();
                    (new noticeOrderModel())->destroyQuestionList($memberGoodsInfo['asset_id'],$memberGoodsInfo['shard_id'],$member_id);
                }
            }
            (new SyntheticLog())->insert([
                'synthetic_id' => $data['goods_id'],
                'name' => $goods_data['goods_name'],
                'status' => 10,
                'create_time' => time(),
                'update_time' => time(),
                'app_id' => self::$app_id,
                'member_id' => $member_id,
                'goods_synthetic_id' => $Synthetic_id,
            ]);
            return false;
        }
        //藏品数据
        Db::startTrans();
        try{
            //判断执行合成的是盲盒 还是藏品
            if($goods_data['product_types'] == '3'){  //发送盲盒藏品
                $member_data = MemberModel::where('member_id',$member_id)->find();
                $model = new OrderModel;
                $order_id = $model->addBoxOrder($member_data,[
                    'goods_id' => $data['goods_id'],
                    'order_type' => 10,
                    'order_status' => 2
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
                    $model->where(['order_id' => $order_id])->update(['pay_status' => 2,'pay_time'=>time()]);
                }
            }else{
                $member_goods = [
                    'member_id' => $member_id,
                    'order_id' => 0,
                    'order_no' => 0,
                    'goods_id' => $data['goods_id'],
                    'phone' =>$member_data['phone'],
                    'nickname' => $member_data['name'],
                    'goods_no' => $goods_data['goods_no'],
                    'goods_name' => $goods_data['goods_name'],
                    'goods_thumb' => $goods_data['goods_thumb'],
                    'goods_price' => $goods_data['goods_price'],
                    'total_num' => 1,
                    'writer_id' => $goods_data['writer']['id'],
                    'writer_name' => $goods_data['writer']['name'],
                    'source_type' => 5,
                    'app_id' => self::$app_id
                ];
                if ($Synthetic['remain']<1){
                    throw new BaseException(['msg'=>'藏品数量不足合成失败!']);
                }
                //创建合成商品
                MemberGoods::create($member_goods);
                //写入redis 进行上链操作的  加入铸造藏品的队列
                $member_goods_insertId = (new MemberGoods())->getLastInsID();
                (new noticeOrderModel())->castingQuestionList($member_goods_insertId,$data['goods_id'],$member_id,"casting_question_list");
            }
            //记录合成日志
            (new SyntheticLog())->insert([
                'synthetic_id' => $data['goods_id'],
                'name' => $goods_data['goods_name'],
                'status' => 20,
                'create_time' => time(),
                'update_time' => time(),
                'app_id' => self::$app_id,
                'member_id' => $member_id,
                'goods_synthetic_id' => $Synthetic_id,
            ]);
            //销毁藏品 根据前端传值的ID 进行销毁
            MemberGoods::update(['is_synthesis'=>1,'synthesis_time'=>time()],['id'=>['in',$material_ids]]);
            foreach ($material_ids as $item){
                $memberGoodsInfo = (new MemberGoods())->where(['id'=>$item])->find();
                (new noticeOrderModel())->destroyQuestionList($memberGoodsInfo['asset_id'],$memberGoodsInfo['shard_id'],$member_id);
            }
            //更新藏品库存
            GoodsSynthetic::where('id',$Synthetic_id)->setDec('remain',1);
            GoodsSynthetic::where('id',$Synthetic_id)->setInc('exchange',1);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            throw new BaseException(['msg'=>$e->getMessage()]);
        }
    }

    /**
     * 合成记录
     * @param $member_id
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-21 15:06
     */
    public static function syntheticRecord($member_id,$page)
    {
        $SyntheticLog = (new SyntheticLog())->field("synthetic_id,name,status,create_time")->where(['member_id'=>$member_id])->page($page,10)->select();
        foreach ($SyntheticLog as $key=>$val){
            $goods_info = \app\common\model\Goods::detail($val['synthetic_id']);
            $SyntheticLog[$key]['goods_thumb'] = $goods_info['goods_thumb'];
        }
        return $SyntheticLog;
    }

    /**
     * 合成概率的算法
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface compositeProbabilityAlgorithm
     * @Time: 2022/7/20   20:25
     */
    public static function compositeProbabilityAlgorithm($number)
    {
        $prize_arr = [
            [
                'id' => 1,'v'=>$number,
            ],
            [
                'id' => 2,'v' => bcsub(10,$number,3),
            ]
        ];
        $prize_arr = array_combine(array_column($prize_arr, 'id'), $prize_arr);

        foreach($prize_arr as $key => $val)
        {
            $arr[$val['id']] = $val['v'];
        }
        $result = '';
        $proSum = array_sum($arr);
        //概率数组循环
        foreach($arr as $key => $proCur){
            // 获取随机数
            $randNum = mt_rand(1,$proSum);
            if($randNum <= $proCur){
                $result = $key;
                break;
            }else{
                // 减掉当前中奖的概率
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return  $prize_arr[$result]['id']; //中奖项
    }
}