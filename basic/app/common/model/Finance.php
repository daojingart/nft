<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: sliyusheng@foxmail.com
 * +----------------------------------------------------------------------
 * | Time:  2022/06/13 19:08
 * +----------------------------------------------------------------------
 * | className:  余额变化数据模型
 * +----------------------------------------------------------------------
 */
namespace app\common\model;


use app\common\extend\llp\Acctmgr;
use think\Request;

class Finance extends BaseModel
{
    protected $name = 'finance';


    /**
     * 获取账单流水
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     * @Time: 2022/6/14   15:58
     */
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;

        $where = [];
        if (!empty($param['phone'])) {$where['b.phone'] = $param['phone'];}
        if (!empty($param['type'])) {$where['a.type'] = $param['type'];}
        if(!empty($param['from'])){$where['a.from'] = $param['from'];}
        if(!empty($param['amount_type'])){
            if($param['amount_type'] == 1){$where['a.amount'] = ['>',0];}
            if($param['amount_type'] == 2){$where['a.amount'] = ['<',0];}
        }


        // 根据下单时间筛选
        if (!empty($param['create_time'])) {
            $create_time = explode('~',$param['create_time']);
            $where['a.create_time'] = ['between time',[trim($create_time[0]),trim($create_time[1])]];
        }

        $dataList = $this->alias("a")
            ->join('member b', 'a.member_id = b.member_id','left')
            ->where($where)
            ->order(['a.id' => 'desc'])
            ->field('a.*,b.name as nickname,b.phone')
            ->limit($offset,$limit)
            ->select();

        foreach ($dataList as $key => $value){
            // 币种类型
            if($value['from'] == 1){
                $dataList[$key]['from'] = '余额';
            }elseif ($value['from'] == 2){
                $dataList[$key]['from'] = '微信';
            }elseif ($value['from'] == 3){
                $dataList[$key]['from'] = '支付宝';
            }
            // 收支类型
            if($value['amount'] > 0){
                $dataList[$key]['amount_type'] = '收入';
            }else{
                $dataList[$key]['amount_type'] = '支出';
            }
        }

        $return['count'] = $this->alias("a")->join('member b', 'a.member_id = b.member_id','left')->where($where)->count();
        $return['data'] = $dataList;
        $return['code'] = '0';
        $return['msg'] = 'OK';
        return $return;
    }


    /**
     * 获取资金记录列表
     * @param $param
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/6/20   21:58
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getDataList
     */
    public function getDataList($param)
    {
        $list = self::where(['member_id' => $param['member_id']])->field('id,amount,remark,create_time')
            ->order('create_time desc')
            ->paginate($param['listRows'],false,[
                'query' =>  Request::instance()->request()
            ]);
        return $list;
    }

    /**
     * 消费类型
     * @param $type_code
     * @return string|void
     * @Time: 2022/7/28   01:02
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getTxnType
     */
    public function getTxnType($type_code)
    {
        switch ($type_code)
        {
            case "USER_TOPUP":
                return "用户充值";
            case "GENERAL_CONSUME":
                return "普通消费";
            case "SECURED_CONSUME":
                return "担保消费";
            case "SERVICE_FEE":
                return "手续费收取";
            case "INNER_FUND_EXCHANGE":
                return "内部代发";
            case "ACCT_CASH_OUT":
                return "账户提现";
            case "OUTER_FUND_EXCHANGE":
                return "外部代发";
            case "SECURED_CONFIRM":
                return "担保确认";
            case "CAPITAL_CANCEL":
                return "手续费应收应付核销";
            case "INNER_DIRECT_EXCHANGE":
                return "定向内部代发";
        }
    }

}