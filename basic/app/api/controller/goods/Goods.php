<?php

namespace app\api\controller\goods;

use app\common\components\helpers\StockUtils;
use app\common\controller\Controller;
use app\common\model\Appointment;
use app\common\model\Blindbox;
use app\common\model\Goods as GoodsModel;
use app\common\model\MemberGoods;
use app\common\model\MemberGoodsCollection;
use app\api\model\collection\Calendar;
use app\common\model\Purchase;
use app\common\model\Setting;


/**
 * 藏品管理
 */
class Goods extends Controller
{
    protected $noNeedLogin = [
        'getNewGoodsList','getBoutiqueRecommendation','getLaunchCalendar','getDetails','getBuyRule'
    ];

    /**
     * 新品发售
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.goods/getNewGoodsList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="goods_id", type="string", description="藏品ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品标题")
     * @ApiReturnParams   (name="product_types", type="string", description="1=首发 3盲盒 4申购")
     * @ApiReturnParams   (name="goods_images", type="string", description="藏品封面图")
     * @ApiReturnParams   (name="goods_price", type="string", description="藏品价格")
     * @ApiReturnParams   (name="original_number", type="string", description="限量发售数量")
     * @ApiReturnParams   (name="issue_name", type="string", description="作者名称")
     * @ApiReturnParams   (name="issue_tag", type="array", description="藏品标签")
     * @ApiReturnParams   (name="is_sold", type="array", description="10=发售中  20=发售结束")
     * @ApiReturnParams   (name="start_time", type="array", description="藏品发售时间")
     * @ApiReturnParams   (name="new_time", type="array", description="当前时间")
     */
    public function getNewGoodsList()
    {
        $param = array_merge($this->request->param(),['listRows' => 5]);
        if(!$param['page']){
           $this->error('参数错误');
        }
        $redis = \initRedis();
        $goodsIDs = $redis->LRANGE("collectionList","0","-1");
        // 获取藏品信息
        $list = [];
        foreach ($goodsIDs as $key => $value){
            $goods = $redis->hGetAll("collection:goods_id:$value");
            if(!empty($goods) && isset($goods['goods_id']) && $goods['product_types']!=5){
                $stock_num = StockUtils::getStock($goods['goods_id']);
                $stock_num = ($stock_num!=null)?$stock_num:$goods['stock_num'];
                // 修改标签
                $start_time = $goods['start_time'];
                if($this->auth->isLogin()){
                    $start_time = buyFirstInfo($goods['goods_id'],$this->auth->getUser()['member_id'],$start_time);
                }
                // 重新组装数组
                $list[$key] = [
                    'goods_id' => $goods['goods_id'],
                    'goods_name' => $goods['goods_name'],
                    'product_types' => $goods['product_types'],
                    'goods_sort' => isset($goods['goods_sort'])?$goods['goods_sort']:100,
                    'goods_images' => $goods['goods_images'],
                    'is_sold' => ($stock_num>0)?10:"20",
                    'goods_price' => $goods['goods_price'],
                    'start_time' => $start_time,
                    'new_time' => time(),
                    'issue_name' => $goods['issue_name'],
                    'issue_tag' => $goods['issue_tag'],
                    'original_number'=> isset($goods['original_number'])?$goods['original_number']:'', //发行量
                ];
            }
        }
        $key = array_column(array_values($list), 'goods_sort');
        array_multisort($key, SORT_ASC, $list);
        $page = intval($param['page']);
        $listRows = $param['listRows'];
        $total = count($list,0);
        $offset = ($param['page']-1) * $param['listRows'];
        $list = array_slice($list, $offset, $param['listRows']);
        $total_page = ceil($total/$listRows);
        $this->success('获取成功', compact('total','page','listRows','total_page','list'));
    }


    /**
     * 精品推荐
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.goods/getBoutiqueRecommendation)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="goods_id", type="array", description="藏品ID")
     * @ApiReturnParams   (name="goods_name", type="array", description="藏品标题")
     * @ApiReturnParams   (name="online_buy", type="array", description="在售")
     * @ApiReturnParams   (name="circulate", type="array", description="流通量")
     * @ApiReturnParams   (name="min_goods_price", type="array", description="最低金额")
     * @ApiReturnParams   (name="is_collection", type="array", description="是否收藏  is_collection==false 没有收藏  is_collection==true 已收藏")
     */
    public function getBoutiqueRecommendation()
    {
        $page = $this->request->post('page');
        if(!$page){
            $this->error('参数错误');
        }
        $goods_list = (new GoodsModel())->where(['is_recommend'=>10,'goods_status'=>10,'is_del'=>0])->page($page,10)->field("goods_id,goods_name,goods_thumb")->select();
        $memberGoods = new MemberGoods();
        foreach ($goods_list as $key => $value){
            $member_goods_info = $memberGoods->where(['goods_id'=>$value['goods_id'],'goods_status'=>['in',['1','2']],'is_synthesis'=>0,'is_donation'=>0])->field("sale_price")->order("sale_price asc")->find();
            $value['online_buy'] = $memberGoods->where(['goods_id'=>$value['goods_id'],'goods_status'=>['in',['1','2']],'is_synthesis'=>0,'is_donation'=>0,'sale_status'=>10])->count();
            $value['circulate'] = $memberGoods->circulate($value['goods_id']);
            $value['min_goods_price'] = empty($member_goods_info['sale_price'])?0:$member_goods_info['sale_price'];
            $collection = false;
            if($this->auth->isLogin()){
                $collection = (new MemberGoodsCollection())->getIsCollection($this->auth->getMemberId(), $value['goods_id']);
            }
            $value['is_collection'] =$collection;
        }
        $this->success('ok',compact('goods_list'));
    }


    /**
     * 获取发售日历
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute (/api/goods.goods/getLaunchCalendar)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="goods_id", type="string", description="藏品ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品名称")
     * @ApiReturnParams   (name="original_number", type="string", description="发售数量")
     * @ApiReturnParams   (name="start_time", type="string", description="开售时间")
     */
    public function getLaunchCalendar()
    {
        $page = $this->request->post('page')?:1;
        $model = new Calendar();
        $list = $model->getList($page);
        $this->success('获取成功',$list);
    }

    /**
     * 发售藏品详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.goods/getDetails)
     * @ApiParams   (name="goods_id", type="string", required=true, description="藏品ID")
     * @ApiReturnParams   (name="goods_id", type="string", description="藏品ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品标题")
     * @ApiReturnParams   (name="goods_no", type="string", description="藏品编码")
     * @ApiReturnParams   (name="goods_price", type="string", description="藏品售价")
     * @ApiReturnParams   (name="content", type="string", description="藏品详情介绍 富文本")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="藏品封面图")
     * @ApiReturnParams   (name="d_images", type="string", description="藏品详情图")
     * @ApiReturnParams   (name="create_time", type="string", description="product_types==1 或者 product_types==2 表达的是售卖的时间戳")
     * @ApiReturnParams   (name="start_time", type="string", description="product_types==1 或者 product_types==2 表达的是售卖的时间")
     * @ApiReturnParams   (name="is_sold", type="string", description="is_sold==10 可以售卖状态  is_sold==20 不可售卖,按钮要变成灰色 文字为已售罄")
     * @ApiReturnParams   (name="new_time", type="string", description="当前时间")
     * @ApiReturnParams   (name="writer_name", type="string", description="作者名字")
     * @ApiReturnParams   (name="goods_type", type="string", description="藏品类型  1视频  3图片")
     * @ApiReturnParams   (name="video_link_url", type="string", description="视频链接 藏品类型为视频展示")
     * @ApiReturnParams   (name="is_suffix", type="string", description="10 gif/png 图片格式   20/3D")
     * @ApiReturnParams   (name="original_number", type="string", description="发行量")
     * @ApiReturnParams   (name="original_number", type="string", description="发行量")
     * @ApiReturnParams   (name="appointment_start_time", type="string", description="预约开始时间 申购时返回")
     * @ApiReturnParams   (name="appointment_end_time", type="string", description="预约结束时间 申购时返回")
     * @ApiReturnParams   (name="draw_time", type="string", description="开奖时间 申购时返回")
     * @ApiReturnParams   (name="sale_time", type="string", description="开售时间 公售 申购时返回")
     * @ApiReturnParams   (name="booking_number", type="string", description="预约人数 申购时返回")
     */
    public function getDetails()
    {
        $goods_id = $this->request->post('goods_id');
        if(!$goods_id){
            $this->error('参数错误');
        }
        $detail = $this->redis->hGetAll("collection:goods_id:$goods_id");
        //获取redis 里面的库存数量
        $stock_num = StockUtils::getStock($goods_id);
        $stock_num = ($stock_num!=null)?$stock_num:$detail['stock_num'];
        // 重新组装数组
        $info = [
            'goods_id' => $detail['goods_id'],
            'goods_name' => $detail['goods_name'],
            'goods_no' => $detail['goods_no'],
            'goods_price' => $detail['goods_price'],
            'content' => htmlspecialchars_decode($detail['content']),
            'goods_thumb' => $detail['goods_images'],
            'd_images' => $detail['d_images'],
            'create_time' => $detail['start_time'],
            'start_time' => date("Y-m-d H:i:s",$detail['start_time']),
            'is_sold' => ($stock_num>0)?10:"20",
            'new_time' => time(),
            'hash_url' => isset($detail['hash_url'])?$detail['hash_url']:'',
            'writer_name' => $detail['writer_name'],
            'goods_type' => $detail['goods_type'],
            'audio_link_url' => $detail['audio_link_url'],
            'is_suffix' => isset($detail['is_suffix'])?$detail['is_suffix']:10,
            'video_link_url' => $detail['video_link_url'],
            'original_number'=> isset($detail['original_number'])?$detail['original_number']:'', //发行量
        ];
        // 判断是否是盲盒藏品
        if($detail['product_types'] == '3'){
            // 获取盲盒关联的藏品
            $open_box_type = (new GoodsModel())->where(['goods_id'=>$detail['goods_id']])->field('open_box_type')->find()['open_box_type'];
            $goods = [];
            if($open_box_type==10){
                $blindbox_goods_count = (new Blindbox())->where(['blindbox_id' => $detail['goods_id']])->count();
                $blindbox_goods = (new Blindbox())->where(['blindbox_id' => $detail['goods_id']])->limit(5)->order("sort asc")->select()->toArray();
                foreach ($blindbox_goods as $key => $value){
                    $goods[] = [
                        'goods_id' => $value['goods_id'],
                        'goods_name' => $value['goods_name'],
                        'goods_thumb' => $value['goods_thumb'],
                        'probability' => $value['probability'],
                        'goods_price' => $value['goods_price'],
                        'label' => $value['label']
                    ];
                }
            }
            $is_show =10;
            if($blindbox_goods_count>5){
                $is_show =20;
            }
            $info['is_box_show'] = $is_show;
            $info['goods'] = $goods;
        }
        // 判断是否是申购藏品
        if($detail['product_types'] == '4'){
            // 获取预约时间,抽签/付款时间,开放时间
            $appointment = new Appointment();
            $purchase_info = (new Purchase())->where(['goods_id' => $detail['goods_id']])->find()->toArray();
            $info['appointment_start_time'] = $purchase_info['appointment_start_time'];
            $info['appointment_end_time'] = $purchase_info['appointment_end_time'];
            $info['draw_time'] = $purchase_info['draw_time'];
            $info['sale_time'] = $detail['start_time'];
            $info['booking_number'] = bcadd($appointment->where(['goods_id'=>$goods_id])->count(),$purchase_info['init_booking_num']);
            $info['is_buy'] = $appointment->where(['goods_id'=>$goods_id,'member_id'=>$this->auth->member_id])->count()>0?10:20;
        }
        //判断下这个藏品是否开启了优先购买 开启了优先购买则可以提前下单抢购
        if($this->auth->isLogin()){
            $info['create_time'] = buyFirstInfo($goods_id,$this->auth->getMemberId(),$info['start_time']);
        }
        $this->success('ok',$info);
    }

    /**
     * 藏品购买须知
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.goods/getBuyRule)
     * @ApiReturnParams   (name="data", type="string", description="购买须知内容")
     */
    public function getBuyRule()
    {
        $this->success('ok',htmlspecialchars_decode(Setting::getItem('read')['buy']));
    }


}