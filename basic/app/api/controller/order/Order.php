<?php

namespace app\api\controller\order;

use app\admin\model\Setting;
use app\api\model\order\GoodsOrder;
use app\api\validate\OrderValidate;
use app\common\components\helpers\RedisUtils;
use app\common\controller\Controller;
use app\common\controller\Pay;
use app\common\library\AliPay;
use app\common\library\BalancePay;
use app\common\library\BalancePay as BalancePayment;
use app\common\library\HfPay;
use app\common\library\HftPay;
use app\common\library\HyPay;
use app\common\library\IntegralPay;
use app\common\library\LianPay;
use app\common\library\SdPay;
use app\common\library\WxPay;
use app\common\model\GloryGoods;
use app\common\model\Goods;
use app\common\model\Member;
use app\common\model\MemberBox;
use app\common\model\MemberGoods;
use app\common\model\PaySetting;
use think\exception\DbException;
use app\api\model\order\Order as OrderModel;

/**
 * 支付订单
 */
class Order extends Controller
{

    /**
     * 创建订单
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/order.Order/createOrder)
     * @ApiParams   (name="goods_id", type="string", required=true, description="产品ID")
     * @ApiParams   (name="order_type", type="string", required=true, description="1 藏品订单  2盲盒订单  3二级藏品订单 4回收订单 5荣誉值兑换藏品   6使用空投卷订单  7盲盒空投订单    9荣誉值兑换盲盒  10合成盲盒订单  11 二级盲盒寄售订单  12 申购订单")
     * @ApiParams   (name="id", type="string", required=true, description="二级市场的产品ID")
     * @ApiReturnParams   (name="order_id", type="string", description="订单ID")
     * @ApiReturnParams   (name="order_sn", type="string", description="订单编号")
     * @ApiReturnParams   (name="code", type="string", description="code ==-10 引导实名认证")
     * @throws DbException
     */
    public function createOrder()
    {
        $goods_id = $this->request->post('goods_id');
        $order_type = $this->request->post('order_type');
        $id = $this->request->post('id');
        if(!$goods_id || !$order_type){
            $this->error('参数错误');
        }
        if($this->auth->real_status['value'] != 2){
            $this->error('请先实名认证',"","-10");
        }
        if ($order_type == 3 || $order_type == 11) {
            if(!$id){
                $this->error('参数错误');
            }
            $lockKey = "createOrder:$order_type:$goods_id:".$id;
        } else {
            $lockKey = "createOrder:$order_type:$goods_id";
        }
        if (RedisUtils::lock($lockKey, 60)) {
            $this->error('商品购买火爆,请重试');
        }
        $orderValidate = new OrderValidate();
        if(!$orderValidate->validationCreateOrder($this->request->post(),$lockKey,$this->auth->getUser())){
            RedisUtils::unlock($lockKey);
            $this->error($orderValidate->getError());
        }
        $model = new OrderModel();
        if ($order_type != 5) { //藏品兑换
			$member_lock_key = "member_lock_member_id:".$this->auth->member_id;
			$order_values = Setting::getItem("order");
			$order_lock_number = isset($order_values['order_lock_number'])? (int)$order_values['order_lock_number'] :3;
			$order_lock_time = $order_values['order_lock_time'] ?? 2;
			if($this->redis->get($member_lock_key)>= $order_lock_number){
				$this->error("1个小时内连续{$order_lock_number}次取消订单，限制交易{$order_lock_time}小时");
			}
			//判断是否开启可以连续下单开启后则连续下单 否则只能下单一次
			if(isset($order_values['create_order_open']) && $order_values['create_order_open']==20){
				$order_count = OrderModel::where('member_id',$this->auth->member_id)->where('order_status',1)->where('pay_status',1)->count();
				if($order_count>0){
					$this->error('您有未支付订单，请先支付后购买!');
				}
			}
            $orderInfo = $model->where('member_id', $this->member_info['member_id'])
                ->where(['pay_status' => 1, 'order_status' => 1, 'order_type' => $order_type])
                ->find();
            if (!empty($orderInfo)) {
                RedisUtils::unlock($lockKey);
                $this->error('您有未支付订单，请先支付后购买!');
            }
        }
        try {
            $res = $model->add($this->auth->getUser(), $this->request->post());
        } catch (\Exception $e) {
            RedisUtils::unlock($lockKey);
            $this->error($e->getMessage());
        }
        RedisUtils::unlock($lockKey);
        if (($res ?? false)) {
            $this->success("获取成功",[
                'order_id' => $model['order_id'],
                'order_sn' => $model['order_no'],
            ]);
        }
        $this->error($model->getError() ?? '下单失败');
    }

    /**
     * 获取收银台信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/order.Order/getPaySetting)
     * @ApiParams   (name="order_id", type="string", required=true, description="订单ID")
     * @ApiReturnParams   (name="returnArray", type="string", description="订单信息")
     * @ApiReturnParams   (name="paySettingList", type="string", description="支付信息")
     */
    public function getPaySetting()
    {
        $order = Setting::getItem('order');
        $param = $this->request->post();
        if (!isset($param['order_id']) || empty($param['order_id'])) {
            $this->error('订单信息异常');
        }
        $model = new OrderModel();
        $orderInfo = $model::detail($param['order_id']);
        $new_time = time();
        $create_str_time = strtotime($orderInfo['create_time']);
        $returnArray['order_price'] = $orderInfo['pay_price'];
        $returnArray['new_time'] = $new_time;
        $returnArray['wait_pay_time'] = $create_str_time + 60 * $order['pay_time'];
        //获取收银台信息
        $paySettingList = (new PaySetting())->getPayGetWay(2);
        $this->success('获取成功',compact('returnArray','paySettingList'));
    }


    /**
     * 收银台支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/order.order/toPay)
     * @ApiParams   (name="order_id", type="string", required=true, description="订单ID")
     * @ApiParams   (name="pay_type", type="string", required=true, description="1=公众号、2=微信AP、3=微信H5,4=支付宝H5,8=支付宝APP，5=余额支付、6=荣誉值、9=连连余额、13=连连银行卡、10=杉德快捷、16=杉德钱包、11=汇付天下、12=汇元钱包、14=汇元快捷、15=汇付通钱包")
     * @ApiParams   (name="card_no", type="string", required=true, description="连连快捷支付 银行卡的支付标识")
     * @ApiParams   (name="pwd", type="string", required=true, description="支付密码(连连余额、连连快捷、系统钱包)")
     * @ApiParams   (name="random_key", type="string", required=true, description="连连支付随机数")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function toPay()
    {
        $order_id = $this->request->post('order_id');
        $pay_type = $this->request->post('pay_type');
        $card_no = $this->request->post('card_no');
        $pwd = $this->request->post('pwd');
        $random_key = $this->request->post('random_key');
        if(!$order_id || !$pay_type){
            $this->error('参数错误');
        }
        $model = OrderModel::detail($order_id);
        if ($model['order_status']['value'] != 1) {
            $this->error("订单错误,无法支付请联系客服!！");
        }
        if($model['member_id'] != $this->auth->member_id){
            $this->error("订单错误,无法支付请联系客服!！");
        }
        $goodsInfo = (new Goods())::detail($model['goods'][0]['goods_id']);
        if(!in_array($model['order_type'],[3,6,11])){
            if (empty($goodsInfo) || $goodsInfo['is_del']==1 || $goodsInfo['goods_status']['value']==20) {
                $this->error("无法支付;藏品不存在或者已下架");
            }
        }
        if ($model['order_type'] == 5) {
            $res       = (new GloryGoods())->where(['goods_id' => $goodsInfo['goods_id']])->find();
            $order_key = "order_score_id_" . $goodsInfo['goods_id'] . "_member_id_" . $model['member_id'];
            if ($this->redis->get($order_key) >= $res['limit']) {
                 $this->error("下单失败！请您稍后再试");
            }
            if ($pay_type != 6) {
                $this->error("非法请求,支付错误！");
            }
            $this->redis->incr($order_key);
            $this->redis->expire($order_key, 3600);
        }
        if ($model['order_type'] != 5 && $model['order_type'] != 9) {
            if ($pay_type == 6) {
                $this->error("非法请求,支付错误！");
            }
        }
        //如果是寄售订单 卖家如果未开通连连 则不能直接走分账  判断 如果是汇元支付  没有开户则不能支付 网关账户拦截
        if ($model['order_type'] == 3 || $model['order_type']==11) {
            if ($model['order_type']==3){
                $memberGoods = (new MemberGoods())::detail($model['sale_goods_id']);
                if ($memberGoods['goods_status'] != 2) {
                    $this->error("非法请求,支付错误！");
                }
            }else{
                //是否关联盲盒 存在问题❓❓❓❓❓
                $order_box = (new MemberBox())->details(['id'=>$model['sale_goods_id']]);
                if ($order_box['box_status'] == 10 || $order_box['box_status'] == 40) {
                    $this->error("非法请求,支付错误！");
                }
            }
            //判断这个藏品是否被卖过 卖过则不能再次卖
            $order_info = (new OrderModel())->where(['sale_goods_id' => $model['sale_goods_id'], 'pay_status' => 2,'order_type'=>$model['order_type']])->find();
            if (!empty($order_info)) {
                 $this->error("支付错误，请联系客户提供订单号!");
            }
            switch ($pay_type) {
                case "3":
                    //查询卖家是否开户
                    if (!(new LianPay())->getIsOpenStatus($model['sale_member_id'])) {
                        $this->error("请选择其他支付方式,卖家不支持此付款方式!");
                    }
                    break;
                case "9":
                    if (!(new LianPay())->getIsOpenStatus($model['sale_member_id'])) {
                        $this->error("请选择其他支付方式,卖家不支持此付款方式!");
                    }
                    break;
                case "12":
                    if (!(new HyPay())->getUserInfo($model['sale_member_id'])) {
                         $this->error("请选择其他支付方式,卖家不支持此付款方式!");
                    }
                    break;

            }
        }
        //判断是否发起的是杉德支付 是的话判断银行卡参数
        if (in_array($pay_type, [11,13,14])) {
            if (!$card_no) {
                $this->error("参数错误请--请联系客服！");
            }
        }
        //判断是否是发起的汇元支付  如果是 则判断开户
        if ($pay_type == 12) {
            if (!(new HyPay())->getUserInfo($model['member_id'])) {
                $this->error("请选择其他支付方式,卖家不支持此付款方式!");
            }
        }
        if ($pay_type == 9 || $pay_type==13) {
            if (!(new LianPay())->getIsOpenStatus($model['member_id'])) {
                $this->error("请选择其他支付方式,卖家不支持此付款方式!");
            }
        }
        if ($pay_type == 12) {
            if (!(new HyPay())->getUserInfo($model['member_id'])) {
                $this->error("请选择其他支付方式,卖家不支持此付款方式!");
            }
        }
        //修改订单的支付状态
        if($pay_type!=15){
            (new OrderModel())->where(['order_id' => $model['order_id']])->update(['pay_type' => $pay_type]);
        }
        $wxPay = new WxPay();
        $aliPay = new AliPay();
        $balancePay = new BalancePay();
        $integralPay = new IntegralPay();
        $lianlianPay = new LianPay();
        $sdPay = new SdPay();
        $hfPay = new HfPay();
        $hyPay = new HyPay();
        $hytPay = new HftPay();
        switch ($pay_type) {
            case "1": //公众号支付
                $result = $wxPay->weChatPay($model['order_no'],$model['goods'][0]['goods_name'],$model['pay_price'],$this->auth->open_id);
                if($result){
                    $this->success("获取支付信息",$result);
                }
                $this->error($wxPay->getErrorMessage());
                break;
            case "3": //微信APP支付
                $resultApp = $wxPay->weAppPay($model['order_no'],$model['goods'][0]['goods_name'],$model['pay_price'],$this->auth->open_id);
                if($resultApp){
                    $this->success("获取支付信息",$resultApp);
                }
                $this->error($wxPay->getErrorMessage());
                break;
            case "7": //微信H5支付
                $resultH5 = $wxPay->weH5Pay($model['order_no'],$model['goods'][0]['goods_name'],$model['pay_price']);
                if($resultH5){
                    $this->success("获取支付信息",$resultH5);
                }
                $this->error($wxPay->getErrorMessage());
                break;
            case "4": //支付宝H5支付
                $result = $aliPay->aliPayH5($model['order_no'], $model['pay_price'],$model['goods'][0]['goods_name']);
                if($result){
                    $this->success("获取支付信息",$result);
                }
                $this->error($wxPay->getErrorMessage());
                break;
            case "8": //支付宝APP
                $result = $aliPay->aliPayApp($model['order_no'], $model['pay_price'],$model['goods'][0]['goods_name']);
                if($result){
                    $this->success("获取支付信息",$result);
                }
                $this->error($wxPay->getErrorMessage());
                break;
            case "5": //余额支付
                if (!$this->auth->checkOperationPwd($this->auth->member_id, $pwd)) {
                    $this->error('操作密码不正确,请重新输入');
                }
                $result = $balancePay->balancePayment($model,$this->auth->member_id,$this->auth->account);
                if($result){
                    $this->success("支付成功",$result);
                }
                $this->error($balancePay->getError());
                break;
            case "6": //荣誉值支付
                $result = $integralPay->integralPay($model,$this->auth->member_id,$this->auth->glory);
                if($result){
                    $this->success("支付成功",$result);
                }
                $this->error($integralPay->getError());
                break;
            case "9": //连连余额支付
                $order_sn = $lianlianPay->createRadecreate("GENERAL_CONSUME",$model['order_id'],$this->auth->member_id);
                if(!$order_sn){
                    $this->error("订单创建失败,请联系客服!");
                }
                //发起支付
                $result = $lianlianPay->balancePay($order_sn,$pwd,$random_key,$this->auth->member_id,$model['pay_price'],$model['goods'][0]['goods_name']);
                if($result){
                    $this->success("支付成功",$result);
                }
                break;
            case "13": //连连银行卡支付
                $order_sn = $lianlianPay->createRadecreate("GENERAL_CONSUME",$model['order_id'],$this->auth->member_id);
                if(!$order_sn){
                    $this->error("订单创建失败,请联系客服!");
                }
                //发起支付
                $result = $lianlianPay->bankcardPay($order_sn,$pwd,$random_key,$this->auth->member_id,$model['pay_price'],$model['goods'][0]['goods_name'],$card_no);
                if($result){
                    $this->success("支付成功",$result);
                }
                break;
            case "10": //杉德快捷支付
                $result = $sdPay->QuickPayment($model['order_no'], $model['pay_price'], $model['goods'][0]['goods_name'], $this->auth->member_id);
                $this->success("获取支付信息",$result);
               break;
            case "16": //杉德钱包
                $result = $sdPay->cloudPayment($model['order_no'], $model['pay_price'], $model['goods'][0]['goods_name'], $this->auth->member_id);
                $this->success("获取支付信息",$result);
               break;
            case "11": //汇付天下[adaPay]
                $result = $hfPay->QuickPayment($model['order_no'], $model['pay_price'], $model['goods'][0]['goods_name'], $card_no);
                $this->success("获取支付信息",$result);
                break;
            case "12": //汇元支付钱包支付
                $result = $hyPay->QuickPayment($model['order_no'], $model['pay_price'], $model['goods'][0]['goods_name'], "", 2,$this->getMemberInfo()['user_uid']);
                if($result){
                    $this->success("获取支付信息",$result);
                }
                $this->error($hyPay->getError());
                break;
            case "14": //汇元支付快捷支付
                $result = $hyPay->QuickPayment($model['order_no'], $model['pay_price'], $model['goods'][0]['goods_name'], $card_no, 63,$this->getMemberInfo()['user_uid']);
                if($result){
                    $this->success("获取支付信息",$result);
                }
                $this->error($hyPay->getError());
                break;
            case "15": //汇付通钱包
                $result = $hytPay->PayPayment($this->auth->member_id,$model['order_no'],$model['pay_price'],$model['goods'][0]['goods_name'],$model['order_type'],$this->auth->user_cust_id);
                if($result){
                    $this->success("获取支付信息",$result);
                }
                $this->error($hytPay->getError());
                break;
        }
    }

    /**
     * 官方回收订单提交
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/order.order/toRecovery)
     * @ApiParams   (name="goods_id", type="string", required=true, description="商品id")
     */
    public function toRecovery()
    {
        $goods_id = $this->request->post('goods_id');
        if(!$goods_id){
            $this->error('参数错误');
        }
        $model = new MemberGoods();
        $detail = ($model)->where([
            'goods_id' => $goods_id,
            'goods_status' => 0,
            'cast_status' => 2,
            'is_synthesis' => 0,
            'is_donation' => 0,
            'member_id' => $this->auth->member_id
        ])->find();
        if(!$detail){
            $this->error('无此藏品，不能出售');
        }
        if($detail['goods_status'] == 4){
            $this->error('此藏品已回收');
        }
        if($model->toRecovery($detail,$this->auth->member_id)){
            $this->success('出售成功');
        }
        $this->error($model->getError());
    }


    /**
     * 获取实物收银台信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (GET)
     * @ApiRoute  (/api/order.order/buyGoodsNow)
     * @ApiParams   (name="goods_id", type="string", required=true, description="商城ID")
     * @ApiParams   (name="goods_sku_id", type="string", required=true, description="商城SKUID")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function buyGoodsNow()
    {
        $goods_id = $this->request->param('goods_id');
        $goods_sku_id = $this->request->param('goods_sku_id');
        if(!$goods_id){
            $this->error("参数错误");
        }
        // 商品结算信息
        $model = new GoodsOrder();
        $order = $model->getGoodsBuyNow($this->auth->getUser(), $goods_id, 1, $goods_sku_id);
        if (!$this->request->isPost()) {
            $this->success("获取信息成功",$order);
        }
        if ($model->hasError()) {
            $this->error($model->getError());
        }
    }


    /**
     * 收银台发起支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/order.order/buyGoodsNowPay)
     * @ApiParams   (name="goods_id", type="string", required=true, description="商城ID")
     * @ApiParams   (name="goods_sku_id", type="string", required=true, description="商城SKUID")
     * @ApiParams   (name="pay_type", type="string", required=true, description="1=公众号、3=微信AP 7=微信H5,4=支付宝H5,8=支付宝APP，5=余额支付")
     * @ApiParams   (name="pwd", type="string", required=true, description="余额支付输入支付密码")
     */
    public function buyGoodsNowPay()
    {
        $goods_id = $this->request->param('goods_id');
        $goods_sku_id = $this->request->param('goods_sku_id');
        $pwd = $this->request->param('pwd');
        if(!$goods_id){
            $this->error("参数错误");
        }
        // 商品结算信息
        $model = new GoodsOrder();
        $order = $model->getGoodsBuyNow($this->auth->getUser(), $goods_id, 1, $goods_sku_id);
        if (!$this->request->isPost()) {
            $this->success("获取信息成功",$order);
        }
        if ($model->hasError()) {
            $this->error($model->getError());
        }
        $param = $this->request->post();
        if(!isset($param['pay_type'])){
            $error = "缺少支付方式的参数,无法发起支付";
            $this->error($error);
        }
        $param['order_type'] = 2;
        //根据不同的支付规则返回不同支付方式的参数
        $balancePay = new BalancePay();
        if ($model->addGoods($this->auth->getUser(), $order,$param)) {
            switch ($param['pay_type']){
                case "1": //公众号支付
                    $this->success("公众号支付",[
                        'payment' => (new Pay())->wxPay($model['order_no'], $this->auth->open_id
                            , $model['pay_price'],$order['goods_list'][0]['product_name']),
                        'order_id' => $model['order_id']
                    ]);
                    break;
                case "3": //微信APP支付
                    $this->success("APP支付",[
                        'payment' => (new Pay())->appPay($model['order_no'],$model['pay_price'],$order['goods_list'][0]['product_name']),
                        'order_id' => $model['order_id']
                    ]);
                    break;
                case "7": //微信H5支付
                    $this->success("维信H5支付",[
                        'payment' => (new Pay())->wxH5Pay($model['order_no'],$model['pay_price'],$order['goods_list'][0]['product_name']),
                        'order_id' => $model['order_id']
                    ]);
                    break;
                case "4": //支付宝H5支付
                    $this->success("支付宝H5",[
                        'payment' => (new Pay())->aliPay($model['order_no'],$model['pay_price'],$order['goods_list'][0]['product_name']),
                        'order_id' => $model['order_id']
                    ]);
                    break;
                case "8": //支付宝APP
                    $this->success("支付宝APP",[
                        'payment' => (new Pay())->aliAppPay($model['order_no'],$model['pay_price'],$order['goods_list'][0]['product_name']),
                        'order_id' => $model['order_id']
                    ]);
                    break;
                case "5": //余额支付
                    if(!$pwd){
                        $this->error("操作密码错误");
                    }
                    if (!$this->auth->checkOperationPwd($this->auth->member_id, $pwd)) {
                        $this->error('操作密码不正确,请重新输入');
                    }
                    $result = $balancePay->balancePayment($model,$this->auth->member_id,$this->auth->account);
                    if($result){
                        $this->success("支付成功",$result);
                    }
                    $this->error($balancePay->getError());
                    break;
            }
        }
        $error = $model->getError() ?: '订单创建失败';
        $this->error($error);
    }

}