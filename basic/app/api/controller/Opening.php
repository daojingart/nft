<?php

namespace app\api\controller;


use app\common\model\Member;
use app\common\model\MemberChain;
use app\common\model\MemberGoods;
use app\common\model\Notice;
use app\common\model\Order;

/**
 * 开放API
 */
class Opening
{
	protected $noNeedLogin = [
		'*'
	];

	/**
	 * 获取藏品列表
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/opening/getCollectionList)
	 * @ApiReturnParams   (name="collect_id", type="string", description="藏品id")
	 * @ApiReturnParams   (name="albumDetailName", type="string", description="藏品名称")
	 * @ApiReturnParams   (name="thumbPic", type="string", description="藏品封面")
	 * @ApiReturnParams   (name="quantity", type="string", description="藏品发售量")
	 * @ApiReturnParams   (name="circulateQuantity", type="string", description="藏品流通量")
	 */
	public function getCollectionList()
	{
		$memberGoodsModel = new MemberGoods();
		$goodsList = $memberGoodsModel
			->alias("a")
			->join("goods b","a.goods_id=b.goods_id")
			->where(['a.goods_status'=>['in',['0','1','2']],'a.cast_status' => 2,'a.is_synthesis' => 0,'a.is_donation' => 0,'a.sale_status' => 10,'b.is_open_consignment'=>['in',[10,20]]])
			->group("a.goods_id")
			->field("a.goods_id as collect_id,a.goods_name as albumDetailName,a.goods_thumb as thumbPic,
			b.original_number as quantity")
			->select();
		foreach ($goodsList as $key => $value) {
			$value['circulateQuantity'] = $memberGoodsModel->circulate($value['collect_id']);
		}
		return json($goodsList);
	}

	/**
	 * 获取寄售藏品列表
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/opening/getCllectList)
	 * @ApiParams   (name="collect_id", type="string", required=true, description="藏品的ID")
	 * @ApiReturnParams   (name="item_id", type="string", description="藏品ID")
	 * @ApiReturnParams   (name="serial_no", type="string", description="藏品编号")
	 * @ApiReturnParams   (name="item_name", type="string", description="藏品名称")
	 * @ApiReturnParams   (name="price", type="string", description="挂售价格")
	 * @ApiReturnParams   (name="sale_time", type="string", description="寄售时间 毫秒")
	 * @ApiReturnParams   (name="sale_status", type="string", description="支付状态ON_LOCK【锁定中】ON_SALE【在售】")
	 */
	public function getCllectList($collect_id)
	{
		if (!$collect_id) {
			die("藏品参数错误");
		}
		$memberGoodsModel = new MemberGoods();
		$memberGoodsList = $memberGoodsModel->where(['goods_id'=>$collect_id,'goods_status'=>['in',[1,2]],'is_synthesis'=>0,'is_donation'=>0])
			->order("sale_price asc")
			->field("id as item_id,collection_number as serial_no,goods_name as item_name,sale_price as price,list_time as sale_time,goods_status as sale_status")
			->limit(0,50)
			->select();
		foreach ($memberGoodsList as $key=>$value) {
			$value['sale_status'] = $value['sale_status'] == 1 ? 'ON_SALE' : 'ON_LOCK';
		}
		return json($memberGoodsList);
	}


	/**
	 * 平台公告
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/opening/getPlatformAnnouncements)
	 * @ApiParams   (name="pageSize", type="string", required=true, description="每页显示条数")
	 * @ApiParams   (name="pageNum", type="string", required=true, description="分页")
	 * @ApiReturnParams   (name="aid", type="string", description="公告ID")
	 * @ApiReturnParams   (name="title", type="string", description="公告标题")
	 * @ApiReturnParams   (name="link", type="string", description="跳转链接")
	 * @ApiReturnParams   (name="sendtime", type="string", description="公告发送时间")
	 * @ApiReturnParams   (name="collection", type="string", description="公告富文本")
	 */
	public function getPlatformAnnouncements($pageSize=10,$pageNum=1)
	{
		$noticeList = (new Notice())
			->where(['type'=>2,'disabled'=>'10'])
			->field("id as aid,title,create_time as sendtime,content as collection")
			->select();
		foreach ($noticeList as $key => $value) {
			$value['sendtime'] = date("Y-m-d H:i:s",$value['sendtime']);
			$value['collection'] = htmlspecialchars_decode($value['collection']);
			$value['link'] = HOST."/h5/h5.html#/pagesA/index/realDet?id=".$value['aid'];
		}
		return json($value);
	}

	/**
	 * 平台藏品交易记录
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/opening/getGoodsOrderList)
	 * @ApiReturnParams   (name="aid", type="string", description="公告ID")
	 * @ApiReturnParams   (name="title", type="string", description="公告标题")
	 * @ApiReturnParams   (name="link", type="string", description="跳转链接")
	 * @ApiReturnParams   (name="sendtime", type="string", description="公告发送时间")
	 * @ApiReturnParams   (name="collection", type="string", description="公告富文本")
	 */
	public function getGoodsOrderList()
	{
		$order_list = (new Order())
			->where(['order_type'=>3,'pay_status'=>2])
			->order("pay_time desc")
			->limit(0,50)
			->field("order_id,sale_member_id,sale_goods_id,member_id,pay_time,pay_price")
			->select();
		$new_list = [];
		foreach ($order_list as $key=> $value) {
			$member_goods_info = MemberGoods::detail($value['sale_goods_id']);
			$buyMemberChinaInfo = (new MemberChain())->where(['member_id'=>$member_goods_info['member_id']])->find();
			$member_info = (new Member())->where(['member_id'=>$value['member_id']])->find();
			$memberChinaInfo = (new MemberChain())->where(['member_id'=>$value['member_id']])->find();
			$new_list[$key]['collect_id'] = $member_goods_info['goods_id'];
			$new_list[$key]['item_id'] = $member_goods_info['id'];
			$new_list[$key]['log_id'] = $value['order_id'];
			$new_list[$key]['serial_no'] = $member_goods_info['collection_number'];
			$new_list[$key]['item_name'] = $member_goods_info['goods_name'];
			$new_list[$key]['buyUserName'] = $member_info['name'];
			$new_list[$key]['buyUserId'] = $member_info['member_id'];
			$new_list[$key]['buyUserWallet'] = $memberChinaInfo['w_account'];
			$new_list[$key]['saleUserName'] = $member_goods_info['nickname'];
			$new_list[$key]['saleUserId'] = $member_goods_info['member_id'];
			$new_list[$key]['saleUserWallet'] = $buyMemberChinaInfo['w_account'];
			$new_list[$key]['price'] = $value['pay_price'];
			$new_list[$key]['buyTime'] = $value['pay_time'];
		}
		return json($new_list);
	}

	/****MASCHI****/

	/**
	 * 行情实盘
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/opening/getCollectionListmaschi)
	 * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
	 */
	public function getCollectionListmaschi()
	{
		$memberGoodsModel = new MemberGoods();
		$goodsList = $memberGoodsModel
			->alias("a")
			->join("goods b","a.goods_id=b.goods_id")
			->where(['a.goods_status'=>['in',['0','1','2']],'a.cast_status' => 2,'a.is_synthesis' => 0,'a.is_donation' => 0,'a.sale_status' => 10,'b.is_open_consignment'=>['in',[10,20]]])
			->group("a.goods_id")
			->field("a.goods_id as id,a.goods_name as name,a.goods_thumb as image,b.original_number as saleNum,b.goods_price as publicPrice,b.start_time as publicTime")
			->select();
		foreach ($goodsList as $key => $value) {
			$goods_info = $memberGoodsModel->where(['goods_id'=>$value['id'],'goods_status'=>1])->order("sale_price asc")->find();
			$value['locknNum'] = $memberGoodsModel->where(['goods_id'=>$value['id'],'goods_status'=>2])->count();
			$value['price'] = $goods_info['sale_price'];
		}
		return json(['code'=>0,'data'=>$goodsList]);
	}

	/**
	 * MASCHI平台公告
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/opening/getPlatformAnnouncementsMaschi)
	 * @ApiParams   (name="pageSize", type="string", required=true, description="每页显示条数")
	 * @ApiParams   (name="pageNum", type="string", required=true, description="分页")
	 * @ApiReturnParams   (name="aid", type="string", description="公告ID")
	 * @ApiReturnParams   (name="title", type="string", description="公告标题")
	 * @ApiReturnParams   (name="link", type="string", description="跳转链接")
	 * @ApiReturnParams   (name="sendtime", type="string", description="公告发送时间")
	 * @ApiReturnParams   (name="collection", type="string", description="公告富文本")
	 */
	public function getPlatformAnnouncementsMaschi($pageSize=10,$pageNum=1)
	{
		$noticeList = (new Notice())
			->where(['type'=>2,'disabled'=>'10'])
			->order("id desc")
			->field("id,title,content as brief,image_url as image")
			->select();
		foreach ($noticeList as $key => $value) {
			$value['brief'] = htmlspecialchars_decode($value['brief']);
			$value['link'] = HOST."/h5/h5.html#/pagesA/index/realDet?id=".$value['id'];
		}
		return json(['code'=>0,'data'=>$noticeList]);
	}


	/**
	 * 获取用户持有的藏品
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/opening/getMyGoodsListMaschi)
	 * @ApiParams   (name="blockaddress", type="string", required=true, description="钱包地址")
	 */
	public function getMyGoodsListMaschi($blockaddress)
	{
		if(!$blockaddress){
			die('参数错误!');
		}
		$member_info = (new MemberChain())->where(['t_address'=>$blockaddress])->find();
		if(empty($member_info)){
			die('用户不存在!');
		}
		$memberGoodsModel = new MemberGoods();
		$memberGoodsList = $memberGoodsModel->where(['member_id'=>$member_info['member_id'],'goods_status'=>0,'is_synthesis'=>0,'is_donation'=>0,'cast_status'=>2])
			->order("id desc")
			->field("id,collection_number as num,goods_name as name,goods_thumb as image,create_time as time,order_id,goods_price as cost")
			->limit(0,50)
			->select();
		foreach ($memberGoodsList as $key=>$value ) {
			$order_info = (new Order())->where(['order_id'=>$value['order_id']])->find();
			if(!empty($order_info)){
				$value['cost'] = $order_info['total_price'];
			}
		}
		return json(['code'=>0,'data'=>$memberGoodsList]);
	}




}