<?php

namespace app\api\controller\member;

use app\common\components\helpers\RedisUtils;
use app\common\components\helpers\StockUtils;
use app\common\controller\Controller;
use app\common\helpers\PayTools;
use app\common\model\Blindbox;
use app\common\model\MemberBox;
use app\api\model\order\Order as OrderModel;
use app\common\model\Goods as GoodsModel;
use app\common\model\MemberGoods;
use app\common\model\Setting;
use app\common\model\Writer;
use app\api\model\member\Box as BoxModel;
use app\notice\model\Order as noticeOrderModel;


/**
 * 我的盲盒
 */
class Box extends Controller
{
    /**
     * 我的盲盒
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.box/getMyBoxList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiParams   (name="type", type="string", required=true, description="1==全部 2==未拆盒 3==已拆盒")
     * @ApiParams   (name="goods_name", type="string", required=true, description="名称搜索")
     * @ApiReturnParams   (name="order_no", type="string", description="订单编号")
     * @ApiReturnParams   (name="is_open", type="string", description="10 == 未开  20==已开")
     * @ApiReturnParams   (name="goods_name", type="string", description="盲盒的名称")
     * @ApiReturnParams   (name="goods_image", type="string", description="封面图")
     * @ApiReturnParams   (name="goods_price", type="string", description="价格")
     * @ApiReturnParams   (name="writer_name", type="string", description="作者名字")
     * @ApiReturnParams   (name="box_status", type="string", description="盲盒的状态 10 持有中  20 已寄售  30 交易中  40已交易")
     */
    public function getMyBoxList()
    {
        $type = $this->request->post('type');
        $goods_name = $this->request->post('goods_name');
        if(!$type || $type<1 || $type>3){
            $this->error('参数错误');
        }
        $page                = $this->request->param("page") ? : '1';
        $member_id           = $this->auth->member_id;
        $where               = [];
        $where['member_id']  = $member_id;
        if($goods_name){
            $where['goods_name'] = ['like',"%$goods_name%"];
        }
        switch ($type) {
            case "1":
                break;
            case "2":
                $where['is_open'] = 10;
                break;
            case "3":
                $where['is_open'] = 20;
                break;
        }
        $where['box_status'] = ['<>',40];
        $orderGoodsList = (new MemberBox())->field("order_sn,order_id,is_open,id,box_status")->with("goods")->where($where)->page($page)->order("create_time desc")->select();
        $new_array      = [];
        foreach ($orderGoodsList as $key => $val) {
            $order_info = (new OrderModel())->where(['order_id'=>$val['order_id']])->field("total_price")->find();
            $goods_info                     = GoodsModel::detail($val['goods'][0]['goods_id']);
            $writer_info                    = Writer::detail($goods_info['writer_id']);
            $new_array[$key]['order_no']    = $val['order_sn'];
            $new_array[$key]['is_open']     = $val['is_open'];
            $new_array[$key]['id']     = $val['id'];
            $new_array[$key]['goods_name']  = $val['goods'][0]['goods_name'];
            $new_array[$key]['goods_image'] = $val['goods'][0]['goods_image'];
            $new_array[$key]['goods_price'] = $order_info['total_price'];
            $new_array[$key]['writer_name'] = $writer_info['name'];
            $new_array[$key]['box_status'] = $val['box_status'];
        }
        $this->success('获取数据成功',$new_array);
    }


    /**
     * 获取盲盒的寄售权限
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.box/isSaleBoxStatus)
     * @ApiParams   (name="box_id", type="string", required=true, description="盲盒的ID")
     */
    public function isSaleBoxStatus()
    {
        $box_id = $this->request->post('box_id');
        if(!$box_id){
            $this->error('参数错误');
        }
		//判断当前用户的挂售权限
		if(!PayTools::getPayTools($this->auth->member_id)){
			$this->error('缺少收款钱包,请先在钱包列表开通钱包', \app\admin\model\Setting::getItem('read')['hangSell']);
		}
        $result = (new BoxModel())->verifyGoods($box_id,$this->auth->member_id);
        if(!$result){
            $this->error((new BoxModel())->getError()?:"参数错误");
        }
        $this->success("允许挂售");
    }

    /**
     * 提交盲盒寄售
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.box/submitSaleBox)
     * @ApiParams   (name="box_id", type="string", required=true, description="盲盒的ID")
     * @ApiParams   (name="sale_price", type="string", required=true, description="挂售价格")
     * @ApiParams   (name="operation_pwd", type="string", required=true, description="操作密码")
     * @ApiReturnParams   (name="code", type="string", description="1001 == 引导设置操作密码")
     */
    public function submitSaleBox()
    {
        $id = $this->request->post("box_id");
        $sale_price = $this->request->post("sale_price");
        $operation_pwd = $this->request->post("operation_pwd");
        if(!$id || !$sale_price || !$operation_pwd){
            $this->error("缺少参数");
        }
        if(!isAmount($sale_price)){
            $this->error("输入的价格错误！");
        }
		//判断当前用户的挂售权限
		if(!PayTools::getPayTools($this->auth->member_id)){
			$this->error('缺少收款钱包,请先在钱包列表开通钱包', \app\admin\model\Setting::getItem('read')['hangSell']);
		}
        if(!$this->auth->operation_pwd){
            $this->error("请先设置操作密码","",'1001');
        }
        if (!$this->auth->checkOperationPwd($this->auth->member_id, $operation_pwd)) {
            $this->error('操作密码不正确,请重新输入');
        }
        $result = (new BoxModel())->verifyGoods($id,$this->auth->member_id);
        if(!$result){
            $this->error((new BoxModel())->getError()?:"参数错误");
        }
        $box_details = (new MemberBox())->where(['id'=>$id])->field("goods_id,member_id")->find();
        $values      = Setting::getItem("collection");
        $goods_info = (new GoodsModel())->where(['goods_id'=>$box_details['goods_id']])->field("goods_price,is_open_consignment,consignment_minute,limit_consignment_open,top_price_limit,minimum_consignment")->find();
        if($goods_info['limit_consignment_open']==10){
            $limit_price = $goods_info['goods_price'] + $goods_info['goods_price'] * ($values['consignment_percentage'] / 100);
            if ($limit_price > $sale_price) {
                 $this->error("寄售产品的价格超过平台设置的最低价,请重新修改挂售价格;限制最低价格为{$limit_price}");
            }
            if ($values['consignment_high_percentage'] < $sale_price) {
                 $this->error("寄售产品的价格超过平台设置的最高发行价,请重新修改挂售价格;限制最高价格为{$values['consignment_high_percentage']}");
            }
        }else{
            if ($goods_info['minimum_consignment'] > $sale_price) {
                 $this->error("寄售产品的价格超过平台设置的最低价,请重新修改挂售价格;限制最低价格为{$goods_info['minimum_consignment']}");
            }
            if ($goods_info['top_price_limit'] < $sale_price) {
                 $this->error("寄售产品的价格超过平台设置的最高发行价,请重新修改挂售价格;限制最高价格为{$goods_info['top_price_limit']}");
            }
        }
        (new MemberBox())->where(['id'=>$id])->update(['sale_time'=>time(),'box_status'=>20,'sale_price'=>$sale_price]);
         $this->success("挂售成功");
    }



    /**
     * 获取盲盒的挂售列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.box/getMySaleList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiParams   (name="sale_type", type="string", required=true, description="1==正在挂售  2==已卖出")
     * @ApiReturnParams   (name="id", type="string", description="藏品ID")
     * @ApiReturnParams   (name="goods_id", type="string", description="藏品商品ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品标题")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="藏品封面图")
     * @ApiReturnParams   (name="writer_name", type="string", description="发行方 页面的作者改成发行方")
     * @ApiReturnParams   (name="goods_price", type="string", description="挂售价格")
     * @ApiReturnParams   (name="pay_name", type="string", description="买家的昵称")
     * @ApiReturnParams   (name="sale_status", type="string", description="挂售状态  10上架   20下架")
     * @ApiReturnParams   (name="collection_number", type="string", description="作品编号")
     */
    public function getMySaleList()
    {
        $page = $this->request->post("page", 1);
        $sale_type = $this->request->post("sale_type");
        if(!$page || !$sale_type || $sale_type>2){
            $this->error("缺少参数");
        }
        $where = ['is_open' => 10, 'member_id' => $this->auth->member_id];
        switch ($sale_type) {
            case "1": // 已挂售
                $where['box_status'] = 20;
                break;
            case "2":  // 已卖出
                $where['box_status'] = 40;
                break;
        }
        $model = new BoxModel();
        $list  = $model->getSaleGoodsList([
            'page' => $page,
            'listRows' => 10,
        ], $where);
        $this->success('ok',$list);
    }

    /**
     * 盲盒下架
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.box/editStatusBox)
     * @ApiParams   (name="box_id", type="string", required=true, description="盲盒的ID")
     */
    public function editStatusBox()
    {
        $box_id = $this->request->post("box_id");
        if(!$box_id){
            $this->error("缺少参数");
        }
        $member_id = $this->auth->member_id;
        $member_box = (new MemberBox())->where(['member_id'=>$member_id,'id'=>$box_id])->find();
        if(empty($member_box)){
            $this->error("未找到这个盲盒");
        }
        if($member_box['box_status']!=20){
            $this->error("非法操作");
        }
        //下架盲盒
        (new MemberBox())->where(['id'=>$box_id])->update(['box_status'=>10]);
         $this->success('下架成功');
    }

    /**
     * 修改挂售的改价
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.box/editSalePrice)
     * @ApiParams   (name="box_id", type="string", required=true, description="盲盒的ID")
     * @ApiParams   (name="sale_price", type="string", required=true, description="挂售的价格")
     */
    public function editSalePrice()
    {
        $id = $this->request->post("box_id");
        $sale_price = $this->request->post("sale_price");
        if(!$id || !$sale_price){
            $this->error("缺少参数");
        }
        if(!isAmount($sale_price)){
            $this->error("输入的价格错误！");
        }
        $model  = new MemberBox();
        $detail = $model->where(['id' => $id, 'member_id' => $this->auth->member_id])->find();
        if (!$detail) {
            $this->error('参数错误');
        }
        if ($detail['box_status'] != 20) {
            $this->error('状态错误,无法修改价格！！');
        }
		//判断当前用户的挂售权限
		if(!PayTools::getPayTools($this->auth->member_id)){
			$this->error('缺少收款钱包,请先在钱包列表开通钱包', \app\admin\model\Setting::getItem('read')['hangSell']);
		}
        $values      = Setting::getItem("collection");
        $goods_info = (new GoodsModel())->where(['goods_id'=>$detail['goods_id']])->field("goods_price,is_open_consignment,consignment_minute,limit_consignment_open,top_price_limit,minimum_consignment")->find();
        if($goods_info['limit_consignment_open']==10){
            $limit_price = $goods_info['goods_price'] + $goods_info['goods_price'] * ($values['consignment_percentage'] / 100);
            if ($limit_price > $sale_price) {
                $this->error("寄售产品的价格超过平台设置的最低价,请重新修改挂售价格;限制最低价格为{$limit_price}");
            }
            if ($values['consignment_high_percentage'] < $sale_price) {
                $this->error("寄售产品的价格超过平台设置的最高发行价,请重新修改挂售价格;限制最高价格为{$values['consignment_high_percentage']}");
            }
        }else{
            if ($goods_info['minimum_consignment'] > $sale_price) {
                $this->error("寄售产品的价格超过平台设置的最低价,请重新修改挂售价格;限制最低价格为{$goods_info['minimum_consignment']}");
            }
            if ($goods_info['top_price_limit'] < $sale_price) {
                $this->error("寄售产品的价格超过平台设置的最高发行价,请重新修改挂售价格;限制最高价格为{$goods_info['top_price_limit']}");
            }
        }
        if ($model->where(['id'=>$detail['id']])->update(['sale_price'=>$sale_price])) {
            $this->success("改价成功");
        }
        $this->error('改价失败');
    }

    /**
     * 开盲盒
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.box/openBlindBox)
     * @ApiParams   (name="box_id", type="string", required=true, description="盲盒的ID")
     */
    public function openBlindBox()
    {
        $box_id = $this->request->post("box_id");
        if(!$box_id){
            $this->error("缺少参数");
        }
        //开盒逻辑 判断盲盒是否存在 是否写入
        $member_info = $this->auth->getUser();
        $orderInfo   = (new MemberBox())->with("goods")->where(['id' => $box_id, 'member_id' => $member_info['member_id'], 'box_status' => 10])->find();
        if (empty($orderInfo)) {
             $this->error('盲盒状态错误,不可开启！');
        }
        $memberGoods = (new MemberGoods())->where(['order_id' => $orderInfo['order_id']])->find();
        if (!empty($memberGoods)) {
             $this->error('已经开过盒子了奥');
        }
        //查询订单盲盒是否已经开启过
        if ($orderInfo['is_open'] != 10) {
             $this->error('盲盒已经被开启过了');
        }
        //计算盲盒的开启  然后上链操作
        $boxList = (new Blindbox())->where(['blindbox_id' => $orderInfo['goods'][0]['goods_id'], 'stock_num' => ['>', 0]])->select()->toArray();
        if (empty($boxList)) {
             $this->error('暂无藏品,开启失败！');
        }
        //判断开盒方式 启用不同的开盒算法
        $box_details = GoodsModel::detail($orderInfo['goods'][0]['goods_id']);
        $lottery_id  = 0;
        if ($box_details['open_box_type'] == 10) {
            $gradeArray = [];
            foreach ($boxList as $val) {
                $gradeArray[$val['goods_id']] = $val['probability'];
            }
            //根据概率获取奖项id
            $lottery_id = get_rand($gradeArray);
        } else {
            //随机算法开盒
            $goods_ids = array_column($boxList, 'goods_id');
            //随机获取一个ID
            $goods_ids_key = array_rand($goods_ids);
            $lottery_id    = $goods_ids[$goods_ids_key];
        }
        if ($lottery_id == 0) {
             $this->error('网络拥堵,请稍后重试!');
        }
        $lockKey = "member:openBlindBox:member_id:{$this->auth->member_id}";
        if (RedisUtils::lock($lockKey, 10)) {
            $this->error('兑换的太快了,请等待一会兑换！！');
        }
        $goodsInfo   = (new GoodsModel())::detail($lottery_id);
        $memberGoods = (new MemberGoods())->where(['order_id' => $orderInfo['order_id']])->find();
        if(!empty($memberGoods)){
            $this->error("已经开过盒子了奥");
        }
        $Blindbox = (new Blindbox())->where(['goods_id' => $lottery_id, 'stock_num' => ['>', 0]])->find();
        if (empty($Blindbox)) {
            RedisUtils::unlock($lockKey);
            $this->error('开启失败,通道太拥挤了！');
        }
        try {
            //减少库存
            if(!StockUtils::deductStock($lottery_id, 1)){
                RedisUtils::unlock($lockKey);
                $this->error('开启失败错误代码[kc-1001]！');
            }
            (new Blindbox())->where(['goods_id' => $lottery_id])->setDec("stock_num", 1);
            (new Blindbox())->where(['goods_id' => $lottery_id])->setInc("sales_actual", 1);
            //添加藏品信息
            $goodsData = [
                'member_id'   => $member_info['member_id'],
                'order_id'    => $orderInfo['order_id'],
                'order_no'    => $orderInfo['order_sn'],
                'goods_id'    => $goodsInfo['goods_id'],
                'phone'       => $member_info['phone'],
                'nickname'    => $member_info['name'],
                'goods_no'    => $goodsInfo['goods_no'],
                'goods_name'  => $goodsInfo['goods_name'],
                'goods_thumb' => $goodsInfo['goods_thumb'],
                'goods_price' => $goodsInfo['goods_price'],
                'total_num'   => 1,
                'writer_id'   => $goodsInfo['writer_id'],
                'writer_name' => $goodsInfo['writer']['name'] ?? '',
                'hash_url'    => '',
                'source_type' => 3,
                'app_id'      => '10001',
            ];
            (new MemberGoods())->add($goodsData);
            $member_goods_insertId = (new MemberGoods())->getLastInsID();
            (new noticeOrderModel())->castingQuestionList($member_goods_insertId, $goodsInfo['goods_id'], $member_info['member_id'], "casting_question_list");
            //返回开盒的信息数据给前端  buy_num //计算次数
            $returnArray = [
                'goods_name'  => $goodsInfo['goods_name'],
                'goods_thumb' => $goodsInfo['goods_thumb'],
                'goods_id'    => $member_goods_insertId,
            ];
            //修改订单开盒状态
            (new OrderModel())->where(['order_id' => $orderInfo['order_id']])->update(['is_open' => 20]);
            (new MemberBox())->where(['order_id' => $orderInfo['order_id']])->update(['is_open' => 20]);
        } catch (\Exception $e) {
             RedisUtils::unlock($lockKey);
             $this->error($e->getMessage()?:"开启失败");
        }
        if(!empty($returnArray)){
            RedisUtils::unlock($lockKey);
            $this->success('开启成功',$returnArray);
        }

    }

}