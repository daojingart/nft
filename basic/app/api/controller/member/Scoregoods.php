<?php

namespace app\api\controller\member;

use app\api\validate\ProductValidate;
use app\common\components\helpers\RedisUtils;
use app\common\components\helpers\StockUtils;
use app\common\controller\Controller;
use app\common\model\Glory;
use app\common\model\GloryGoods;
use app\common\model\Setting;
use app\api\model\collection\Goods as GoodsModel;
use app\api\model\collection\Product as ProductModel;

/**
 * 积分兑换
 */
class Scoregoods extends Controller
{
    /**
     * 兑换空投卷
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Scoregoods/setRedeemAirdrops)
     * @ApiParams (name="number", type="string", required=true, description="兑换数量")
     */
    public function setRedeemAirdrops()
    {
        $number = $this->request->post('number');
        if (!$number || $number <= 0) {
            $this->error('请输入兑换数量');
        }
        if (!$this->redis->get(request()->ip() . 'exchange')) {
            $this->error('人机验证失败!');
        }
        $value      = Setting::getItem('drop');
        $glory      = $number * $value['exchange'];
        if ($glory > $this->auth->glory) {
            $this->error('贯余额不足');
        }
        //判断今天兑换了几次，后台设置的几次
        $lockKey = "member:setRedeemAirdrops:member_id:{$this->auth->member_id}";
        if (RedisUtils::lock($lockKey, 10)) {
            $this->error('兑换的太快了,请等待一会兑换！！');
        }
        $day_count = (new Glory())->whereTime('create_time', 'today')->where(['member_id' => $this->auth->member_id, 'type' => 4])->count();
        if (binaryCalculator($day_count,"+",$number,0) > $value['day_exchange']) {
            RedisUtils::unlock($lockKey);
            $this->error("抱歉！今天的兑换次数已经用完");
        }
        try {
            (new \app\common\model\Member())->where(['member_id' => $this->auth->member_id])->setInc('volume_drop', $number);
            $integralData = [
                'member_id' => $this->auth->member_id,
                'type'      => 4,
                'amount'    => -$glory,
                'remark'    => '荣誉值兑换空投卷',
            ];
            $res          = (new Glory())->allowField(true)->save($integralData);
        }catch (\Exception $e){
            RedisUtils::unlock($lockKey);
            $this->error($e->getMessage()?:"兑换失败");
        }
        RedisUtils::unlock($lockKey);
        if ($res) {
            $this->success('兑换成功');
        }
        $this->error("兑换失败");
    }


    /**
     * 获取兑换的藏品和盲盒
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Scoregoods/getExchangeGoodsList)
     * @ApiParams   (name="type", type="string", required=true, description="1==藏品2==盲盒")
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="id", type="string", description="兑换活动产品ID")
     * @ApiReturnParams   (name="goods_id", type="string", description="产品ID")
     * @ApiReturnParams   (name="price", type="string", description="价格")
     * @ApiReturnParams   (name="goods_name", type="string", description="名称")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="封面图")
     * @ApiReturnParams   (name="stock_num", type="string", description="库存")
     * @ApiReturnParams   (name="sales_actual", type="string", description="销量")
     */
    public function getExchangeGoodsList()
    {
        $type = $this->request->post('type');
        $page = $this->request->post('page')?:1;
        $glory      = intval($this->auth->glory ?? 0);
        if(!$type || $type<1 || $type>2){
            $this->error('参数错误');
        }
        $where              = [];
        $where['gg.is_del'] = $where['g.is_del'] = 0;
        $param['page']      = $page;
        $param['listRows']  = 10;
        $where['gg.type'] = $type;
        $query = (new GloryGoods())->alias('gg');
        $query->where($where);
        $query->join('snake_goods g', 'gg.goods_id=g.goods_id', 'left');
        $query->order('gg.id desc');
        $list  = $query->field("gg.id,gg.goods_id,gg.price,g.goods_name,g.goods_thumb,g.sales_actual")->select()->toArray();
        if (!empty($list)) {
            foreach ($list as &$val) {
                $val['price']     = intval($val['price']);
                $val['stock_num'] = StockUtils::getStock($val['goods_id']);
            }
        }
        $page       = intval($param['page']);
        $listRows   = $param['listRows'];
        $total      = count($list, 0);
        $offset     = ($param['page'] - 1) * $param['listRows'];
        $list       = array_slice($list, $offset, $param['listRows']);
        $total_page = ceil($total / $listRows);
        $this->success('获取数据成功',compact('total', 'page', 'listRows', 'total_page', 'list', 'glory'));

    }

    /**
     * 获取兑换藏品/盲盒详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Scoregoods/getDetails)
     * @ApiParams   (name="goods_id", type="string", required=true, description="藏品ID")
     * @ApiReturnParams   (name="goods_id", type="string", description="藏品ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品名称")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="藏品封面图")
     * @ApiReturnParams   (name="original_number", type="string", description="发行数量")
     * @ApiReturnParams   (name="start_time", type="string", description="开始时间")
     * @ApiReturnParams   (name="issue_name", type="string", description="发行方")
     * @ApiReturnParams   (name="hash", type="string", description="藏品hash")
     * @ApiReturnParams   (name="content", type="string", description="内容简介 富文本")
     * @ApiReturnParams   (name="price", type="string", description="价格")
     * @ApiReturnParams   (name="limit", type="string", description="限制购买数量")
     * @ApiReturnParams   (name="buy_rule", type="string", description="兑换须知")
     */
    public function getDetails()
    {
        $goods_id = $this->request->post('goods_id');
        if(!$goods_id){
            $this->error('参数错误');
        }
        $value    = Setting::getItem('read');
        // 商品详情
        $GoodsModel       = new GoodsModel();
        $goods_info       = $GoodsModel->where(['goods_id' => $goods_id])->field("goods_id,goods_name,goods_thumb,original_number,start_time,issue_name,hash,content")->find();
        $GloryGoods       = new GloryGoods();
        $glory_goods_info = $GloryGoods->where(['goods_id' => $goods_id])->find();
        if (empty($glory_goods_info)) {
            $this->error('兑换商品不存在');
        }
        $goods_info['start_time'] = date("Y-m-d H:i:s", $goods_info['start_time']);
        $goods_info['price']    = intval($glory_goods_info['price']);
        $goods_info['limit']    = $glory_goods_info['limit'];
        $goods_info['buy_rule'] = htmlspecialchars_decode($value['exchange']);
        $this->success( 'ok',$goods_info);
    }


    /**
     * 获取积分产品
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Scoregoods/getGoodsList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function getGoodsList()
    {
        $data['data']['glory'] = intval($this->auth->glory ?? 0);;
        $data = (new \app\api\model\collection\Product())->getList(10);
        $this->success('获取数据成功', $data);
    }

    /**
     * 获取积分商品详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Scoregoods/getProducyInfo)
     * @ApiParams   (name="product_id", type="string", required=true, description="产品ID")
     */
    public function getProducyInfo()
    {
        (new ProductValidate())->goCheck('getProducyInfo');
        $data = (new ProductModel())->getProducyInfo(10);
        if ($data){
            $this->success('ok',$data);
        }
        $this->error('产品不存在');
    }


    /**
     * 积分产品兑换下单
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.Scoregoods/preOrder)
     * @ApiParams   (name="product_id", type="string", required=true, description="产品ID")
     * @ApiParams   (name="sku_id", type="string", required=true, description="商品skuid")
     * @ApiParams   (name="numberOf", type="string", required=true, description="商品兑换数量")
     * @ApiParams   (name="address_id", type="string", required=true, description="商品收货地址")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function preOrder()
    {
        $member = $this->auth->getUser();
        (new ProductValidate())->goCheck('preOrder');
        $param = $this->request->param();
        $lockKey = "createProductOrder:{$param['product_id']}";
        if (RedisUtils::lock($lockKey, 60)) {
            $this->error('商品购买火爆,请重试');
        }
        $data = (new ProductModel())->preOrder($member,10);
        if ($data){
            $this->success($data);
        }
        $this->error('兑换失败');
    }

}