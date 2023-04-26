<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-13 17:21
 */


namespace app\common\model;
use app\admin\model\Setting;
use exception\BaseException;
use think\Session;

/**
 * 用户数据模型
 * Class Member
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-13 17:22
 */
class Member extends BaseModel
{

    // 定义全局的查询范围
    protected function base($query)
    {
        $query->where('is_del',1);
    }

    /**
     * 状态
     */
    public function getStatusAttr($value)
    {
        $data = ['禁用','正常'];
        $res = ['value'=>$value,'text'=>$data[$value]];
        return $res;
    }
    /**
     * 实名
     */
    public function getRealStatusAttr($value)
    {
        $data = ['未实名','审核中','已实名','已拒绝'];
        $res = ['value'=>$value,'text'=>$data[$value]];
        return $res;
    }
    /**
     * 性别
     */
    public function getGenderAttr($value)
    {
        $data = ['未知','男','女'];
        $res = ['value'=>$value,'text'=>$data[$value]];
        return $res;
    }
    /**
     * 注册渠道
     */
    public function getFromTypeAttr($value)
    {
        if ($value==''){
            $res = ['value'=>$value,'text'=>'app手机号注册'];
            return $res;
        }
        $data = ['xcx'=>'微信小程序注册','APP'=>'app微信注册','h5'=>'公众号注册'];
        $res = ['value'=>$value,'text'=>$data[$value]];
        return $res;
    }

    /**
     * 获取当前用户上级用户
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 17:54
     */
    public function pUser()
    {
        return $this->hasOne('member','member_id','p_id');
    }

    /**
     * 获取用户信息
     * @param $where
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($where)
    {
        return self::get($where);
    }


    /**
     * @Notes: 账户余额调整
     * @Interface recharge
     * @param $data [mode] inc 增加  dec 减少
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/8   2:37 下午
     */
    public function recharge($data)
    {
        if ($data['type']==1){
            return $this->updateAmount($data);
        }else {
            return $this->updateGlory($data);
        }
    }
    public function updateGlory($data)
    {
        $amount = '0';
        switch ($data['balance_type'])
        {
            case "1": //增加
                $amount = $data['price'];
                break;
            case "2": //减少
                $amount = -$data['price'];
                break;
        }
        $param = [
            'amount' => $amount,
            'remark' => date('Y-m-d H:i:s',time()).'后台充值',
            'content' => "后台调整,调整之前账户荣誉余额{$this['glory']}",
            'type' => '2',
            'member_id' => $this['member_id']
        ];
        return Glory::create($param);
    }
    /**
     * @Notes: 余额调整
     * @Interface updateAmount
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/1/8   2:42 下午
     */
    public function updateAmount($data)
    {
        $amount = '0';
        switch ($data['balance_type'])
        {
            case "1": //增加
                $amount = $data['price'];
                break;
            case "2": //减少
                $amount = -$data['price'];
                break;
        }
        $param = [
            'amount' => $amount,
            'remark' => date('Y-m-d H:i:s',time()).'后台充值',
            'content' => "后台调整用户账户余额,调整之前账户余额{$this['account']}",
            'type' => '1',
            'member_id' => $this['member_id']
        ];
        return Finance::create($param);
    }
    /**
     * 用户列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 17:24
     */
    public static function getMemberList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        if(isset($param['member_type'])){
            $where['member_type'] = $param['member_type'];
        }
        if (array_key_exists('name',$param)&&$param['name']!==''){
            $where['name']=['like','%'.$param['name']."%"];
        }
        if (array_key_exists('member_id',$param)&&$param['member_id']!==''){
            $where['member_id']=$param['member_id'];
        }
        if (array_key_exists('phone',$param)&&$param['phone']!==''){
            $where['phone']=$param['phone'];//手机号
        }
        if (array_key_exists('shiming',$param)&&$param['shiming']!==''){
            $where['real_status']=$param['shiming'];//实名
        }
        if (array_key_exists('status',$param)&&$param['status']!==''){
            $where['status']=$param['status'];//状态
        }
        if (array_key_exists('create_time',$param)&&$param['create_time']!==''){
            $time = explode('~',$param['create_time']);
            $where['create_time'] = ['between time',[trim($time[0]),trim($time[1])]];
        }
        if (array_key_exists('p_id',$param)&&$param['p_id']!==''){
            $where['p_id']=$param['p_id'];//状态
        }
        // 排序规则
        $sort = [];
        if(isset($param['type_sort'])){
            switch ($param['type_sort'])
            {
                case "100":
                    $sort = ['glory'=>'asc'];
                    break;
                case "101":
                    $sort = ['glory'=>'desc'];
                    break;
                case "102":
                    $sort = ['account'=>'asc'];
                    break;
                case "103":
                    $sort = ['account'=>'desc'];
                    break;
                case "104":
                    $sort = ['invitations_number'=>'asc'];
                    break;
                case "105":
                    $sort = ['invitations_number'=>'desc'];
                    break;
                default:
                    $sort = ['member_id'=>'desc'];
                    break;
            }
        }else{
            $sort = ['member_id'=>'desc'];
        }
        $data = self::where($where)
            ->order($sort)
            ->limit($offset,$limit)
            ->select();
        $blockchain = Setting::getItem('blockchain');
        foreach ($data as &$item){
            if($limit == '10000'){
                //查询用户收货地址 然后进行导出下
                $memberAddressInfo = (new MemberAddress())->where(['member_id'=>$item['member_id'],'select_status'=>1,'status'=>1])->find();
                if(!empty($memberAddressInfo)){
                    $item['address_name'] = $memberAddressInfo['name'];
                    $item['address_phone'] = $memberAddressInfo['phone'];
                    $item['address_info'] = $memberAddressInfo['region']['province'].$memberAddressInfo['region']['city'].$memberAddressInfo['region']['region'].$memberAddressInfo['detail'];
                }else{
                    $item['address_name'] = '暂无';
                    $item['address_phone'] = '暂无';
                    $item['address_info'] = '暂无';
                }
            }
            $member_chain = MemberChain::details(['member_id'=>$item['member_id']]);
            if($item['real_status']['value']==2){
                if ($blockchain['default'] == 'BD') {
                    $item['address'] = $member_chain['b_address'];
                }else if($blockchain['default'] == 'WC'){
                    $item['address'] = $member_chain['w_account'];
                }else if($blockchain['default'] == 'TH'){
                    $item['address'] = $member_chain['t_address'];
                }else{
                    $item['address'] = "--";
                }
            }else{
                $item['address'] = '--';
            }
            $item['status'] = $item['status']['value'];
            $item['real_status_text'] = $item['real_status']['text'];
            $item['gender_text'] = $item['gender']['text'];
            $item['from_type_text'] = $item['from_type']['text'];
            $item['p_name'] = $item['p_id']!=0?'('.self::detail(['member_id'=>$item['p_id']])['phone'].')':'--';
            $item['operate'] = showNewOperate(self::makeButton($item['member_id']));
        }

        $arr['data'] = $data;
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = self::where($where)->order($sort)->count();
        return  $arr;
    }
    /**
     * @Notes:修改用户上下级
     * @Interface updatePid
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/26   3:17 下午
     */
    public function updatePid($data)
    {

        if($data['p_id'] !=0){
            //搜索下当前输入的上级用户是否存在 存在则修改不存在不修改
            $member_info = $this->where(['member_id'=>$data['p_id']])->find();
            if(empty($member_info)){
                throw new BaseException(['msg' => '上级用户不存在,请重新输入用户ID']);
            }
        }
        return $this->where(['member_id'=>$this['member_id']])->update(['p_id'=>$data['p_id']]);
    }
    private static  function makeButton($id): array
    {
        return [
            '持有藏品' => [
                'href' => url('user.index/hold', ['member_id' => $id]),
                'lay-event' => '',
                'lay-type' => '1',
            ],
            '空投藏品' => [
                'href' => url('user.index/drop', ['member_id' => $id]),
                'lay-event' => '',
                'lay-type' => '1',
            ],
            '下级用户' => [
                'href' => url('user.index/getNodeList', ['member_id' => $id]),
                'lay-event' => '',
                'lay-type' => '1',
            ],
            '更多' => [
                'href' => "javascript:void(0)",
                'lay-event' => $id,
                'lay-type' => '2',
            ]
        ];
    }


    /**
     * 修改次数
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface updatePurchaseLimit
     * @Time: 2022/7/24   22:27
     */
    public function updatePurchaseLimit($param)
    {
       if($param['balance']['balance_type']==1){ //增加
           return $this->where(['member_id'=>$param['member_id']])->setInc("purchase_limit",$param['balance']['price']);
       }else{
           return $this->where(['member_id'=>$param['member_id']])->setDec("purchase_limit",$param['balance']['price']);
       }
    }

    /**
     * 用户列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 17:24
     */
    public static function getLeaderboard($param)
    {
        $where = [];
        $sort = ['invitations_number'=>'desc'];
        if (isset($param['type']) && $param['type'] == 'export') {
            $data = self::where($where)
                ->order($sort)
                ->field("member_id,name,phone,invitations_number")
                ->select();
        }else{
            $limit = $param['limit'];
            $offset = ($param['page'] - 1) * $limit;
            // 排序规则
            $data = self::where($where)
                ->order($sort)
                ->limit($offset,$limit)
                ->select();
        }

        $arr['data'] = $data;
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = self::where($where)->order($sort)->count();
        return  $arr;
    }


    /**
     * 消费排行榜
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-13 17:24
     */
    public static function getConsume($param)
    {
        $where = [];
        $sort = ['amount_spent'=>'desc'];
        if (array_key_exists('phone',$param)&&$param['phone']!==''){
            $where['phone']=$param['phone'];//手机号
        }
        if (isset($param['type']) && $param['type'] == 'export') {
            $data = self::where($where)
                ->order($sort)
                ->field("member_id,name,phone,amount_spent")
                ->select();
        }else{
            $limit = $param['limit'];
            $offset = ($param['page'] - 1) * $limit;
            // 排序规则
            $data = self::where($where)
                ->order($sort)
                ->limit($offset,$limit)
                ->select();
        }

        $arr['data'] = $data;
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = self::where($where)->order($sort)->count();
        return  $arr;
    }

    /**
     * 获取下级列表
     * @param $param
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-09-16 14:54
     */
    public function getChildData($param)
    {
        $page = isset($param['page'])?$param['page']:1;
        $limit = isset($param['limit'])?$param['limit']:20;
        $data = self::where(['p_id'=>$param['member_id']])->paginate($limit,false,['page'=>$page])->toArray();
        $arr['data'] = $data['data'];
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = $data['total'];
        return  $arr;
    }

}