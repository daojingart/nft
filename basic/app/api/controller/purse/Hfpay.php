<?php

namespace app\api\controller\purse;

use adapay\Acctmgr;
use adapay\Card;
use adapay\Pay;
use app\common\controller\Controller;
use app\common\model\CardCategory;
use app\common\model\MemberReal;
use app\common\model\PaySetting;

/**
 * 我的钱包汇付支付
 */
class Hfpay extends Controller
{
    protected $Payment;

    protected $BankCard;

    protected $payment;

    public function __construct()
    {
        $lianlianPay = PaySetting::getItem('hfPay');
        $this->Payment = new Acctmgr($lianlianPay);
        $this->BankCard = new Card($lianlianPay);
        $this->payment = new Pay($lianlianPay);
        parent::__construct();
    }

    /**
     * 开通账户
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute (/api/purse.Hfpay/openAccount)
     */
    public function openAccount()
    {
        $member_real_info = (new MemberReal())->getDetails(['member_id' => $this->auth->member_id]);
        if (empty($member_real_info)) {
            $this->error("请先实名认证");
        }
        if (!$this->Payment->createRealName($member_real_info)) {
            $this->error("开通账户失败");
        }
        $this->success("开通账户成功;可以进入钱包");
    }

    /**
     * 获取银行卡列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hfpay/getBankCardList)
     * @ApiReturnParams   (name="linked_brbankname", type="string", description="银行卡名称")
     * @ApiReturnParams   (name="linked_acctno", type="string", description="银行卡号")
     * @ApiReturnParams   (name="linked_bankcode", type="string", description="银行卡编码(支付要用)")
     * @ApiReturnParams   (name="color", type="string", description="银行卡颜色")
     * @ApiReturnParams   (name="thumb", type="string", description="银行卡图标")
     */
    public function getBankCardList()
    {
        $list = $this->BankCard->getBankList($this->auth->member_id);
        if(empty($list)){
            $this->success("获取成功", []);
        }
        $new_list = [];
        foreach ($list as $key=>$value){
            $CardCategory = (new CardCategory())->where(['title'=>$value['linked_brbankname']])->find();
            $new_list[$key]['linked_brbankname'] = $value['bank_name'];
            $new_list[$key]['linked_acctno'] = $value['card_id'];
            $new_list[$key]['linked_bankcode'] = $value['token_no'];
            $new_list[$key]['color'] = empty($CardCategory)?"#486DEF":$CardCategory['color'];
            $new_list[$key]['thumb'] = $CardCategory['thumb'];

        }
        $this->success("获取成功", $new_list);
    }

    /**
     * 添加银行卡
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hfpay/addBankCard)
     * @ApiParams   (name="card_no", type="string", required=true, description="银行卡账号")
     * @ApiParams   (name="phone", type="string", required=true, description="银行预留手机号")
     * @ApiReturnParams   (name="data", type="string", description="Adapay生成的快捷绑卡申请id")
     */
    public function addBankCard()
    {
        $card_no = $this->request->post('card_no');
        $phone = $this->request->post('phone');
        if (!$card_no || !$phone) {
            $this->error("参数错误");
        }
        if (!$id = $this->BankCard->bindCard(['member_id' => $this->auth->member_id, 'card_no' => $card_no, 'phone' => $phone])) {
            $this->error("绑定银行卡失败");
        }
        $this->success("绑定银行卡成功",$id);
    }

    /**
     * 绑卡确认
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hfpay/confirmBindCard)
     * @ApiParams   (name="sms_code", type="string", required=true, description="短信验证码")
     * @ApiParams   (name="id", type="string", required=true, description="Adapay生成的快捷绑卡申请id")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function confirmBindCard()
    {
        $id = $this->request->post('id');
        $sms_code = $this->request->post('sms_code');
        if(!$id || !$sms_code){
            $this->error("参数错误");
        }
        if($this->BankCard->confirmBindCard($id,$sms_code)){
            $this->success("绑定银行卡成功");
        }
        $this->error("绑定银行卡失败");
    }

    /**
     * 解除绑定银行卡
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hfpay/unbankCard)
     * @ApiParams   (name="token", type="string", required=true, description="用户名")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function unbankCard()
    {
        $token = $this->request->post('token');
        if(!$token){
            $this->error("参数错误");
        }
        if($this->BankCard->unbindCard($this->auth->member_id,$token)){
            $this->success("解绑银行卡成功");
        }
        $this->error("解绑银行卡失败");
    }

    /**
     * 确认支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hfpay/confirmPay)
     * @ApiParams   (name="sms_code", type="string", required=true, description="支付的短信验证码")
     * @ApiParams   (name="payment_id", type="string", required=true, description="发起支付返回的ID")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function confirmPay()
    {
        $sms_code = $this->request->post('sms_code');
        $payment_id = $this->request->post('payment_id');
        if(!$sms_code || !$payment_id){
            $this->error("参数错误");
        }
        if($this->payment->confirmPayment($payment_id, $sms_code)){
            $this->success("支付成功");
        }
        $this->error("支付失败");
    }

    /**
     * 支付确认验证码重发
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hfpay/smsCodeSend)
     * @ApiParams   (name="payment_id", type="string", required=true, description="发起支付返回的ID")
     */
    public function smsCodeSend()
    {
        $payment_id = $this->request->post('payment_id');
        if(!$payment_id){
            $this->error("参数错误");
        }
        if($this->payment->smsCodeSend($payment_id)){
            $this->success("发送成功");
        }
        $this->error("发送失败");
    }

    /**
     * 测试支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hfpay/testPay)
     * @ApiParams   (name="order_sn", type="string", required=true, description="订单编号")
     * @ApiParams   (name="total_fee", type="string", required=true, description="订单价格")
     * @ApiParams   (name="goods_name", type="string", required=true, description="产品名称")
     * @ApiParams   (name="token_no", type="string", required=true, description="银行卡编号")
     */
    public function testPay()
    {
        $order_sn = $this->request->post('order_sn');
        $total_fee = $this->request->post('total_fee');
        $goods_name = $this->request->post('goods_name');
        $token_no = $this->request->post('token_no');
        $result = (new \app\common\library\HfPay())->QuickPayment($order_sn, $total_fee, $goods_name, $token_no);
        pre($result);
    }



}