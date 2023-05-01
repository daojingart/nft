<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 17:17
 */


namespace app\api\validate;

use app\api\model\order\Order;
use app\common\components\helpers\StockUtils;
use app\common\model\Appointment;
use app\common\model\GloryGoods;
use app\common\model\Goods;
use app\common\model\GoodsPrecedence;
use app\common\model\MemberBox;
use app\common\model\MemberGoods;

/**
 * 订单验证
 * Class OrderValidate
 * @package app\api\validate
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-27 17:19
 */
class OrderValidate extends BaseValidate
{
    protected $order_error = "";

    protected $rule = [
        'type' => 'require',
        'order_id'=> 'require',
    ];
    protected $message = [
        'type.require' => '类型不能为空',
        'order_id.require' => '订单号id不能为空'

    ];
    protected $scene = [
        'toObtainListOrder' => ['type'],
        'remindDelivery' => ['order_id'],
        'confirmGoods' => ['order_id']
    ];

    /**
     * 创建订单参数效验
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface validationCreateOrder
     * @Time: 2022/10/28   09:52
     */
    public function validationCreateOrder($param,$lockKey,$member_info)
    {
		$member_id = $member_info['member_id'];
        $ip = request()->ip();
        switch ($param['order_type'])
        {
            case "1": //藏品发售购买订单 可根据商品 ID进行效验
                $goods_info = Goods::detail($param['goods_id']);
                if (empty($goods_info) || $goods_info['is_del']==1) {
                    $this->order_error = "商品信息不存在无法购买";
                    return false;
                }
                if($goods_info['product_types']!=1){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "非法效验参数;IP 已被风控记录";
                    return false;
                }
                //如果藏品下架 或者 藏品 已被隐藏
                if($goods_info['goods_status']['value']==20){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "藏品已被下架无法购买";
                    return false;
                }
                if (!StockUtils::getStock($param['goods_id'])) {
                    $this->order_error = "库存不足,无法购买";
                    return false;
                }
                $start_time = buyFirstInfo($goods_info['goods_id'],$member_id, $goods_info['start_time']);
                if (time() < $start_time) {
                    $this->order_error = "未开始抢购,抢购未开始,请等待...";
                    return false;
                }
                return true;
            case "2": //盲盒订单
                $goods_info = Goods::detail($param['goods_id']);
                if (empty($goods_info) || $goods_info['is_del']==1) {
                    $this->order_error = "商品信息不存在无法购买";
                    return false;
                }
                if($goods_info['product_types']!=3){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "非法效验参数;IP 已被风控记录";
                    return false;
                }
                //如果藏品下架 或者 藏品 已被隐藏
                if($goods_info['goods_status']['value']==20){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "藏品已被下架无法购买！！！";
                    return false;
                }
                if (!StockUtils::getStock($param['goods_id'])) {
                    $this->order_error = "库存不足,无法购买";
                    return false;
                }
                $start_time = buyFirstInfo($goods_info['goods_id'], $member_id, $goods_info['start_time']);
                if (time() < $start_time) {
                    $this->order_error = "未开始抢购,抢购未开始,请等待...";
                    return false;
                }
                return true;
            case "3": //寄售藏品订单
                $member_goods_info = MemberGoods::detail($param['id']);
                if(empty($member_goods_info)){
                    $this->order_error = "藏品异常,无法下单购买；反馈客服错误代码--10001";
                    return false;
                }
				if($member_goods_info['goods_status']==2){
					$this->order_error = "藏品锁定中！暂时无法购买！";
					return false;
				}
                if($member_goods_info['goods_status']!=1){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "藏品类型有误;无法购买";
                    return false;
                }
                if($member_goods_info['member_id']==$member_id){
                    $this->order_error = "自己不能购买自己寄售的藏品";
                    return false;
                }
                if($member_goods_info['goods_status']==2){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "藏品交易中..";
                    return false;
                }
                if($member_goods_info['goods_id']!=$param['goods_id']){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "非法请求;IP 已被风控记录";
                    return false;
                }
                $order_details = (new Order())->where(['order_type'=>3,'sale_goods_id'=>$member_goods_info['id'],'pay_status'=>2])->find();
                if(!empty($order_details)){
                    (new MemberGoods())->where(['id' => $member_goods_info['id']])->update(['goods_status' => 3]);
                    $this->order_error = "已被提前抢购,请重新选择藏品";
                    return false;
                }
                $order_detailsInfo = (new Order())->where(['order_type'=>3,'sale_goods_id'=>$member_goods_info['id'],'order_status'=>1])->find();
                if(!empty($order_detailsInfo)){
                    (new MemberGoods())->where(['id' => $member_goods_info['id']])->update(['goods_status' => 2]);
                    $this->order_error = "藏品被锁定请购买其他的藏品";
                    return false;
                }
                return true;
            case "12": //预约购买
                $goods_info = Goods::detail($param['goods_id']);
                if (empty($goods_info) || $goods_info['is_del']==1) {
                    $this->order_error = "商品信息不存在无法购买";
                    return false;
                }
                if($goods_info['product_types']!=4){
                    write_log("非法记录--请求会员ID：$member_id --非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "非法效验参数;IP 已被风控记录";
                    return false;
                }
                //如果藏品下架 或者 藏品 已被隐藏
                if($goods_info['goods_status']['value']==20){
                    write_log("非法记录--请求会员ID：$member_id --非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "藏品已被下架无法购买!!!";
                    return false;
                }
                if (!StockUtils::getStock($param['goods_id'])) {
                    $this->order_error = "库存不足,无法购买";
                    return false;
                }
                $goodsPrecedence = (new GoodsPrecedence())->where(['goods_id'=>$goods_info['goods_id']])->find();
                if($goodsPrecedence['appointment_start_time']<time() && time()<=$goodsPrecedence['appointment_end_time']){
                    $this->order_error = "预约活动期间禁止下单支付";
                    return false;
                }
                //没有开始抢购前下单 判断
                if(time()>=$goodsPrecedence['draw_time'] && time()<=$goods_info['start_time']){
                    $appointment = (new Appointment())->where(['member_id'=>$member_id,'goods_id'=>$goods_info['goods_id'],'status'=>1])->find();
                    if(empty($appointment)){
                        $this->order_error = "非法下单支付";
                        return false;
                    }
                }
                return true;
            case "5": //荣誉值兑换藏品
                $redis = initRedis();
                if (!$redis->get(request()->ip() . 'honor_exchange')) {
                    $this->order_error = "人机验证失败!";
                    return false;
                }

                //查询这个藏品是否在荣誉值兑换里面
                $GloryGoods = (new GloryGoods())->where(['goods_id'=>$param['goods_id']])->find();
                if(empty($GloryGoods)){
                    $this->order_error = "兑换藏品不存在,无法进行兑换!";
                    return false;
                }

				//判断当前用户荣誉值是否充足 充足才可以兑换
				if ($GloryGoods['price'] > $member_info['glory']) {
					$this->order_error = "荣誉值不足,无法兑换!";
					return false;
				}

                //查询藏品是否存在
                $goods_info = Goods::detail($param['goods_id']);
                if (empty($goods_info) || $goods_info['is_del']==1) {
                    $this->order_error = "商品信息不存在无法购买";
                    return false;
                }
                //如果藏品下架 或者 藏品 已被隐藏
                if ($goods_info['start_time'] > time()) {
                    $this->order_error = "活动未开始,请到开始时间在进行兑换！";
                    return false;
                }
                if($goods_info['goods_status']['value']==20){
                    $this->order_error = "藏品已被下架无法购买!!!";
                    return false;
                }
                if (!StockUtils::getStock($param['goods_id'])) {
                    $this->order_error = "库存不足,无法购买!!!";
                    return false;
                }
                if($GloryGoods['type']==1 && !in_array($goods_info['product_types'], [1,5])){
                    $this->order_error = "请修改参数,类型错误";
                    return false;
                }
                if($GloryGoods['type']==2 && $goods_info['product_types']!=3){
                    $this->order_error = "请修改参数,类型错误";
                    return false;
                }
                $order_type = 5;
                if ($GloryGoods['type'] == 2) {
                    $order_type = 9;
                }
                //判断兑换是否有门槛限制 有门槛则判断拦截
                if($GloryGoods['hold_goods_status'] == 20){
                    $member_goods_count = (new MemberGoods())->where(['member_id'=>$member_id,'goods_status'=>0,'is_donation'=>0,'goods_id'=>$GloryGoods['hold_goods_id']])->count();
                    if($member_goods_count==0){
                        $this->order_error = "您无法兑换该藏品,请先获取兑换权益！";
                        return false;
                    }
                }
                $count_number = (new \app\common\model\Order())->alias('o')
                    ->whereTime("pay_time", "m")
                    ->join('order_goods og', 'o.order_id = og.order_id', 'left')
                    ->where(['og.goods_id' => $goods_info['goods_id'], 'o.member_id' => $member_id, 'order_status' => ['<>', 5], 'o.order_type' => $order_type])
                    ->count();
                if ($count_number >= $GloryGoods['limit']) {
                    $this->order_error = "限制兑换";
                    return false;
                }
                return true;
            case "11": //寄售盲盒订单
                $member_box_info = (new MemberBox())->details(['id'=>$param['id']]);
                if(empty($member_box_info)){
                    $this->order_error = "盲盒异常,无法下单购买；反馈客服错误代码--10001";
                    return false;
                }
                if($member_box_info['box_status']!=20){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "藏品类型有误;无法购买";
                    return false;
                }
                if($member_box_info['member_id']==$member_id){
                    $this->order_error = "自己不能购买自己寄售的盲盒";
                    return false;
                }
                if($member_box_info['box_status']==30){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "交易中请选择其他的盲盒下单..";
                    return false;
                }
                if($member_box_info['goods_id']!=$param['goods_id']){
                    write_log("非法记录--请求会员ID：$member_id--非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                    $this->order_error = "非法请求;IP 已被风控记录";
                    return false;
                }
                $order_details = (new Order())->where(['order_type'=>11,'sale_goods_id'=>$member_box_info['id'],'pay_status'=>2])->find();
                if(!empty($order_details)){
                    (new MemberGoods())->where(['id' => $member_box_info['id']])->update(['box_status' => 40]);
                    $this->order_error = "已被提前抢购,请重新选择藏品";
                    return false;
                }
                return true;
            default:
                write_log("非法记录--请求会员ID：$member_id --非法请求参数--{$param['order_type']}---{$param['goods_id']}--请求IP--{$ip}--用户请求User Agent--{$_SERVER['HTTP_USER_AGENT']}", RUNTIME_PATH . 'disable');
                $this->order_error = "非法参数,IP已被收录";
                return false;
        }
    }

    /**
     * 支付方式
     * @ApiAuthor [Mr.Zhang]
     */
    public function paymentChannels($pay_type)
    {
        if(!$pay_type){
            return false;
        }
    }

    public function getError()
    {
        return $this->order_error;
    }
}