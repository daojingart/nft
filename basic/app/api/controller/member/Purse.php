<?php

namespace app\api\controller\member;

use app\common\components\helpers\RedisUtils;
use app\common\controller\Controller;
use app\common\model\PaySetting;
use app\common\model\Finance as FinanceModel;
use app\common\model\Setting;
use app\common\model\Withdraw;

/**
 * 我的钱包
 */
class Purse extends Controller
{
    /**
     * 钱包首页
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.purse/index)
     * @ApiReturnParams   (name="title", type="string", description="支付方式,根据UI进行名字的增加")
     * @ApiReturnParams   (name="icon", type="string", description="支付的图标")
     * @ApiReturnParams   (name="open_status", type="string", description="快捷支付的开关 10==开启  20关闭")
     * @ApiReturnParams   (name="open_purse_status", type="string", description="钱包的支付开关 10==开启  20关闭")
     * @ApiReturnParams   (name="purse_balance", type="string", description="钱包的余额")
     * @ApiReturnParams   (name="sum_apply_amount", type="string", description="累计提现金额")
     */
    public function index()
    {
        $new_purse_type = [];
        //获取每个钱包的配置信息
        $paySettingList = (new PaySetting())->getPayGetWay(1);
        //获取累计体现金额
        $financeAmount = (new Withdraw())->where(['type'=>1,'member_id'=>$this->auth->member_id])->sum('amount');
        $this->success('获取成功',['purse_type'=>$paySettingList,'purse_balance'=>$this->auth->account,'sum_apply_amount'=>abs($financeAmount)]);
    }

    /**
     * 钱包明细
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (get)
     * @ApiRoute  (/api/member.purse/detail)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="id", type="string", description="记录的ID")
     * @ApiReturnParams   (name="amount", type="string", description="金额")
     * @ApiReturnParams   (name="remark", type="string", description="记录描述")
     * @ApiReturnParams   (name="create_time", type="string", description="记录时间")
     */
    public function detail()
    {
        $param = array_merge($this->request->param(),['listRows' => 10,'member_id' => $this->auth->member_id]);
        $model = new FinanceModel();
        $list = $model->getDataList($param);
        $this->success('ok',$list);
    }

    /**
     * 提现记录
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (get)
     * @ApiRoute  (/api/member.purse/getWithdrawalsList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="id", type="string", description="记录的ID")
     * @ApiReturnParams   (name="amount", type="string", description="金额")
     * @ApiReturnParams   (name="zfb_account", type="string", description="描述")
     * @ApiReturnParams   (name="create_time", type="string", description="时间")
     * @ApiReturnParams   (name="status", type="string", description="审核状态  0未审核   1审核通过   2审核拒绝")
     */
    public function getWithdrawalsList()
    {
        $page = $this->request->get('page')?:1;
        $list = (new Withdraw())->where(['member_id'=>$this->auth->member_id])->field("id,amount,zfb_account,create_time,status")->page($page,10)->select();
        foreach ($list as $key=>$value){
            $value['zfb_account'] = "提现到支付宝账户:".$value['zfb_account'];
        }
        $this->success($list);
    }

    /**
     * 钱包提现
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.purse/withdraw)
     * @ApiParams   (name="price", type="string", required=true, description="提现的金额")
     * @ApiParams   (name="type", type="string", required=true, description="1=支付宝 2=连连银行卡提现")
     * @ApiParams   (name="linked_agrtno", type="string", required=true, description="银行卡提现的时候传值银行卡的linked_agrtno type==2的时候传值")
     * @ApiParams   (name="order_sn", type="string", required=true, description="订单编号 type==2的时候传值")
     * @ApiParams   (name="password", type="string", required=true, description="钱包的密码 type==2的时候传值")
     * @ApiParams   (name="random_key", type="string", required=true, description="随机KEY type==2的时候传值")
     * @ApiParams   (name="zfb_name", type="string", required=true, description="支付宝姓名 type==1的时候传值")
     * @ApiParams   (name="zfb_account", type="string", required=true, description="支付宝账号 type==1的时候传值")
     * @ApiReturnParams   (name="code", type="string", description="连连钱包提现的时候如果code ==8888 需要进行支付的二次确认")
     * @ApiReturnParams   (name="data", type="string", description="返回的Token")
     */
    public function withdraw()
    {
        $member_info = $this->auth->getUser();
        // 获取提现配置
        $withdraw = Setting::getItem('withdrawal');
        if($withdraw['status'] == 20){
            $this->error($withdraw['close_text']);
        }
        $param = $this->request->param();
        if($param['price'] <= 0){
            $this->error('请输入合法的提现金额');
        }
        if($member_info['account'] < $param['price']){
            $this->error('账户余额不足');
        }
        if($withdraw['minimum_withdrawal'] > $param['price']){
            $this->error('单次最低提现金额'.$withdraw['minimum_withdrawal']."元");
        }
        $lockKey = "toWithdrawal:{$member_info['member_id']}:{$param['type']}";
        if (RedisUtils::lock($lockKey, 10)) {
            $this->error('提现通道拥堵!请稍后重试');
        }
        $model = new Withdraw();
        try {
            $result = $model->toWithdrawal($param,$member_info,$withdraw['handling_fee']);
        } catch (\Exception $e) {
            RedisUtils::unlock($lockKey);
            $this->error($e->getMessage());
        }
        RedisUtils::unlock($lockKey);
        if(isset($result['code']) && $result['code']==1){
            $this->success('提现成功',$result);
        }
        if(isset($result['code']) && $result['code']==8888){
            $this->success('请先二次确认',$result['msg']);
        }
        $this->error($model->getError());
    }

    /**
     * 获取提现的订单编号
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.purse/getOrderSn)
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function getOrderSn()
    {
        $order_sn =  'withdrawal'.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
        $this->success("获取成功",$order_sn);
    }

}