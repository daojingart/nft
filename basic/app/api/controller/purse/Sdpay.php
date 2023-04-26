<?php

namespace app\api\controller\purse;

use app\common\controller\Controller;
use app\common\model\MemberReal;
use app\common\model\PaySetting;
use exception\BaseException;
use sd\Acctmgr;

/**
 * 杉德支付
 */
class Sdpay extends Controller
{
    protected $payment;
	protected $config;
    public function __construct()
    {
        $lianlianPay = PaySetting::getItem('sdPay');
        $this->payment = new Acctmgr($lianlianPay);
		$this->config  = $lianlianPay;
        parent::__construct();
    }

    /**
     * 杉德云钱包开户
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Sdpay/openAction)
     * @ApiReturnParams   (name="jump_url", type="string", description="跳转地址")
     */
    public function openAction()
    {
        $member_real_info = (new MemberReal())->where(['member_id'=>$this->auth->member_id])->find();
        if (empty($member_real_info)){
            $this->error('请先实名认证;然后再开通钱包');
        }
		if(!$this->payment->getMemberStatusQuery($this->auth->member_id)){
			if ( $this->auth->sd_open_free==10 && $this->config['account_opening_fee_status'] == 10 ) {
				$this->error('请先缴纳开户费用');
			}
		}
        $this->success("开户成功", $this->payment->openAccount(['member_id' => $this->auth->member_id,'name' => $member_real_info['name'],'card' => $member_real_info['card']]));
    }

	/**
	 * 获取是否收取开户费
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute (/api/purse.Sdpay/getIsAccountOpen)
	 * @ApiReturnParams   (name="is_open_account_status", type="string", description="10=未开通 20=已开通")
	 */
	public function getIsAccountOpen()
	{
		$is_open_account_status = $this->auth->sd_open_free; //未开通  20已开通
		if($this->config['account_opening_fee_status'] == 20 || $this->payment->getMemberStatusQuery($this->auth->member_id)) {
			$is_open_account_status = 20;
		}
		$this->success("获取成功", ['is_open_account_status' => $is_open_account_status]);
	}

	/**
	 * 获取收取开户费的信息
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/purse.Sdpay/getAccountInfo)
	 * @ApiReturnParams   (name="account_opening_fee_content", type="string", description="开户规则 文本域格式需要兼容换行符")
	 */
	public function getAccountInfo()
	{
		$this->success('获取成功', ['account_opening_fee_content' => $this->config['account_opening_fee_content']]);
	}


	/**
	 * 支付杉德钱包开户费
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute (/api/purse.Sdpay/payAccount)
	 * @ApiReturnParams   (name="jump_url", type="string", description="支付跳转接口")
	 */
	public function payAccount()
	{
		if($this->auth->sd_open_free == 20 || $this->config['account_opening_fee_status'] == 20){
			$this->error('您已经缴纳过开户费了,请联系平台客户！');
		}
		$member_realInfo = (new MemberReal())->where(['member_id' => $this->auth->member_id])->find();
		if (empty($member_realInfo)) {
			$this->error('请先实名认证;然后再开通钱包');
		}
		$this->success("开户成功", $this->payment->paymentQuick($this->orderHyNo()."_".$this->auth->member_id,$this->config['account_opening_fee'],$this->auth->member_id,$member_realInfo['name'], $member_realInfo['card']));
	}

	/**
	 * 生成订单号
	 */
	protected function orderHyNo()
	{
		return "openSd".date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
	}


	/**
	 * 注销账户
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute (/api/purse.Sdpay/logoutAccount)
	 * @ApiParams   (name="member_id", type="string", required=true, description="用户ID")
	 */
	public function logoutAccount()
	{
		$member_id = $this->request->param("member_id");
		$this->success("销户成功", $this->payment->setAccountStatus($member_id));
	}


}