<?php

namespace app\api\controller\member;

use app\admin\model\Setting;
use app\common\components\helpers\RedisUtils;
use app\common\controller\Controller;
use app\common\helpers\PayTools;
use app\common\model\GoodsLog;
use app\common\model\MemberBox;
use app\common\model\MemberGoods;
use app\api\model\collection\Goods as GoodsModel;


/**
 * 我的藏品
 */
class Goods extends Controller
{

    /**
     * 获取我的藏品
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/getGatherGoodsList)
     * @ApiParams (name="goods_name", type="string", required=true, description="藏品/盲盒标题")
     * @ApiParams (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="goods_id", type="string", description="藏品ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品标题")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="藏品封面图")
     * @ApiReturnParams   (name="number", type="string", description="藏品数量")
     */
    public function getGatherGoodsList()
    {
        $goods_name = $this->request->post('goods_name');
        $page = $this->request->post('page')?:1;
        $member_id = $this->auth->member_id;
        $model     = new MemberGoods();
        $list      = $model->groupGoodsList([
            'member_id' => $member_id,
            'goods_name' => $goods_name,
            'page' => $page
        ]);
        $this->success('获取成功',$list);
    }

    /**
     * 获取我的藏品列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/getMygoodsList)
     * @ApiParams   (name="goods_id", type="string", required=true, description="藏品ID")
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="id", type="string", description="藏品的ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品标题")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="藏品封面图")
     * @ApiReturnParams   (name="create_time", type="string", description="创建时间")
     * @ApiReturnParams   (name="cast_status", type="string", description="铸造状态  1铸造中  2铸造成功  3铸造失败")
     * @ApiReturnParams   (name="sale_status", type="string", description="挂售状态  10上架   20下架")
     * @ApiReturnParams   (name="goods_status", type="string", description="藏品状态  0持有中  1挂售中  2交易中 3已出售  4已回收")
     * @ApiReturnParams   (name="goods_price", type="string", description="藏品价格")
     * @ApiReturnParams   (name="collection_number", type="string", description="藏品编码")
     */
    public function getMygoodsList()
    {
        $goods_id = $this->request->post('goods_id');
        $page = $this->request->post('page')?:1;
        if(!$goods_id){
            $this->error("缺少参数");
        }
        $member_id = $this->auth->member_id;
        $model     = new MemberGoods();
        $list      = $model->getMyGoodsList([
            'member_id' => $member_id,
            'goods_id' => $goods_id,
            'page' => $page
        ]);
        $this->success('ok',$list);
    }

    /**
     * 获取我的藏品详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/getDetail)
     * @ApiParams   (name="id", type="string", required=true, description="藏品ID")
     * @ApiReturnParams   (name="id", type="string", description="我的藏品ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品名称")
     * @ApiReturnParams   (name="content", type="string", description="藏品介绍")
     * @ApiReturnParams   (name="d_images", type="string", description="藏品图")
     * @ApiReturnParams   (name="goods_no", type="string", description="编号")
     * @ApiReturnParams   (name="goods_type", type="string", description="藏品类型  1视频  2音频  3图片")
     * @ApiReturnParams   (name="audio_link_url", type="string", description="音频链接")
     * @ApiReturnParams   (name="video_link_url", type="string", description="视频链接")
     * @ApiReturnParams   (name="hash_url", type="string", description="HASH")
     * @ApiReturnParams   (name="create_time", type="string", description="创建时间")
     * @ApiReturnParams   (name="writer_name", type="string", description="作者名称")
     * @ApiReturnParams   (name="now_time", type="string", description="当前时间")
     * @ApiReturnParams   (name="start_time", type="string", description="购买的时间")
     * @ApiReturnParams   (name="end_time", type="string", description="转增的结束时间")
     * @ApiReturnParams   (name="g_end_time", type="string", description="挂售的结束时间")
     * @ApiReturnParams   (name="sale_switch", type="string", description="10=允许挂售 20=不允许挂售")
     * @ApiReturnParams   (name="is_suffix", type="string", description="10  gif   20/3D")
     * @ApiReturnParams   (name="original_number", type="string", description="发行量")
     * @ApiReturnParams   (name="circulate_number", type="string", description="流通量")
     */
    public function getDetail()
    {
        $id = $this->request->post("id");
        if(!$id){
            $this->error("缺少参数");
        }
        $member_info = $this->auth->getUser();
        $model       = new MemberGoods();
        $detail      = $model->where(['member_id' => $member_info['member_id'], 'id' => $id,'cast_status'=>2,'is_donation'=>0,'is_synthesis'=>0])->find();
        if (!$detail) {
            $this->error('未获取到藏品详情！');
        }
        if ($detail['cast_status'] == 1) {
            $this->error('铸造中,请稍后再试');
        }
        $goodsModel = new GoodsModel();
        $goods_info = $goodsModel->detail($detail['goods_id']);
        if(empty($goods_info)){
            $this->error('藏品不存在');
        }
        // 获取须知配置
        $collection  = Setting::getItem('collection');
        $minute_time = $collection['time']; //转增时间
        $sale_time   = $collection['sale_time']; //寄售时间
        //根据藏品的配置开关 获取藏品的转增时间
        if ($goods_info['is_open_increase'] == 20) { //单独设置转增时间
            $minute_time = $goods_info['increas_minute'];
        }
        if ($goods_info['is_open_consignment'] == 20) { //单独设置转增时间
            $sale_time = $goods_info['consignment_minute'];
        }
        // 重新组装数组
        $info = [
            'id'       => $detail['id'],
            'goods_name'     => $detail['goods_name'],
            'content'    => $goods_info['content'],
            'd_images'       => $goods_info['d_images'],
            'goods_no'       => $detail['collection_number'],
            'goods_type'     => $goods_info['goods_type'],
            'audio_link_url' => $goods_info['audio_link_url'],
            'video_link_url' => $goods_info['video_link_url'],
            'hash_url'       => $detail['hash_url'],
            'create_time'    => $goods_info['create_time'],
            'writer_name' => $goods_info['writer']['name'],
            'now_time'       => time(),
            'start_time'     => strtotime($detail['create_time']),
            'end_time'       => $minute_time*60+strtotime($detail['create_time']),
            'g_end_time'     => $sale_time*60+strtotime($detail['create_time']),
            'sale_switch'    => $collection['permissions'], // 挂售开关
            'is_suffix'      => isset($goods_info['is_suffix']) ? $goods_info['is_suffix'] : 10,
            'original_number' => $goods_info['original_number'], //发行量
            'circulate_number' =>(new MemberGoods())->circulate($detail['goods_id']) //流通量
        ];
        $this->success('获取信息成功',$info);
    }

    /**
     * 获取转增的提示信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (get)
     * @ApiRoute  (/api/member.goods/getIncreaseSetting)
     * @ApiReturnParams   (name="data", type="string", description="文本域格式信息")
     */
    public function getIncreaseSetting()
    {
        $this->success('ok',Setting::getItem('read')['donation']);
    }


    /**
     * 获取转增页面藏品详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/getIncreaseGoodsInfo)
     * @ApiParams   (name="id", type="string", required=true, description="藏品的ID")
     * @ApiReturnParams   (name="id", type="string", description="藏品的ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品标题")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="藏品封面图")
     * @ApiReturnParams   (name="collection_number", type="string", description="藏品编号")
     */
    public function getIncreaseGoodsInfo()
    {
        $id = $this->request->post("id");
        if(!$id){
            $this->error("缺少参数");
        }
        $model  = new MemberGoods();
        $detail = $model->where(['member_id' => $this->auth->member_id, 'id' => $id,'goods_status'=>0,'cast_status'=>2,'is_synthesis'=>0,'is_donation'=>0])->find();
        if (!$detail) {
             $this->error('藏品不符合转增条件无法转增！！！');
        }
        $minute     = Setting::getItem('collection')['time'];
        $goodsModel = new GoodsModel();
        $goods_info = $goodsModel->detail($detail['goods_id']);
        if ($goods_info['is_open_increase'] == 20) { //单独设置转增时间
            $minute = $goods_info['increas_minute'];
        }
        if ($goods_info['is_open_increase'] == 30) { //单独设置转增时间
            $this->error('不符合转赠规则;无法进行转赠');
        }
        $new_time = time();
        $end_time = $minute*60+strtotime($detail['create_time']);
        if ($new_time < $end_time) {
            $this->error("转赠时间未到,不可转赠");
        }
        // 重新组装数组
        $info = [
            'id'      => $detail['id'],
            'goods_name'    => $detail['goods_name'],
            'goods_thumb'   => $detail['goods_thumb'],
            'collection_number'   => $detail['collection_number'],
        ];
        $this->success( 'ok',$info);
    }

    /**
     * 提交确认转增
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/toFormMemberGoods)
     * @ApiParams   (name="id", type="string", required=true, description="藏品的ID")
     * @ApiParams   (name="phone", type="string", required=true, description="受赠人的手机号")
     * @ApiParams   (name="operation_pwd", type="string", required=true, description="受赠人的手机号")
     * @ApiReturnParams   (name="code", type="string", description="1001 == 引导设置操作密码")
     */
    public function toFormMemberGoods()
    {
        $id = $this->request->post("id");
        $phone = $this->request->post("phone");
        $operation_pwd = $this->request->post("operation_pwd");
        if(!$id || !$phone || !$operation_pwd){
            $this->error("缺少参数");
        }
        if(!checkMobile($phone)){
            $this->error("手机号错误");
        }
        if(!$this->auth->operation_pwd){
            $this->error("请先设置操作密码","",'1001');
        }
        if (!$this->auth->checkOperationPwd($this->auth->member_id, $operation_pwd)) {
             $this->error('操作密码不正确,请重新输入');
        }
        $info = (new \app\common\model\Member())->where(['phone' => $phone,'is_del'=>1,'status'=>1])->find();
        if (!$info) {
             $this->error('用户不存在或者用户状态异常,无法转赠');
        }
        if($info['real_status']['value'] !=2){
            $this->error('用户未实名认证,无法转赠');
        }
        if ($this->auth->member_id == $info['member_id']) {
            $this->error('不能转赠给自己');
        }
        $order_details = (new \app\common\model\Order())->where(['order_type'=>3,'sale_goods_id'=>$id,'pay_status'=>2])->find();
        if(!empty($order_details)){
            $this->error('网络错误!');
        }
        $lockKey = "member:toFormMemberGoods:member_id:{$this->auth->member_id}";
        if (RedisUtils::lock($lockKey, 10)) {
            $this->error('点击的太快了！!');
        }
        $model = new MemberGoods();
        try {
            $res= $model->toDonation($this->auth->getUser(), $info,$id);
        } catch (\Exception $e) {
            RedisUtils::unlock($lockKey);
            $this->error($e->getMessage()?:"转赠失败");
        }
        RedisUtils::unlock($lockKey);
        if ($res) {
            $this->success('转赠成功');
        }
        $this->error($model->getError() ?: '转赠失败');
    }

    /**
     * 获取挂售的权限
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (get)
     * @ApiRoute  (/api/member.goods/getSaleAuth)
     * @ApiParams   (name="id", type="string", required=true, description="藏品的ID")
     * @ApiReturnParams   (name="code", type="string", description="1002 时间未到")
     * @ApiReturnParams   (name="data", type="string", description="文本域格式信息;")
     */
    public function getSaleAuth()
    {
        $member_id = $this->auth->member_id;
        $id = $this->request->get('id');
        if(!$id){
            $this->error("缺少参数");
        }
        $model  = new MemberGoods();
        $detail = $model->where(['member_id' => $member_id, 'id' => $id,'goods_status'=>0,'cast_status'=>2,'is_synthesis'=>0,'is_donation'=>0])->find();
        if (!$detail) {
            $this->error('藏品已被挂售',Setting::getItem('read')['hangSell']);
        }
		//判断当前用户的挂售权限
		if(!PayTools::getPayTools($member_id)){
			$this->error('缺少收款钱包,请先在钱包列表开通钱包',Setting::getItem('read')['hangSell']);
		}
        $collection     = Setting::getItem('collection');
		$minute = $collection['sale_time'];
		if ($collection['permissions'] == 20) {
			$this->error("寄售权限已关闭,无法寄售");
		}
        $goodsModel = new GoodsModel();
        $goods_info = $goodsModel->detail($detail['goods_id']);
        if ($goods_info['is_open_consignment'] == 20) { //单独设置转增时间
            $minute = $goods_info['consignment_minute'];
        }
        if ($goods_info['is_open_consignment'] == 30) { //单独设置转增时间
            $this->error('不符合挂售规则;无法进行挂售',Setting::getItem('read')['hangSell']);
        }
        $new_time = time();
        $end_time = $minute*60+strtotime($detail['create_time']);
        if ($new_time < $end_time) {
            $this->error("挂售时间未到,请耐心等待！", Setting::getItem('read')['hangSell'], 0);
        }
        $this->success("可以挂售");
    }

    /**
     * 提交确认挂售
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/submitSale)
     * @ApiParams   (name="id", type="string", required=true, description="藏品的ID")
     * @ApiParams   (name="sale_price", type="string", required=true, description="挂售价格")
     * @ApiParams   (name="operation_pwd", type="string", required=true, description="操作密码")
     * @ApiReturnParams   (name="code", type="string", description="1001 == 引导设置操作密码")
     */
    public function submitSale()
    {
        $id = $this->request->post("id");
        $sale_price = $this->request->post("sale_price");
        $operation_pwd = $this->request->post("operation_pwd");
        if(!$id || !$sale_price || !$operation_pwd){
            $this->error("缺少参数");
        }
        if(!$this->auth->operation_pwd){
            $this->error("请先设置操作密码","",'1001');
        }
        if (!$this->auth->checkOperationPwd($this->auth->member_id, $operation_pwd)) {
            $this->error('操作密码不正确,请重新输入');
        }
        if(!isAmount($sale_price)){
            $this->error("输入的价格错误！");
        }
		//判断当前用户的挂售权限
		if(!PayTools::getPayTools($this->auth->member_id)){
			$this->error('缺少收款钱包,请先在钱包列表开通钱包',Setting::getItem('read')['hangSell']);
		}
        $values      = Setting::getItem("collection");
		if ($values['permissions'] == 20) {
			$this->error("寄售权限已关闭,无法寄售");
		}
        $member_info = $this->auth->getUser();
        $member_id   = $member_info['member_id'];
        $model       = new MemberGoods();
        $detail = $model->where(['member_id' => $member_id, 'id' => $id,'goods_status'=>0,'cast_status'=>2,'is_synthesis'=>0,'is_donation'=>0])->find();
        if(!$detail){
            $this->error('不符合挂售规则;无法进行挂售；检查是否已经寄售！');
        }
        $sale_time   = $values['sale_time'];
        $goodsModel  = new GoodsModel();
        $goods_info  = $goodsModel->detail($detail['goods_id']);
        if ($goods_info['is_open_consignment'] == 20) { //单独设置转增时间
            $sale_time = $goods_info['consignment_minute'];
        }
        if ($goods_info['is_open_consignment'] == 30) { //单独设置转增时间
             $this->error('不符合挂售规则;无法进行挂售');
        }
        //获取发行价
        if (strtotime($detail['create_time']) + $sale_time * 60 > time()) {
            $this->error('冻结时间未到无法寄售！');
        }
        if($goods_info['limit_consignment_open']==10){
            $limit_price = $detail['goods_price'] + $detail['goods_price'] * ($values['consignment_percentage'] / 100);
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
        //判断填写的寄售价格
        if ($model->toSale($detail, $sale_price)) {
            $this->success('挂售成功');
        }
        $this->error('挂售失败');
    }


    /**
     * 获取我的挂售列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/getMySaleList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiParams   (name="sale_type", type="string", required=true, description="1==挂售中  2==已卖出")
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
        $where = ['cast_status' => 2, 'is_synthesis' => 0, 'is_donation' => 0, 'member_id' => $this->auth->member_id];
        switch ($sale_type) {
            case "1": // 已挂售
                $where['goods_status'] = 1;
                $where['sale_status']  = 10;
                break;
            case "2":  // 已卖出
                $where['goods_status'] = 3;
                break;
        }
        $model = new MemberGoods();
        $list  = $model->getSaleGoodsList([
            'page' => $page,
            'listRows' => 10,
        ], $where);
        $this->success('ok',$list);

    }


    /**
     * 修改藏品上下架
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/editStatus)
     * @ApiParams   (name="id", type="string", required=true, description="藏品的ID")
     * @ApiParams   (name="status", type="string", required=true, description="status==10  发起上架  20==发起下架")
     */
    public function editStatus()
    {
        $id = $this->request->post("id");
        $status = $this->request->post("status");
        if(!$id || !$status){
            $this->error("缺少参数");
        }
        $model  = new MemberGoods();
        $detail = $model->where(['id' => $id, 'member_id' => $this->auth->member_id])->find();
        if (!$detail) {
            $this->error('参数错误');
        }
        if ($detail['goods_status'] != 1) {
            $this->error('不可修改状态');
        }
        if ($model->toStatus(['id'=>$id,'status'=>$status])) {
            if ($status == 10) {
                 $this->success("下架成功");
            } else {
                $this->success("上架成功");
            }
        }
        $this->error('修改失败');
    }


    /**
     * 修改挂售的改价
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/editSalePrice)
     * @ApiParams   (name="id", type="string", required=true, description="藏品的ID")
     * @ApiParams   (name="sale_price", type="string", required=true, description="挂售的价格")
     */
    public function editSalePrice()
    {
        $id = $this->request->post("id");
        $sale_price = $this->request->post("sale_price");
        if(!$id || !$sale_price){
            $this->error("缺少参数");
        }
        if(!isAmount($sale_price)){
            $this->error("输入的价格错误！");
        }
        $model  = new MemberGoods();
        $detail = $model->where(['id' => $id, 'member_id' => $this->auth->member_id])->find();
        if (!$detail) {
            $this->error('参数错误');
        }
        if ($detail['sale_status'] != 10) {
            $this->error('藏品已下架');
        }
        $values = Setting::getItem("collection");
        //判断填写的寄售价格
        $goodsModel  = new GoodsModel();
        $goods_info  = $goodsModel->detail($detail['goods_id']);
        if($goods_info['limit_consignment_open']==10){
            $limit_price = $detail['goods_price'] + $detail['goods_price'] * ($values['consignment_percentage'] / 100);
            if ($limit_price > $sale_price) {
                $this->error("寄售产品的价格超过平台设置的最低价,请重新修改挂售价格;限制最低价格为{$limit_price}");
            }
            $limit_high_price = $values['consignment_high_percentage'];
            if ($limit_high_price < $sale_price) {
                $this->error("寄售产品的价格超过平台设置的最高发行价,请重新修改挂售价格;限制最高价格为{$limit_high_price}");
            }
        }else{
            if ($goods_info['minimum_consignment'] > $sale_price) {
                 $this->error("寄售产品的价格超过平台设置的最低价,请重新修改挂售价格;限制最低价格为{$goods_info['minimum_consignment']}");
            }
            if ($goods_info['top_price_limit'] < $sale_price) {
                 $this->error("寄售产品的价格超过平台设置的最高发行价,请重新修改挂售价格;限制最高价格为{$goods_info['top_price_limit']}");
            }
        }
        if ($detail['goods_status'] != 1) {
            $this->error('不可改价');
        }
        if ($model->toPrice(['id'=>$id,'price'=>$sale_price])) {
            $this->success("改价成功");
        }
        $this->error('改价失败');
    }

    /**
     * 获取藏品证书页面
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/getCertificate)
     * @ApiParams   (name="id", type="string", required=true, description="藏品的ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品名称")
     * @ApiReturnParams   (name="collection_number", type="string", description="藏品编号")
     * @ApiReturnParams   (name="hash_url", type="string", description="HASH")
     * @ApiReturnParams   (name="writer_name", type="string", description="作者")
     * @ApiReturnParams   (name="issue_name", type="string", description="发行方")
     * @ApiReturnParams   (name="nickname", type="string", description="收藏人")
     */
    public function getCertificate()
    {
        $member_id = $this->auth->member_id;
        $goods_id  = $this->request->param('id');
        if (!$goods_id) {
            $this->error('参数错误');
        }
        $model  = new MemberGoods();
        $goods_info = $model->where(['member_id' => $member_id, 'id' => $goods_id])->find();
        if(empty($goods_info)){
            $this->error("藏品不存在");
        }
        $detail  = (new GoodsModel())->where(['goods_id' => $goods_info['goods_id']])->find();
        //获取流转信息
        $info = [
            'goods_name'  => $goods_info['goods_name'],
            'collection_number'  => $goods_info['collection_number'],
            'hash_url'    => $goods_info['hash_url'],
            'writer_name' => $goods_info['writer_name'], // 创作者
            'issue_name'  => $detail['issue_name'], // 发行方
            'nickname'    => $goods_info['nickname'],
        ];
        $this->success( 'ok',$info);
    }


    /**
     * 获取藏品记录
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/getCollectionRecords)
     * @ApiParams   (name="type", type="string", required=true, description="1=藏品记录  2=获赠记录 3=赠送记录")
     * @ApiReturnParams   (name="id", type="string", description="记录ID")
     * @ApiReturnParams   (name="goods_name", type="string", description="藏品名称")
     * @ApiReturnParams   (name="goods_thumb", type="string", description="藏品封面图")
     * @ApiReturnParams   (name="create_time", type="string", description="创建时间")
     * @ApiReturnParams   (name="sale_type", type="string", description="1抢购  2预约  3盲盒  4空投  5合成  6兑换码兑换  7=空投  8荣誉值兑换 9 藏友赠送  -1转增出去")
     * @ApiReturnParams   (name="collection_number", type="string", description="藏品编号")
     * @ApiReturnParams   (name="name", type="string", description="type=2和type=3时返回
     */
    public function getCollectionRecords()
    {
        $page = $this->request->post('page', 1);
        $type = $this->request->post('type', 1);
        if(!$page || !$type){
            $this->error("缺少参数");
        }
        $model = new GoodsLog();
        $this->success('ok',$model->getDonationList($this->auth->member_id, $type, $page));
    }

    /**
     * 我的藏品/盲盒 数量
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.goods/getMyGoodsNumber)
     * @ApiParams   (name="type", type="string", required=true, description="1=藏品  2=盲盒")
     * @ApiReturnParams   (name="on_saleNumber", type="string", description="正在挂售")
     * @ApiReturnParams   (name="soldNumber", type="string", description="已售出")
     */
    public function getMyGoodsNumber()
    {
        $type = $this->request->post('type', 1);
        if($type>2){
            $this->error("参数错误");
        }
        if($type==1){
            $model = new MemberGoods();
            $on_saleNumber = $model->where(['member_id'=>$this->auth->member_id,'goods_status'=>1,'is_synthesis'=>0,'is_donation'=>0,'sale_status'=>10])->count();
            $soldNumber = $model->where(['member_id'=>$this->auth->member_id,'goods_status'=>3,'is_synthesis'=>0,'is_donation'=>0])->count();
        }else{
            $model = new MemberBox();
            $on_saleNumber = $model->where(['box_status'=>20,'is_open'=>10,'member_id'=>$this->auth->member_id])->count();
            $soldNumber = $model->where(['box_status'=>40,'is_open'=>10,'member_id'=>$this->auth->member_id])->count();
        }
        $this->success("获取信息成功",compact('on_saleNumber','soldNumber'));
    }
}