<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/19   12:25
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\common\controller;

use app\common\components\helpers\StockUtils;
use app\common\model\App;
use app\common\model\Appointment;
use app\common\model\Goods;
use app\common\model\Member;
use app\common\model\MemberChain;
use app\common\model\MemberGoods;
use app\common\model\Purchase;
use app\common\model\Setting;
use chain\Driver as chainDriver;
use exception\BaseException;
use phpseclib3\File\ASN1\Maps\Time;
use think\Controller;

class Task extends Controller
{
    private $config;
    private $account;

    /**
     * 构造方法
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        // 区块链配置
        $this->config = Setting::getItem('blockchain');
        $info = (new App())::get(['app_id' => '10001']);
        if ($this->config['default'] == 'BD') {
            $this->account = [
                'address' => $info['b_address'],
                'private_key' => $info['b_private_key'],
                'public_key' => $info['b_public_key'],
            ];
        } else if($this->config['default'] == 'WC'){
            $this->account = [
                'address' => $info['w_account']
            ];
        }else{
            $this->account = [
                'address' => $info['t_address'],
                'userKey' => $info['t_userKey'],
                'member_id' => 0,
            ];
        }
    }

    /**
     * 手动创建
     * @param $member_id
     * @Time: 2022/6/29   17:03
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface testCreateAccount
     */
    public function testCreateAccount()
    {
        $member_id = $this->request->param('member_id');
        //判断是否实名认证
        $member_info = (new Member())->where(['member_id'=>$member_id])->find();
        if($member_info['real_status']['value']!=2){
            return json(['code'=>0,'msg'=>'开通账户失败,请先让用户实名认证']);
        }
        if($this->createAccount($member_id)){
            return json(['code'=>1,'msg'=>'成功']);
        }
        return json(['code'=>0,'msg'=>'失败']);
    }

    /**
     * 创建账户  百度、文昌、天河   【需要优化】
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface startInit
     * @Time: 2022/6/19   12:26
     */
    public function createAccount($is_user = 0)
    {
        if ($this->config['default'] != 'SL') {
            $ChainDriver = new chainDriver($this->config);
            if (!$result = $ChainDriver->createIssuedUser($is_user)) {
                self::doLogs(array_merge(['title'=>'创建账户失败--createAccount'], $result));
            }
        }
        //判断是否是平台创建的账户 是的话直接入平台信息  不是则写入另外的账户表
        if ($is_user == 0) {
            switch ($this->config['default'])
            {
                case "BD":
                    (new App())->where(['app_id' => '10001'])->update([
                        'b_address' => $result['address'],
                        'b_private_key' => $result['private_key'],
                        'b_public_key' => $result['public_key'],
                        'b_mnemonic' => $result['mnemonic'],
                    ]);
                    break;
                case "WC":
                    (new App())->where(['app_id' => '10001'])->update([
                        'w_account' => $result['data']['account'],
                        'w_name' => isset($result['data']['name']) ? $result['data']['name'] : $is_user,
                    ]);
                    break;
                case "TH":
                    (new App())->where(['app_id' => '10001'])->update([
                        't_address' => $result['address'],
                        't_userKey' => $result['userKey']
                    ]);
                    break;
            }
        } else {
            switch ($this->config['default'])
            {
                case "BD":
                    if (empty(MemberChain::details(['member_id' => $is_user]))) {
                        (new MemberChain())->createAccount([
                            'b_address' => $result['address'],
                            'b_private_key' => $result['private_key'],
                            'b_public_key' => $result['public_key'],
                            'b_mnemonic' => $result['mnemonic'],
                            'member_id' => $is_user
                        ]);
                    } else {
                        (new MemberChain())->where(['member_id' => $is_user])->update([
                            'b_address' => $result['address'],
                            'b_private_key' => $result['private_key'],
                            'b_public_key' => $result['public_key'],
                            'b_mnemonic' => $result['mnemonic'],
                        ]);
                    }
                    break;
                case "WC":
                    if (empty(MemberChain::details(['member_id' => $is_user]))) {
                        (new MemberChain())->createAccount([
                            'w_account' => $result['data']['account'],
                            'member_id' => $is_user,
                            'w_name' => isset($result['data']['name']) ? $result['data']['name'] : $is_user
                        ]);
                    } else {
                        (new MemberChain())->where(['member_id' => $is_user])->update([
                            'w_account' => $result['data']['account'],
                            'w_name' => isset($result['data']['name']) ? $result['data']['name'] : $is_user
                        ]);
                    }
                    break;
                case "TH":
                    if (empty(MemberChain::details(['member_id' => $is_user]))) {
                        (new MemberChain())->createAccount([
                            't_address' => $result['address'],
                            't_userKey' => $result['userKey'],
                            'member_id' => $is_user,
                        ]);
                    } else {
                        (new MemberChain())->where(['member_id' => $is_user])->update([
                            't_address' => $result['address'],
                            't_userKey' => $result['userKey']
                        ]);
                    }
                    break;
            }
        }
        return true;
    }

    /**
     * 创建文昌链的类别 【后台添加商品后发行创建】
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface wxCreateNfts
     * @Time: 2022/6/28   11:28
     */
    public function wxCreateNfts($goods_images = 'https://zsvideo.86itn.cn/20220622144915314fc9711.png',$goods_name = "蒙娜丽莎卢浮宫博物馆",$amount = '99993',$price = '3000.00',$desc= '八六数藏发行0001',$member_id=0,$goods_id=1)
    {
        if($member_id){
            $member_info = MemberChain::details(['member_id' => $member_id]);
            $this->account = [
                'address' => $member_info['w_account']
            ];
        }
        $ChainDriver = new chainDriver($this->config, $this->account);
        $Collection = [
            'thumb' => $goods_images, //封面图片
            'title' => $goods_name, //商品标题
            'amount' => $amount, //发行份数
            'price' => moneyToSave($price), //发行价格 //分
            'desc' => $desc,
            'goods_id' => $goods_id,
            'member_id' => $member_id
        ];
        $result = $ChainDriver->createAssets($Collection);
        self::doLogs(array_merge(['title'=>'文昌链创建NFT类别--wxCreateNfts'], $Collection));
        if($result['response']['errno']!=0){
            throw new BaseException(['code'=>'-1','msg' => '抱歉!发行失败']);
        }
        $asset_id = $result['response']['asset_id'];
        return [
            'code'=>'1',
            'data' => [
                'asset_id' => $asset_id,
            ]
        ];

    }


    /**
     * 创建资产 百度
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface createAssets
     * @Time: 2022/6/19   21:52
     */
    public function createAssets($goods_images = 'https://zsvideo.86itn.cn/20220622144915314fc9711.png',$goods_name = "蒙娜丽莎卢浮宫博物馆",$amount = '99993',$price = '3000.00',$desc= '八六数藏发行0001',$member_id=0,$goods_id=1)
    {
        if($member_id){
            $member_info = MemberChain::details(['member_id' => $member_id]);
            $this->account = [
                'address' => $member_info['w_account']
            ];
        }
        $ChainDriver = new chainDriver($this->config, $this->account);
        $Collection = [
            'thumb' => $goods_images, //封面图片
            'title' => $goods_name, //商品标题
            'amount' => $amount, //发行份数
            'price' => moneyToSave($price), //发行价格 //分
            'desc' => $desc,
            'goods_id' => $goods_id,
            'member_id' => $member_id
        ];
        if ($this->config['default'] == 'BD') {
            $result = $ChainDriver->createAssets($Collection);
            self::doLogs(array_merge(['title'=>'创建发行资产--createAssets'], $Collection));
            if($result['response']['errno']!=0){
                throw new BaseException(['code'=>'-1','msg' => '抱歉!发行失败']);
            }
            //创建成功之后进行发行资产数据
            $asset_id = $result['response']['asset_id'];
            $result_pull = $this->publishAssets($asset_id,$member_id,$Collection);
            if ($this->config['default'] == 'BD') {
                return [
                    'code'=>'1',
                    'data' => [
                        'asset_id' => $asset_id
                    ]
                ];
            }else{
                return [
                    'code'=>'1',
                    'data' => [
                        'asset_id' => $asset_id,
                        'operation_id' => $result_pull['response']['operation_id']
                    ]
                ];
            }
        }else{
            $goods_info = Goods::detail($goods_id);
            $result_pull = $this->publishAssets($goods_info['asset_id'],$member_id,$Collection);
            return [
                'code'=>'1',
                'data' => [
                    'asset_id' => $goods_info['asset_id'],
                    'operation_id' => $result_pull['response']['operation_id']
                ]
            ];
        }
    }

    /**
     * 资产发行 [百度、文昌统一使用]
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface publishAssets
     * @Time: 2022/6/19   22:48
     */
    public function publishAssets($asset_id = 'bl003',$member_id=1,$Collection)
    {
        if($member_id){
            $member_info = MemberChain::details(['member_id' => $member_id]);
            $this->account = [
                'address' => $member_info['w_account']
            ];
        }
        $ChainDriver = new chainDriver($this->config, $this->account);
        $result = $ChainDriver->publish($asset_id,$Collection);
        self::doLogs(array_merge(['title'=>'资产发行平台--publishAssets--'.$asset_id], $Collection));
        //查询详情
        if($result['response']['errno']!=0){
            throw new BaseException(['code'=>'-1','msg' => '抱歉!上链错误']);
        }
        return $result;
    }

    /**
     * 查询资产详情
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface assetsDetails
     * @Time: 2022/6/19   22:54
     */
    public function assetsDetails($asset_id = 'blnft2211')
    {
        $ChainDriver = new chainDriver($this->config, $this->account);
        return $ChainDriver->queryDetails($asset_id);
    }


    /**
     * 授予碎片操作
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface grantShards
     * @Time: 2022/6/20   14:13
     */
    public function grantShards($asset_id = 566007636037840550, $member_id = 50, $price = '3000')
    {
        $member_info = MemberChain::details(['member_id' => $member_id]);
        if ($this->config['default'] == 'BD') {
            $account = [
                'address' => $member_info['b_address'],
                'member_id' => intval($member_id)
            ];
        }
        $ChainDriver = new chainDriver($this->config, $this->account);
        $result = $ChainDriver->grant($account,$asset_id,intval(moneyToSave($price)));
        if($result['response']['errno']!=0){
            throw new BaseException(['code'=>'-1','msg' => '抱歉!授予失败']);
        }
        return [
            'code'=>'1',
            'data' => [
                'asset_id' => $result['response']['asset_id'],
                'shard_id' => $result['response']['shard_id'],
            ]
        ];
    }

    /**
     * 转移资产碎片--文昌--天河
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface addCollection
     * @Time: 2022/6/20   14:25
     *
     */
    public function addCollection($asset_id = 'blnft2211', $share_id = 'avatawhoozxytpetr0qvannplymztqi6', $member_id = 1, $to_member_id = 2)
    {
        $member_info = MemberChain::details(['member_id' => $member_id]);
        if($to_member_id == 0){
            if ($this->config['default'] == 'BD') {
                $account = [
                    'address' => $member_info['b_address'],
                    'private_key' => $member_info['b_private_key'],
                    'public_key' => $member_info['b_public_key'],
                ];
                $to_account = [
                    'address' =>  $this->account['address'],
                    'member_id' =>  0
                ];
            } else {
                $account = [
                    'address' => $member_info['w_account'],
                ];
                $to_account = [
                    'address' =>  $this->account['address'],
                ];
            }
        }else{
            $to_member_info = MemberChain::details(['member_id' => $to_member_id]);
            if ($this->config['default'] == 'BD') {
                $account = [
                    'address' => $member_info['b_address'],
                    'private_key' => $member_info['b_private_key'],
                    'public_key' => $member_info['b_public_key'],
                ];
                $to_account = [
                    'address' => $to_member_info['b_address'],
                    'member_id' => $to_member_info['member_id']
                ];
            } else {
                $account = [
                    'address' => $member_info['w_account'],
                ];
                $to_account = [
                    'address' => $to_member_info['w_account'],
                ];
            }
        }
        $ChainDriver = new chainDriver($this->config, $this->account);
        $result = $ChainDriver->transfer($account, $asset_id, $share_id, $to_account, $member_id);
        if($result['response']['errno']!=0){
            throw new BaseException(['code'=>'-1','msg' => '抱歉!上链错误']);
        }
        if ($this->config['default'] == 'BD') {
            return [
                'code'=>'1',
                'data' => [
                    'asset_id' => $asset_id,
                    'shard_id' => $share_id,
                ]
            ];
        } else {
            return [
                'code'=>'1',
                'data' => [
                    'asset_id' => $result['response']['asset_id'],
                    'shard_id' => $result['response']['operation_id'],
                ]
            ];
        }

    }


    /**
     * 查询用户持有的碎片列表  【废弃】
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getUserListsDsbyaddr
     * @Time: 2022/6/20   14:41
     */
    public function getUserListsDsbyaddr($member_id = 1)
    {
        $member_info = MemberChain::details(['member_id' => $member_id]);
        if ($this->config['default'] == 'BD') {
            $account = $member_info['b_address'];
        } else {
            $account = $member_info['w_account'];
        }
        $ChainDriver = new chainDriver($this->config, $this->account);
        $result = $ChainDriver->listShardsByAddr($account);
        pre($result);
    }

    /**
     * 查询自定的碎片地址  百度
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface querysds
     * @Time: 2022/6/20   15:08
     */
    public function querysds($asset_id = '613905833046159014', $share_id = '1055977694450265766')
    {
        $result = (new chainDriver($this->config, $this->account))->querysds($asset_id, $share_id);
        if($result['response']['errno']!=0){
            throw new BaseException(['code'=>'-1','msg' => '抱歉!上链错误']);
        }
        return [
            'code'=>'1',
            'data' => [
                'status' => $result['response']['meta']['status'],
                'tx_id' => $result['response']['meta']['tx_id'],
            ]
        ];
    }


    /**
     * 碎片销毁 用于合成后销毁藏品
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface consumeWrite
     * @Time: 2022/6/20   15:14
     */
    public function consumeWrite($asset_id = '613905833046159014', $share_id = '1055977694450265766', $member_id = 50)
    {
        $member_info = MemberChain::details(['member_id' => $member_id]);
        if ($this->config['default'] == 'BD') {
            $account = [
                'address' => $member_info['b_address'],
                'private_key' => $member_info['b_private_key'],
                'public_key' => $member_info['b_public_key'],
            ];
        } else {
            $account = [
                'address' => $member_info['w_account'],
            ];
        }
        self::doLogs(array_merge(['title'=>'销毁资产--consumeWrite--'.$asset_id.'-'.$share_id.'-'.$member_id]));
        $ChainDriver = new chainDriver($this->config, $this->account);
        $result = $ChainDriver->consume($asset_id, $share_id,$account);
        if($result['response']['errno']!=0){
            throw new BaseException(['code'=>'-1','msg' => '抱歉!销毁失败']);
        }
        return [
            'code'=>'1',
            'data' => [],
            'msg' => '销毁成功'
        ];
    }



    /**
     * 查询交易信息  文昌
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getWindUpDetails
     * @Time: 2022/6/21   14:54
     */
    public function getWindUpDetails($operation_id = '0x63ede61ec16358efd929b01d2aede9dcd0943cf45376c0ddab56483c720124c1')
    {
        $ChainDriver = new chainDriver($this->config, $this->account);
        $result = $ChainDriver->getWindUpDetails($operation_id);
        self::doLogs(array_merge(['title'=>'资产查询--getWindUpDetails--'.$operation_id],$result));
        if($result['response']['errno']!=0){
            throw new BaseException(['code'=>'-1','msg' => '抱歉!销毁失败']);
        }
        return [
            'code'=>'1',
            'data' => [
                'status' => $result['response']['data']['data']['status'],
                'tx_id' => $result['response']['data']['data']['tx_hash'],
                'nft_id' => isset($result['response']['data']['data']['nft_id'])?$result['response']['data']['data']['nft_id']:$result['response']['data']['data']['nft']['id'],
            ]
        ];
    }


    /**
     * 定时器执行
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface asyncCochain
     * @Time: 2022/6/22   19:46
     * common/Task/asyncCochain
     */
    public function asyncCochain()
    {
        (new MemberGoods())->asyncCochain();
    }




    /**
     * 计算预约的人数；为每个预约的人进行申购订单的创建
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface startSubscription
     * @Time: 2022/6/27   14:55
     * /common/task/startSubscription
     */
    public function startSubscription()
    {
        //获取已经可以抽签的藏品列表
        $goods_list = (new Goods())
            ->alias("a")
            ->join("goods_purchase b","a.goods_id = b.goods_id")
            ->where(['a.product_types'=>4,'draw_time'=>['<=',time()],'status'=>10])
            ->field("b.draw_time,a.goods_id,a.stock_num")
            ->select();
        if(empty($goods_list)){
            return true;
        }
        foreach ($goods_list as $key=>$val){
            $new_member_ids = [];
            //获取当前这个申购的预约列表是否有人预约
            $member_ids = (new Appointment())->where(['goods_id'=>$val['goods_id'],'status'=>0])->column('member_id');
            if(!empty($member_ids)){
                //判断预约的人数是否大于 发行的总数
                if(count($member_ids)>$val['stock_num'] && $val['stock_num']>0){
                    //预约人数发行的总数 需要随机获取发行的产品人数 然后创建订单
                    $new_member_ids_key = array_rand($member_ids,$val['stock_num']);
                    foreach ($new_member_ids_key as $k=>$v){
                        $new_member_ids[] = $member_ids[$v];
                    }
                    //获取没有中的
                    $missed_member_id = array_diff($new_member_ids,$member_ids);
                    (new Appointment())->where(['member_id'=>['in',$missed_member_id],'goods_id'=> $val['goods_id']])->update(['status'=>2]);
                    //获取多少个中的 然后减少库存
                    StockUtils::deductStock($val['goods_id'], count($new_member_ids));
                    (new Appointment())->where(['member_id'=>['in',$new_member_ids],'goods_id'=> $val['goods_id']])->update(['status'=>1]);
                }else{
                    $new_member_ids = $member_ids;
                    if($val['stock_num']!=0){
                        StockUtils::deductStock($val['goods_id'], count($new_member_ids));
                        (new Appointment())->where(['member_id'=>['in',$new_member_ids],'goods_id'=> $val['goods_id']])->update(['status'=>1]);
                    }else{
                        (new Appointment())->where(['member_id'=>['in',$new_member_ids],'goods_id'=> $val['goods_id']])->update(['status'=>2]);
                    }
                }
                //修改藏品的状态
                (new Purchase())->where(['goods_id'=>$val['goods_id']])->update(['status'=>20]);
            }
        }
    }


    /**
     * 天河连创建资产  [后台先去执行发行]  合约地址  0x5640f1e7a6e9f4585903db0629ed69c8c736a2af
     * @author: [Mr.Zhang] [1040657944@qq.com]  0x02ff327c27656a41d09e04a118067a3815e6d5ae
     * @Interface createPublish
     * @Time: 2022/8/11   23:580xce212fb78e37ed0ab035746671e2ca4fb8ec2679
     */
    public function createPublish($goods_name = "八六互联测试藏品",$amount = 1,$price = '3000.00',$member_id=0,$goods_id=1,$contract_address='',$base_token_id)
    {
        if($member_id !=0){
            $app_base_info = (new MemberChain())->where(['member_id' => $member_id])->find();
            $this->account = [
                'address' => $app_base_info['t_address'],
                'userKey' => $app_base_info['t_userKey']
            ];
        }else{
            $app_base_info = (new App())->where(['app_id' => '10001'])->find();
            $this->account = [
                'address' => $app_base_info['t_address'],
                'userKey' => $app_base_info['t_userKey']
            ];
        }
        $ChainDriver = new chainDriver($this->config, $this->account);
        $Collection = [
            'title' => $goods_name, //商品标题
            'amount' => $amount, //发行份数
            'price' => moneyToSave($price), //发行价格 //分
            'goods_id' => $goods_id,
            'member_id' => $member_id,
            'contract_address' => $contract_address,
            'base_token_id' => $base_token_id
        ];
        return $ChainDriver->createAssets($Collection);
    }

    /**
     * 天河链转增
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface grantTransferTh
     * @Time: 2022/8/12   16:42
     */
    public function grantTransferTh($from_id = 50,$to_member_id=46,$tokenId=4,$contractAddress='0xf9007198a2b2c1fab9fec1572dad4726e7697e5b')
    {
        $member_info = MemberChain::details(['member_id' => $from_id]);
        $account = $member_info['t_address'];
        $this->account = [
            'member_id' => $from_id,
            'userKey' =>$member_info['t_userKey']
        ];
        $to_member_info = MemberChain::details(['member_id' => $to_member_id]);
        $to_account = $to_member_info['t_address'];
        $ChainDriver = new chainDriver($this->config, $this->account);
        return $ChainDriver->transfer($account, $tokenId, $contractAddress, $to_account, '');
    }

    /**
     * 交易结果查询 天河
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getTransactionDetail
     * @Time: 2022/8/12   09:39
     */
    public function getTransactionDetail($operation_id='0x0f070e3d81b3e47be336a8c1b2c35fd50e130308e4f8f93b6f1b68609ac0a2bc',$methodName='transferFrom',$member_id=46)
    {
        $account = [];
        if($member_id !=0){
            $member_info = MemberChain::details(['member_id' => $member_id]);
            $account = [
                'address' => $member_info['t_address'],
                'userKey' => $member_info['t_userKey'],
                'member_id' => $member_info['member_id'],
                'methodName' => $methodName
            ];
        }else{
            $account = [
                'address' => $this->account['address'],
                'userKey' => $this->account['userKey'],
                'member_id' => 0,
                'methodName' => $methodName
            ];
        }
        return (new chainDriver($this->config,$account))->getWindUpDetails($operation_id);
    }


    /**
     * 销毁藏品  天河
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface destroyDigitalCollections
     * @Time: 2022/8/12   18:45
     */
    public function destroyDigitalCollections($member_id=46,$contractAddress='0xf9007198a2b2c1fab9fec1572dad4726e7697e5b',$tokenId=4)
    {
        if($member_id!=0){
            $member_info = MemberChain::details(['member_id' => $member_id]);
            $this->account = [
                'address' => $member_info['t_address'],
                'userKey' => $member_info['t_userKey'],
                'member_id' => $member_info['member_id'],
            ];
        }
        $ChainDriver = new chainDriver($this->config, $this->account);
        return $ChainDriver->consume($contractAddress, $tokenId,'');
    }





    //处理未授予成功的藏品重新授予
    public function testgetListShardsByAsset(){
        $goods_id = "32";
        $asset_id = "2003549710628008127";
        $list = $this->getListShardsByAsset($asset_id,1);
        $new_list =[];
        foreach ($list as $key=>$val){
            if($val['type'] == 2){
                $new_list[]= $val;
            }
        }
        //查询没有上链的交易藏品
        foreach($new_list as $key=>$val){
            $MemberChain = (new MemberChain())->where(['b_address'=>$val['to']])->find();
            //查询授予之前 判断当前的资产ID 是否存在未铸造成功的
            $MemberGoods = (new MemberGoods())->where(['member_id'=>$MemberChain['member_id'],'cast_status'=>1,'goods_id'=>$goods_id,'shard_id'=>['=','']])->find();
            if(!empty($MemberGoods)){
                $asset_id = $val['asset_id'];
                $shard_id = $val['shard_id'];
                $memberGoods_info = (new MemberGoods())->where(['id' => $MemberGoods['id'],'shard_id'=>$shard_id,'asset_id'=>$asset_id])->find();
                if(empty($memberGoods_info)){
                    (new MemberGoods())->where(['id' => $MemberGoods['id']])->update([
                        'asset_id' => $asset_id,
                        'shard_id' => $shard_id,
                        'cast_status' => 2,
                        'hash_url' => $val['tx_id']
                    ]);
                }

            }
        }
        echo "结束";

    }


    //处理未授予成功的藏品重新授予
    public function testBdgetListShardsByAsset(){
        $redis = initRedis();
        $goods_id = "49";
        $asset_id = "98060574826541247";
        $list = $this->getListShardsByAsset($asset_id,1);
        $new_list =[];
        foreach ($list as $key=>$val){
            if($val['type'] == 2){
                $new_list[]= $val;
            }
        }
        $new_array = [];
        //查询没有上链的交易藏品
        foreach($new_list as $key=>$val){
            //查询这个上链的hash  是否存在这个会员表
            $MemberGoods = (new MemberGoods())->where(['asset_id'=>$val['asset_id'],'shard_id'=>$val['shard_id']])->find();
            if(empty($MemberGoods)){
                $MemberChain = (new MemberChain())->where(['b_address'=>$val['to']])->find();
                $val['y_member_id'] = $MemberChain['member_id'];
                //重新进行转增操作
                $new_array[] = $val;
            }
        }
        $MemberGoodsList = (new MemberGoods())->where(['cast_status'=>1,'goods_id'=>$goods_id])->select();
        foreach ($MemberGoodsList as $key=>$val){
            $goods_info = $new_array[$key];
            $to_member_info = MemberChain::details(['member_id' => $val['member_id']]);

            $member_info = MemberChain::details(['member_id' => $goods_info['y_member_id']]);
            if ($this->config['default'] == 'BD') {
                $account = [
                    'address' => $member_info['b_address'],
                    'private_key' => $member_info['b_private_key'],
                    'public_key' => $member_info['b_public_key'],
                ];
                $to_account = [
                    'address' => $to_member_info['b_address'],
                    'member_id' => $to_member_info['member_id']
                ];
            }
            $ChainDriver = new chainDriver($this->config, $this->account);
            $result = $ChainDriver->transfer($account, $goods_info['asset_id'], $goods_info['shard_id'], $to_account, $member_id);
            if($result['response']['errno']==0){
                $asset_id = $goods_info['asset_id'];
                $operation_id = $goods_info['shard_id'];
                (new MemberGoods())->where(['id' => $val['id']])->update([
                    'asset_id' => $asset_id,
                    'operation_id' => $operation_id,
                    'shard_id' => $operation_id,
                ]);
                $redis->lpush('details_question_list', json_encode([
                    'member_goods_id' => $val['id'],
                    'asset_id' => $asset_id,
                    'operation_id' => $operation_id,
                ]));
            }
        }
        echo "结束";

    }


    /**
     * 分页拉取指定资产已授予碎片列表 【百度】
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getBdListAssetHistory
     * @Time: 2022/10/23   15:20
     */
    static public $new_list=array();

    public function getListShardsByAsset($asset_id,$cursor)
    {
        $ChainDriver = new chainDriver($this->config, $this->account);
        $list = $ChainDriver->getHistoryList($asset_id,$cursor);
        if(!empty($list['response']['list'])){
            $this->getListShardsByAsset($asset_id,$cursor+1);
            self::$new_list = array_merge(self::$new_list,$list['response']['list']);
        }
        return self::$new_list;
    }


    /**
     * @Notes: 记录日志错误日志的
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doLogs($values)
    {
        return write_log($values,RUNTIME_PATH.'/task');
    }

}