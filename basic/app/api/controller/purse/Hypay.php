<?php

namespace app\api\controller\purse;

use app\common\controller\Controller;
use app\common\model\CardCategory;
use app\common\model\MemberCard;
use app\common\model\MemberReal;
use app\common\model\PaySetting;
use hy\Bank;
use hy\Pay;
use hy\Wallet;

/**
 * 我的钱包汇元支付
 */
class Hypay extends Controller
{
    protected  $bank;

    protected  $pay;

    protected  $wallet;


    public function __construct()
    {
        $hyPay = PaySetting::getItem('hyPay');
        $this->bank = new Bank($hyPay);
        $this->pay = new Pay($hyPay);
        $this->wallet = new Wallet($hyPay);
        parent::__construct();
    }

    /**
     * 快捷绑定银行卡
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.hypay/buildCardAdd)
     * @ApiReturnParams   (name="redirect_url", type="string", description="绑卡的页面地址")
     */
    public function buildCardAdd()
    {
        $member_real_info = (new MemberReal())->getDetails(['member_id' => $this->auth->member_id]);
        if (empty($member_real_info)) {
            $this->error("请先实名认证");
        }
        $redirect_url = $this->bank->bankSign($this->auth->member_id);
        $this->success("获取成功",$redirect_url);
    }

    /**
     * 获取绑定的银行卡列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.hypay/getBankList)
     * @ApiReturnParams   (name="linked_brbankname", type="string", description="银行卡名称")
     * @ApiReturnParams   (name="linked_acctno", type="string", description="银行卡号")
     * @ApiReturnParams   (name="linked_bankcode", type="string", description="银行卡编码(支付要用)")
     * @ApiReturnParams   (name="color", type="string", description="银行卡颜色")
     * @ApiReturnParams   (name="thumb", type="string", description="银行卡图标")
     */
    public function getBankList()
    {
        $memberCard_list = (new MemberCard())->where(['member_id'=>$this->auth->member_id,'pay_type'=>4])->select();
        $new_list = [];
        foreach ($memberCard_list as $key=>$value){
            $CardCategory = (new CardCategory())->where(['title'=>$value['bank']])->find();
            $new_list[$key]['linked_brbankname'] = $value['bank'];
            $new_list[$key]['linked_acctno'] = $value['card_no'];
            $new_list[$key]['linked_bankcode'] = $value['txn_seqno'];
            $new_list[$key]['color'] = empty($CardCategory)?"#486DEF":$CardCategory['color'];
            $new_list[$key]['thumb'] = $CardCategory['thumb'];
        }
        $this->success("获取成功",$new_list);
    }





    /**
     * 快捷支付确认支付
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.hypay/payConfirmTrade)
     * @ApiParams   (name="sms_code", type="string", required=true, description="验证码")
     * @ApiParams   (name="hy_bill_no", type="string", required=true, description="汇元单号")
     * @ApiParams   (name="hy_token_id", type="string", required=true, description="快捷支付返回的")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function payConfirmTrade()
    {
        $hy_bill_no = $this->request->post('hy_bill_no');
        $sms_code = $this->request->post('sms_code');
        $hy_token_id = $this->request->post('hy_token_id');
        if(!$hy_bill_no || !$sms_code){
            $this->error("参数错误!");
        }
        if($this->pay->getConfirmTrade($sms_code,$hy_bill_no,$hy_token_id)){
            $this->success("支付成功");
        }
        $this->error($this->pay->getError()?:"支付失败");
    }

    /**
     * 钱包开户
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.hypay/openAction)
     * @ApiReturnParams   (name="data", type="string", description="开户页面的地址")
     */
    public function openAction()
    {
        $member_real_info = (new MemberReal())->getDetails(['member_id' => $this->auth->member_id]);
        if (empty($member_real_info)) {
            $this->error("请先实名认证");
        }
        if($redirect_url = $this->wallet->openLoginWallet($this->auth->member_id)){
            $this->success("获取成功",$redirect_url);
        }
        $this->error($this->wallet->getError()?:"开户失败");
    }


//    /**
//     * 测试支付接口[不用对接]
//     * @ApiAuthor [Mr.Zhang]
//     * @ApiMethod (POST)
//     * @ApiRoute  (/api/purse.hypay/testPay)
//     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
//     */
//    public function testPay()
//    {
//        $HyPay = new \app\common\library\HyPay();
//        $result = $HyPay->getUserInfo($this->auth->member_id);
//        pre($result);
//        $result = $HyPay->QuickPayment("2023021854101495855", "0.01", "测试产品","fa4358ca1a981b8959021ab83feff506");
//        if($result){
//            $this->success("获取成功",$result);
//        }
//        $this->error($HyPay->getError()?:"支付失败");
//    }




}