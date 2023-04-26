<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/14   17:03
 * +----------------------------------------------------------------------
 * | className: 提现管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\finance;

use app\admin\model\setting\Notice;
use app\common\extend\alipay\MakePayment;
use app\common\extend\llp\CardPayment;
use app\common\model\Finance;
use app\common\model\PaySetting;
use app\common\model\Withdraw as WithdrawModel;
use lianlian\Txn;
use lianlian\Withdrawal;
use think\Db;


class Withdraw extends WithdrawModel
{

    /**
     * 获取提现审核列表
     * @param $filter
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   19:33
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($filter,$param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;

        // 按照商品名称查询
        if(!empty($param['phone'])){$filter['b.phone'] = $param['phone'];}
        if (isset($filter['status'])){
            $filter['a.status'] = $param['status'];
        }

        $dataList = $this->alias('a')
            ->join('member b','a.member_id=b.member_id','left')
            ->where($filter)
            ->field('a.*,b.name as nickname,b.phone')
            ->order(['a.id' => 'desc'])
            ->limit($offset,$limit)
            ->select();
        foreach ($dataList as $k => $v){
            if($v['type']==1){ //支付宝信息
                $dataList[$k]['bank_card'] = empty($v['zfb_account'])?"--":$v['zfb_account'];
                $dataList[$k]['bank_nickname'] = empty($v['zfb_name'])?"--":$v['zfb_name'];
            }else{ //银行卡信息
                $dataList[$k]['bank_card'] = empty($v['bank_card'])?"--":$v['bank_card'];
                $dataList[$k]['bank_nickname'] = empty($v['bank_nickname'])?"--":$v['bank_nickname'];
            }
            $dataList[$k]['audio_time'] = strtotime($v['update_time'])==0?"--":$v['update_time'];
            $dataList[$k]['bank_name'] = empty($v['bank_name'])?"--":$v['bank_name'];
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['status'],$v['id']));
        }

        $return['count'] = $this->alias('a')->join('member b','a.member_id=b.member_id','left')->where($filter)->count();
        $return['data'] = $dataList;
        $return['code'] = 0;
        $return['msg'] = 'OK';

        return $return;
    }

    /**
     * 操作按钮
     * @param $status
     * @param $id
     * @return array|\string[][]
     * @Time: 2022/6/14   19:37
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static  function makeButton($status,$id)
    {
        if($status == 0){
            return [
                '通过' => [
                    'href' => "javascript:void(0)",
                    'lay-event' => 'adopt',
                ],
                '拒绝' => [
                    'href' => "javascript:void(0)",
                    'lay-event' => 'refuse',
                ]
            ];
        }else {
            return [
                '--' => [
                    'href' => "javascript:void(0)",
                    'lay-event' => '--',
                ],
            ];
        }
    }

    /**
     * 提现审核
     * @param $data
     * @param $info
     * @return bool
     * @Time: 2022/7/9   14:34
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface upStatus
     */
    public function upStatus($data,$info)
    {
        if ($data['status'] == 2 && empty($data['reason'])) {
            $this->error = '请填写驳回原因';return false;
        }
        //使用事务处理
        Db::startTrans();
        try{
            if(!empty($data['pay_type']) && $data['pay_type']==1 && $info['type'] !=1){
                $this->error = '用户提现方式不支持支付宝打款';return false;
            }
            if(!empty($data['pay_type']) && $data['pay_type']==3 && $info['type'] !=2){
                $this->error = '用户提现方式不是银行卡提现无法打款';return false;
            }
            //判断打款方式  如果打款方式是支付宝 并提现账户是支付宝可以使用支付宝付款 否则不进行审核
            if(!empty($data['pay_type']) && $data['pay_type']==1 && $info['type'] ==1){
                //调用支付宝的打款接口进行支付宝打款
                (new MakePayment())->payment($info['zfb_account'],$info['id'],$info['actual_amount']);
            }
            if((!empty($data['pay_type']) && $data['pay_type']==3 && $info['type'] ==2) ||  ($info['type'] ==2 && $data['status'] == 2)){
                //调用支付宝的打款接口进行支付宝打款
                (new Withdrawal(PaySetting::getItem('lianlianPay')))->withdrawalCheck([
                    'txn_seqno' => $info['order_sn'],
                    'total_amount' => $info['amount'],
                    'fee_amount' => $info['service_price'],
                    'check_result' => $data['status']==2?"CANCEL":'ACCEPT',
                    'check_reason' => $data['reason']
                ]);
            }
            // 更新申请记录
            $data['update_time'] = time();
            unset($data['pay_type']);
            $this->allowField(true)->where(['id' => $info['id']])->update($data);
            // 提现驳回：原路返回提现资金
            if($data['status'] == 2 && empty($info['order_sn'])){
                $this->returnMoney($info,$data['reason']);
            }
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 退还账户余额
     * @param $info
     * @return bool
     * @Time: 2022/6/14   20:35
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface returnMoney
     */
    public function returnMoney($info,$reason)
    {

        // 退还账户余额
        (new Finance())->insert([
            'amount' => $info['amount'],
            'remark' => date('Y-m-d H:i:s',time()).'余额提现失败',
            'content' => '余额提现失败,金额原路返回',
            'type' => 1,
            'member_id' => $info['member_id'],
            'apply_id' => $info['id'],
            'app_id' => self::$app_id,
            'create_time' => time(),
            'update_time' => time(),
            'from' => 1
        ]);

        // 审核拒绝发送站内信
        (new Notice())->insert([
            'member_id' => $info['member_id'],
            'type' => 1,
            'title' => '提现拒绝',
            'content' => '余额提现失败,金额原路返回;'.$reason,
            'create_time' => time(),
            'update_time' => time(),
            'app_id' => self::$app_id
        ]);

        return true;
    }


}