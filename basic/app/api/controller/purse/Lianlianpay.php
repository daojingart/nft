<?php

namespace app\api\controller\purse;

use app\api\model\order\RechargeOrder;
use app\common\controller\Controller;
use app\common\library\LianPay;
use app\common\model\CardCategory;
use app\common\model\MemberCard;
use app\common\model\MemberReal;
use app\common\model\Order;
use app\common\model\PaySetting;
use app\common\model\Withdraw;
use lianlian\Acctmgr;
use lianlian\BankCard;
use lianlian\Tools;
use lianlian\Txn;
use think\Request;

/**
 * 我的钱包连连支付
 */
class Lianlianpay extends Controller
{
    protected $llPay;
    protected $llPayCard;
    protected $tools;
    protected $pay;


    public function __construct(Request $request = null)
    {
        $lianlianPay = PaySetting::getItem('lianlianPay');
        $this->llPay = new Acctmgr($lianlianPay);
        $this->llPayCard = new BankCard($lianlianPay);
        $this->tools = new Tools($lianlianPay);
        $this->pay = new Txn($lianlianPay);
        parent::__construct($request);
    }

    /**
     * 获取开户的链接
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/getopenacctApplyUrl)
     * @ApiReturnParams   (name="data", type="string", description="开户的链接")
     */
    public function getopenacctApplyUrl()
    {
        //判断用户是否实名认证 没有实名认证禁止开户
        $member_info = MemberReal::get(['member_id'=>$this->auth->member_id]);
        if(empty($member_info)){
            $this->error('请先实名认证');
        }
        $resultUrl = $this->llPay->openacctApply([
            'member_id' => $this->auth->member_id,
            'phone' => $this->auth->phone,
            'real_name' => $member_info['name'],
            'id_card' => $member_info['card'],
        ]);
        $this->success('获取链接成功',$resultUrl);
    }

    /**
     * 获取开户信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/getQueryUserInfo)
     * @ApiReturnParams   (name="data", type="string", description="true 开户成功  false  开户失败")
     */
    public function getQueryUserInfo()
    {
        $this->success('获取成功',$this->llPay->getQueryUserinfo($this->auth->member_id));
    }

    /**
     * 新增绑卡申请
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/bindcardApplyAdd)
     * @ApiParams   (name="linked_acctno", type="string", required=true, description="银行卡号")
     * @ApiParams   (name="linked_phone", type="string", required=true, description="银行预留手机号")
     * @ApiParams   (name="password", type="string", required=true, description="支付密码")
     * @ApiParams   (name="random_key", type="string", required=true, description="密码随机因子key")
     * @ApiReturnParams   (name="token", type="string", description="授权令牌。有效期30分钟。")
     */
    public function bindcardApplyAdd()
    {
        $linked_acctno = $this->request->post('linked_acctno');
        $linked_phone = $this->request->post('linked_phone');
        $password = $this->request->post('password');
        $random_key = $this->request->post('random_key');
        if(!$linked_acctno || !$linked_phone || !$password || !$random_key){
            $this->error('参数错误');
        }
        $token = $this->llPayCard->bindcardApply(array_merge($this->request->post(),['user_id'=>$this->auth->member_id]));
        if($token){
            $this->success('申请绑定',$token);
        }
        $this->error("绑定银行卡失败");
    }

    /**
     * 新增绑卡验证
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/bindcardVerify)
     * @ApiParams   (name="txn_seqno", type="string", required=true, description="申请绑卡返回的流水账号")
     * @ApiParams   (name="token", type="string", required=true, description="申请绑卡返回授权令牌，有效期为30分钟。")
     * @ApiParams   (name="verify_code", type="string", required=true, description="短信验证码。验证银行预留手机号。")
     */
    public function bindcardVerify()
    {
        $linked_acctno = $this->request->post('txn_seqno');
        $linked_phone = $this->request->post('token');
        $password = $this->request->post('verify_code');
        if(!$linked_acctno || !$linked_phone || !$password){
            $this->error('参数错误');
        }
        $linked_agrtno = $this->llPayCard->individualBindCardVerify(array_merge($this->request->post(),['user_id'=>$this->auth->member_id]));
        if($linked_agrtno){
            //绑卡成功
            $this->success('绑定成功');
        }
        $this->error("绑定银行卡失败");
    }

    /**
     * 获取绑卡列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/getBankList)
     * @ApiReturnParams   (name="linked_acctno", type="string", description="个人用户绑定的银行卡号")
     * @ApiReturnParams   (name="linked_agrtno", type="string", description="绑卡协议号")
     * @ApiReturnParams   (name="linked_bankcode", type="string", description="银行编码")
     * @ApiReturnParams   (name="linked_brbankname", type="string", description="账户开户行名")
     * @ApiReturnParams   (name="linked_phone", type="string", description="银行预留手机号")
     * @ApiReturnParams   (name="color", type="string", description="颜色")
     * @ApiReturnParams   (name="thumb", type="string", description="银行卡图标")
     */
    public function getBankList()
    {
        $bank_list = $this->llPayCard->queryLinkedAcct(['user_id'=>$this->auth->member_id]);
        foreach ($bank_list as $key=>$value){
            $CardCategory = (new CardCategory())->where(['title'=>$value['linked_brbankname']])->find();
            $bank_list[$key]['bank_number'] = $value['linked_acctno'];
            $bank_list[$key]['linked_acctno'] = strreplace($value['linked_acctno'],0,4);
            $bank_list[$key]['color'] = empty($CardCategory)?"#486DEF":$CardCategory['color'];
            $bank_list[$key]['thumb'] = $CardCategory['thumb'];
        }
        $this->success('获取成功',$bank_list);
    }

    /**
     * 解绑银行卡
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/unsetBank)
     * @ApiParams   (name="linked_agrtno", type="string", required=true, description="个人用户绑定的银行卡号。")
     * @ApiParams   (name="password", type="string", required=true, description="支付密码。6-12位的字母、数字，不可以是连续或者重复的数字和字母，正则：^[a-zA-Z0-9]{6,12}$。通过密码控件加密成密文传输。")
     * @ApiParams   (name="random_key", type="string", required=true, description="密码随机因子key。随机因子获取接口返回，或弹框控件回调函数返回。")
     * @ApiReturnParams   (name="data", type="string", description="解绑成功")
     */
    public function unsetBank()
    {
        $linked_agrtno = $this->request->post('linked_agrtno');
        $password = $this->request->post('password');
        $random_key = $this->request->post('random_key');
        if(!$linked_agrtno || !$password || !$random_key){
            $this->error('参数错误');
        }
        if($bank_list = $this->llPayCard->unlinkedAcctIndApply([
            'user_id'=> $this->auth->member_id,
            'linked_agrtno'=> $linked_agrtno,
            'password' => $password,
            'random_key' => $random_key
        ])){
            $this->success('解绑成功');
        }
        $this->error("解绑失败");
    }








    /**
     * 随机密码因子获取
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/getRandom)
     * @ApiParams   (name="flag_chnl", type="string", required=true, description="交易发起渠道 ANDROID IOS H5")
     * @ApiReturnParams   (name="license", type="string", description="license。flag_chnl为ANDROID、IOS、H5时返回")
     * @ApiReturnParams   (name="map_arr", type="string", description="映射数组")
     * @ApiReturnParams   (name="random_key", type="string", description="随机因子key，有效期30分钟。")
     * @ApiReturnParams   (name="random_value", type="string", description="随机因子值，有效期30分钟。")
     * @ApiReturnParams   (name="rsa_public_content", type="string", description="连连RSA公钥。")
     */
    public function getRandom()
    {
        $flag_chnl = $this->request->post('flag_chnl');
        if(!$flag_chnl){
            $this->error('参数错误');
        }
        $this->success('获取成功',$this->tools->getAcctmgrRandom($this->auth->member_id,$flag_chnl));
    }

    /**
     * 申请密码控件Token
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/getPasswordToken)
     * @ApiParams   (name="password_scene", type="string", required=true, description="设置密码：setting_password；修改密码：change_password；换绑卡：bind_card_password；提现密码：cashout_password；支付密码：pay_password")
     * @ApiParams   (name="flag_chnl", type="string", required=true, description="H5")
     * @ApiParams   (name="amount", type="string", required=true, description="交易金额，单位：元，精确到小数点后两位，例如:1.00，支付和提现场景必填")
     * @ApiParams   (name="txn_seqno", type="string", required=true, description="商户系统唯一交易流水号，由商户自定义，余额支付场景时必填。")
     * @ApiReturnParams   (name="map_arr", type="string", description="映射数组")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function getPasswordToken()
    {
        $password_scene = $this->request->post('password_scene');
        $flag_chnl = $this->request->post('flag_chnl');
        $amount = $this->request->post('amount')?:0;
        $txn_seqno = $this->request->post('txn_seqno')?:"";
        if(!$password_scene || !$flag_chnl){
            $this->error('参数错误');
        }
        if(in_array($password_scene, ['cashout_password','pay_password'])){
            if(!$amount || !$txn_seqno){
                $this->error('参数错误');
            }
        }
        $this->success('获取成功',$this->tools->applyPasswordElementToken($this->auth->member_id,$password_scene,$flag_chnl,$amount,$txn_seqno));
    }

    /**
     * 修改密码申请
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/getPasswordApply)
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function getPasswordApply()
    {
        //判断当前用户是否绑定银行卡,绑定了需要获取一个卡的银行卡账号
        $memberCardInfo = (new MemberCard())->where(['member_id'=>$this->auth->member_id])->find();
        $linked_acctno = '';
        if(!empty($memberCardInfo)){
            $linked_acctno = $memberCardInfo['card_no'];
        }
        $result = $this->llPay->editPasswordApply($this->auth->member_id,$linked_acctno);
        if($result){
            $this->success("申请修改成功",$result);
        }
        $this->error("申请修改失败");
    }

    /**
     * 修改密码验证
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/editPasswordVerify)
     * @ApiParams   (name="token", type="string", required=true, description="授权令牌，有效期为30分钟。")
     * @ApiParams   (name="verify_code", type="string", required=true, description="短信验证码")
     * @ApiParams   (name="random_key", type="string", required=true, description="密码随机因子key。随机因子获取接口返回，或弹框控件回调函数返回。")
     * @ApiParams   (name="password", type="string", required=true, description="新支付密码通过密码控件加密成密文传输。")
     */
    public function editPasswordVerify()
    {
        $token = $this->request->post('token');
        $verify_code = $this->request->post('verify_code');
        $random_key = $this->request->post('random_key');
        $password = $this->request->post('password');
        if(!$token || !$verify_code || !$random_key || !$password){
            $this->error('参数错误');
        }
        if($this->llPay->findPasswordVerify([
            'user_id' => $this->auth->member_id,
            'token' => $token,
            'verify_code' => $verify_code,
            'random_key' => $random_key,
            'password' => $password
        ])){
            $this->success("修改成功");
        }
        $this->error("修改失败");
    }


    /**
     * 账户信息查询
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/getQueryAcctinfo)
     * @ApiReturnParams   (name="account", type="string", description="可用金额  可以提现和消费")
     * @ApiReturnParams   (name="pending_account", type="string", description="待结算金额 只能用于消费")
     * @ApiReturnParams   (name="sum_amount", type="string", description="总金额")
     */
    public function getQueryAcctinfo()
    {
        $amountAcctInfo = $this->llPay->queryAcctinfo($this->auth->member_id);
        $info = [
            'account' => $amountAcctInfo['pending_account'], //可用金额  可以提现和消费
            'pending_account' => $amountAcctInfo['available_amount'], //待结算金额 只能用于消费
            'sum_amount' => bcadd($amountAcctInfo['pending_account'],$amountAcctInfo['available_amount'],2)
        ];
        $this->success("获取成功",$info);
    }

    /**
     * 账户流水查询
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/getQueryAcctserial)
     * @ApiParams   (name="flag_dc", type="string", required=true, description="ALL 全部 DEBIT：出账   CREDIT：入账 ")
     * @ApiParams   (name="page_no", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="page_no", type="string", description="当前页码，表示返回结果集第几页")
     * @ApiReturnParams   (name="total_num", type="string", description="结果集总数")
     * @ApiReturnParams   (name="total_page", type="string", description="结果集总页数")
     * @ApiReturnParams   (name="date_acct", type="string", description="账务日期。交易账期，格式：yyyyMMdd")
     * @ApiReturnParams   (name="txn_type", type="string", description="用户充值：USER_TOPUP
    商户充值：MCH_TOPUP
    普通消费：GENERAL_CONSUME
    担保消费：SECURED_CONSUME
    担保确认：SECURED_CONFIRM
    内部代发：INNER_FUND_EXCHANGE
    定向内部代发：INNER_DIRECT_EXCHANGE
    外部代发：OUTER_FUND_EXCHANGE
    账户提现：ACCT_CASH_OUT
    手续费收取：SERVICE_FEE
    手续费应收应付核销：CAPITAL_CANCEL
    垫资调账：ADVANCE_PAY")
     * @ApiReturnParams   (name="amt", type="string", description="出入账金额。单位 元。")
     */
    public function getQueryAcctserial()
    {
        $flag_dc = $this->request->post('flag_dc');
        $page_no = $this->request->post('page_no');
        if(!$flag_dc || !$page_no){
            $this->error('参数错误');
        }
        $list = $this->llPay->queryAcctserial([
            'user_id' => $this->auth->member_id,
            'flag_dc' => $flag_dc,
            'page_no' => $page_no,
            'date_start' => date("YmdHis",strtotime($this->auth->create_time)),
        ]);
        $this->success("获取成功",$list);
    }

    /**
     * 交易二次确认
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/validationSms)
     * @ApiParams   (name="order_sn", type="string", required=true, description="订单编号")
     * @ApiParams   (name="token", type="string", required=true, description="授权令牌")
     * @ApiParams   (name="sms_code", type="string", required=true, description="短信验证码")
     * @ApiReturnParams   (name="order_sn", type="string", description="订单编号")
     * @ApiReturnParams   (name="ret_msg", type="string", description="返回信息")
     */
    public function validationSms()
    {
        $order_sn = $this->request->post('order_sn');
        $token = $this->request->post('token');
        $sms_code = $this->request->post('sms_code');
        if(!$order_sn || !$token || !$sms_code){
            $this->error('参数错误');
        }
        $sub_str = substr($order_sn, 0, 4);
        if ($sub_str == 'BLED') {
            $order_info = (new \app\common\model\RechargeOrder())->where(['order_no' => $order_sn, 'member_id' => $this->auth->member_id])->find();
            $total_price = $order_info['pay_price'];
        } else if ($sub_str == 'with') {
            $Withdraw = (new Withdraw())->where(['order_sn' => $order_sn])->find();
            $total_price = $Withdraw['amount'];
        } else {
            $order_info = (new Order())->with('goods')->where(['order_no' => $order_sn, 'member_id' => $this->auth->member_id])->find();
            $total_price = $order_info['total_price'];
        }
        if($result = $this->pay->validationSms($this->auth->member_id,$order_sn,$total_price,$token,$sms_code)){
            $this->success("支付成功",$result);
        }
        $this->error("支付失败");
    }


    /**
     * 获取充值规格
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/getRechargeList)
     * @ApiReturnParams   (name="ecard_id", type="string", description="规格的ID")
     * @ApiReturnParams   (name="face_value", type="string", description="规格的面值")
     */
    public function getRechargeList()
    {
        $this->success("获取成功",RechargeModel::getDataList());
    }


    /**
     * 提交确认重置
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/purse.Lianlianpay/submit)
     * @ApiParams   (name="eCardId", type="string", required=true, description="ID")
     * @ApiParams   (name="card_no", type="string", required=true, description="银行卡标识")
     * @ApiParams   (name="type", type="string", required=true, description="1=直接充值  2=规格充值")
     * @ApiParams   (name="pwd", type="string", required=true, description="密码")
     * @ApiParams   (name="random_key", type="string", required=true, description="随机KEY")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function submit($eCardId = null,$card_no,$type,$pwd,$random_key)
    {
        // 获取用户记录
        $member_info = $this->auth->getUser();
        //处理防止重复提交信息
        $redis_key =  $member_info['member_id'];
        $lock_key = 'Recharge_LOCK_PREFIX' . $redis_key;
        if($this->redis->exists($lock_key)){
            $this->error("小主,点击的太快喽!");
        }
        $this->redis->setex($lock_key, '3', 1); // 写入内容
        $model = new RechargeOrder();
        if (!$model->createOrder($member_info, $eCardId,$card_no,13,$type)) {
             $this->error($model->getError() ?: '充值失败');
        }
        $goods_name = "钱包账户充值{$model['face_value']}";
        //支付快捷
        $LianPay = new LianPay();
        $LianPay->createRadecreate("USER_TOPUP",$model['order_id'],$this->auth->member_id);
        $this->success("获取成功",[
            'payment' => $LianPay->bankcardPay($model['order_no'],$pwd,$random_key,$this->auth->member_id,$model['pay_price'],$goods_name,$card_no),
            'order_id' => $model['order_id'],
        ]);
    }



}