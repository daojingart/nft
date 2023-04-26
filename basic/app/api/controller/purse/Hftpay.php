<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/9/22   10:39
 * +----------------------------------------------------------------------
 * | className: 汇付通开通钱包
 * +----------------------------------------------------------------------
 */
namespace app\api\controller\purse;


use app\common\controller\Controller;
use app\common\model\FfOpenFreeOrder;
use app\common\model\Member;
use app\common\model\MemberReal;
use app\common\model\PaySetting;
use hftpay\Payment;
use hftpay\Wallet;
use think\Exception;

/**
 * 我的钱包汇付通支付
 */
class Hftpay extends Controller
{

    protected $wallet;

    protected $Payment;

    protected $PaySetting;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function __construct()
    {
        $values = PaySetting::getItem('hftPay');
        $this->wallet = new Wallet($values);
        $this->Payment = new Payment($values);
        $this->PaySetting = $values;
        parent::__construct();
    }

    /**
     * 开通钱包
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hftpay/openWallet)
     * @ApiReturnParams   (name="redirect_url", type="string", description="开户链接")
     * @ApiReturnParams   (name="code", type="string", description="code ==-10 需要处理开户缴纳开户费逻辑；调用须知，调用缴纳支付开户费")
     */
    public function openWallet()
    {
        $memberReal_info = (new MemberReal())->where(['member_id' => $this->auth->member_id])->find();
        if(empty($memberReal_info)){
            $this->error('请先实名认证!');
        }
        if(substr($memberReal_info['card'],6,1) == '*'){
            (new Member())->where(['member_id'=> $this->auth->member_id])->update(['real_status'=>0]);
            $this->error('实名认证无法获取,请返回首页,根据引导提示完善认证信息!');
        }
        if($this->PaySetting['hf_open_fee_status']==10 && $this->auth->hf_open_free==10){
            $this->error('请先缴纳开户费',"","-10");
        }
        //检查手机号、身份证是否合法
        $redirect = $this->wallet->openWallet(array_merge([
            'user_name' => $memberReal_info['name'],
            'id_card' => $memberReal_info['card']
        ], [
            'member_id' => $this->auth->member_id
        ]));
        if($redirect){
            $this->success("获取成功",['redirect_url'=>$redirect]);
        }
        $this->error($this->wallet->getError());
    }

    /**
     * 查询开通钱包状态
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hftpay/queryWalletStatus)
     * @ApiReturnParams   (name="data", type="string", description="true 开户完成调用管理钱包的接口   false 没有开户 调用开户接口")
     */
    public function queryWalletStatus()
    {
        $result = $this->wallet->queryWallet($this->auth->member_id);
        if(!isset($result['acct_info_list']) || empty($result['acct_info_list'])){
            $this->error($this->wallet->getError()?:"开户失败",false);
        }
        $member_info = (new Member())->where(['member_id'=>$result['user_id']])->find();
        if(empty($member_info['user_cust_id']) || empty($member_info['acct_id'])){
            (new Member())->where(['member_id'=>$result['user_id']])->update([
                'user_cust_id' => $result['user_cust_id'],
                'acct_id' => $result['acct_info_list'][0]['acct_id'],
                'hf_open_free' => 20
            ]);
        }
        $this->success("已经开户成功",true);
    }



    /**
     * 获取缴纳开户费须知
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hftpay/getOpenWalletConfig)
     * @ApiReturnParams   (name="hf_open_notice", type="string", description="开户须知")
     * @ApiReturnParams   (name="hf_open_fee", type="string", description="收取的开户费金额")
     */
    public function getOpenWalletConfig()
    {
        $this->success("获取信息成功",['hf_open_notice'=>$this->PaySetting['hf_open_notice'],'hf_open_fee'=>$this->PaySetting['hf_open_fee']]);
    }

    /**
     * 缴纳开户手续费
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hftpay/getOpenFree)
     * @ApiReturnParams   (name="data", type="string", description="支付的链接")
     */
    public function getOpenFree()
    {
        $memberReal_info = (new MemberReal())->where(['member_id' => $this->auth->member_id])->find();
        if(empty($memberReal_info)){
            $this->error('请先实名认证!');
        }
        if($this->auth->real_status['value']!=2){
            $this->error('请先完成实名认证!');
        }
        if($this->auth->hf_open_free==20){
            $this->error('已经完成手续费的支付!无需重复支付');
        }
        if($this->PaySetting['hf_open_fee_status']==20){
            $this->error('已经关闭手续费的支付!');
        }
        if($this->PaySetting['hf_open_fee']<=0){
            $this->error('开户手续费设置错误,请联系平台客服');
        }
        //写入订单记录 后续要使用这个记录作为开户依据
        try {
            $order_sn = $this->orderNo();
            (new FfOpenFreeOrder())->insert([
                'order_sn' => $order_sn,
                'pay_amount' => $this->PaySetting['hf_open_fee'],
                'member_id' => $this->auth->member_id,
                'app_id' => '10001',
                'create_time' => time()
            ]);
            $result = $this->Payment->premiumPayments($this->auth->member_id,$order_sn, $this->PaySetting['hf_open_fee'], "开户手续费",$memberReal_info['card']);
			if($result){
                $this->success("获取成功",['redirect_url'=>$result]);
            }
            $this->error($this->Payment->getError());
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
    }


    /**
     * 钱包管理
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Hftpay/getWalletDetails)
     * @ApiReturnParams   (name="redirect_url", type="string", description="钱包管理链接")
     */
    public function getWalletDetails()
    {
        $result = $this->wallet->getWalletDetails($this->auth->member_id);
        if($result){
            $this->success("获取成功",['redirect_url'=>$result['redirect_url']]);
        }
        $this->error($this->wallet->getError());

    }



    /**
     * 生成订单号
     */
    protected function orderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }
}