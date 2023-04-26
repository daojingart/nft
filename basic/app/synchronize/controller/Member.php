<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/3/23   21:43
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\synchronize\controller;


use app\synchronize\model\MemberWx;
use think\Config;
use think\Db;
use think\Request;
use app\common\controller\Task;


class Member extends BaseController
{

    /**
     * 同步会员表的数据
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2022/3/23   21:45
     * php index.php synchronize/Member/index
     */
    public function index()
    {
        //查询出来所有的会员列表 然后根据这个会员 是否实名认证 认证过 下级没有就直接删除
        $member_list = Db::name("user")->field("id,pid1,phone,username,nickname,balance,status,create_at,t_address,invite_code,card,is_cert,t_PrivateKey,deleted")->order("id asc")->select();
        // pre($member_list);
        $count_number = count($member_list);
        echo "---执行开始---累计总数--{$count_number}";
        $nbumber = 0;
        foreach ($member_list as $key=>$val){
            echo "\n";
            echo "---正在执行第--{$nbumber}--次";
            //判断用户是否实名认证
            if($val['is_cert']==1){
                //组装实名认证的信息
                $this->db_app->name("member_real")->insert([
                    'member_id' => $val['id'],
                    'name' => $val['username'],
                    'phone' => $val['phone'],
                    'update_time' => time(),
                    'app_id' => '10001',
                    'create_time' => time(),
                    'status' =>'1',
                    'card' => $val['card']
                ]);
                //创建区块链账号地址
                $this->db_app->name("member_chain")->insert([
                    'member_id' => $val['id'],
                    'update_time' => time(),
                    'app_id' => '10001',
                    'create_time' => time(),
                    't_address' =>  $val['t_address'],
                    't_userKey' => $val['t_PrivateKey']
                ]);
            }
            //录入会员信息
            $this->db_app->name("member")->insert([
                'member_id' => $val['id'],
                'phone' => $val['phone'],
                'name' => $val['nickname'],
                'avatarUrl' => 'https://art.taijishucang.com/assets/touxiang.png',
                'p_id' => $val['pid1'],
                'create_time' => strtotime($val['create_at']),
                'update_time' => time(),
                'code' => $val['invite_code'],
                'account' => $val['balance'],
                'status' => $val['status'],
                'real_status' => $val['is_cert']==1?2:0,
                'app_id' => '10001',
                'is_del' => $val['deleted']==0?1:0
            ]);
            $nbumber++;
        }
        echo "---执行完毕---";
    }



    /**
     * 更新账户荣誉值 赠送
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2022/3/23   21:45
     * php index.php synchronize/Member/updateHonor
     */
    public function updateHonor()
    {
        //查询出来所有的会员列表 然后根据这个会员 是否实名认证 认证过 下级没有就直接删除
        $member_list = $this->db_app->name("member")->where(['real_status'=>2])->field("member_id,p_id")->select();
        $count_number = count($member_list);
        echo "---执行开始---累计总数--{$count_number}";
        $nbumber = 0;
        //获取荣誉值信息 然后根据这个信息计算赠送的荣誉值
        foreach ($member_list as $key=>$val){
            echo "\n";
            echo "---正在执行第--{$nbumber}--次";
            //查询下级用户量的总数据 然后计算任务值 赠送荣誉积分
            $count_number = $this->db_app->name("member")->where(['p_id'=> $val['member_id'],'real_status'=>2])->count();
            //判断这个邀请总人数到了有没有出现没有赠送的荣誉值；判断赠送的荣誉值
            $award_setting_id = $this->db_app->name("award_setting")->where(['invite_num'=>['<=',$count_number],'is_delete'=>0])->column('award_setting_id');
            $y_award_setting_id = $this->db_app->name("glory_log")->where(['content'=>['<>',''],'member_id'=>$val['member_id'],'type'=>1])->column('content');
            $diff_id = array_diff($award_setting_id, $y_award_setting_id);

            if(!empty($diff_id)){
                foreach ($diff_id as $ke=>$va){
                    $award_setting_info = $this->db_app->name("award_setting")->where(['award_setting_id'=>$va])->field("name,honor_num,award_setting_id")->find();

                    $glory_log_info = $this->db_app->name("glory_log")->where(['member_id'=>$val['member_id'],'content'=>$award_setting_info['award_setting_id']])->find();
                    if(empty($glory_log_info)){
                        $integralData = [
                            'member_id' => $val['member_id'],
                            'type' => 3,
                            'amount' => $award_setting_info['honor_num'],
                            'remark' => "完成{$award_setting_info['name']}任务奖励",
                            'content' =>$award_setting_info['award_setting_id'],
                            'app_id' => '10001',
                            'create_time' => time(),
                            'update_time' => time(),
                        ];
                        $this->db_app->name("glory_log")->insert($integralData);
                        //  $diff_id = implode(',', $diff_id);
                        self::doLogs("---会员ID---{$val['member_id']}--邀请人数--{$count_number}--为奖励ID-{$award_setting_info['award_setting_id']}");
                    }
                }

            }
            $nbumber++;
        }
        echo "---执行完毕---";

    }




    /**
     * 同步藏品上链数据库
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2022/3/23   21:45
     * php index.php synchronize/Member/memberGoods
     */
    public function memberGoods()
    {   
        $goods_list = $this->db_app->name("goods")->where(['goods_id'=>['<>',10]])->select();
        foreach ($goods_list as $k=>$va){
             $member_list = Db::name("users_goods")->where(['goods_id'=>$va['goods_id'],'status'=>'1','is_del'=>0,'is_forge'=>0])->field("uid,goods_id,price,status,goods_num,sales_time,create_time,is_forge,is_source,contract,nft_id,task_id")->select();
            $sum_count = count($member_list);
            $number = 0;
            foreach ($member_list as $key=>$val){
                  $goodsInfo = $this->db_app->name("goods")->where(['goods_id'=>$val['goods_id']])->find();
                  //获取用户的手机号
                  $member_info = $this->db_app->name("member")->where(['member_id'=>$val['uid']])->find();
                  if(!empty($member_info)){
                      $goodsData = [
                        'member_id' => $val['uid'],
                        'order_id' => '',
                        'order_no' => '',
                        'goods_id' => $val['goods_id'],
                        'phone' => $member_info['phone'],
                        'nickname' => $member_info['name'],
                        'goods_no' => $goodsInfo['goods_no'],
                        'goods_name' => $goodsInfo['goods_name'],
                        'goods_thumb' => $goodsInfo['goods_thumb'],
                        'goods_price' => $goodsInfo['goods_price'],
                        'total_num' => 1,
                        'writer_id' => $goodsInfo['writer_id'],
                        'writer_name' => $goodsInfo['writer']['name'] ?? '',
                        'hash_url' => '',
                        'source_type' => $goodsInfo['product_types'],
                        'app_id' => '10001',
                        'goods_status' => 0,
                        'asset_id' => $val['nft_id'],
                        'shard_id' => $val['nft_id'],
                        'operation_id' => $val['task_id'],
                        'hash_url' => $val['contract'],
                        'cast_status' => '2',
                        'cast_time' => time(),
                        'create_time' => strtotime($val['create_time']),
                        'update_time' => time(),
                        'collection_number' => $val['goods_num']
                    ];
                    $number ++;
                    $this->db_app->name("member_goods")->insert($goodsData);
                    echo "\n";
                    echo "----插入成功------".$number.'---条--总'.$sum_count;
                   
                  }
            }
        }

       
     
    }

    /**
     * 同步藏品数据
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2022/3/23   21:45
     * php index.php synchronize/Member/memberGoods
     */
    public function goods()
    {
        //改下 这个藏品同步的逻辑
        $users_goods = Db::name("goods_users")->where(['state'=>1])->group("goods_id")->field("goods_id")->select();
        $sum_count = count($users_goods);
        $number = 0;
        foreach ($users_goods as $k=>$va){
            //查询当前藏品是否存在 存在则不添加
            $goods_info = Db::name("goods")->where(['goods_id'=>$va['goods_id']])->find();
            if(!empty($goods_info)){
                $goodsData = [
                    'goods_id' => $goods_info['id'],
                    'goods_name' => $goods_info['name'],
                    'goods_no' => $goods_info['id'],
                    'goods_price' => $goods_info['price'],
                    'content' => $goods_info['title'],
                    'category_id' =>1,
                    'writer_id' => 1,
                    'goods_thumb' => "https://ly.goidea.top".$goods_info['image'],
                    'create_time' => time(),
                    'update_time' => time(),
                    'product_types' => 1,
                    'app_id' => '10001',
                    'is_del' => 0,
                    'goods_status' => 20,
                    'goods_sort' => 0,
                    'd_images' => "https://ly.goidea.top".$goods_info['images'],
                    'start_time' => strtotime($goods_info['start_time']),
                    'issue_name' => $goods_info['creator'],
                    'issue_tag' => '藏品',
                    'goods_type' => 3
                ];
                $number ++;
                $this->db_app->name("goods")->insert($goodsData);
                echo "\n";
                echo "----插入成功------".$number.'---条--总'.$sum_count;
            }

        }
    }
    
    /**
     * @Notes: 制作邀请码
     * @Interface make_coupon_card
     * @return string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/24   6:05 下午
     */
    public function makeCouponCard() {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        return  $d;
    }
    
    
      /**
     * @Notes: 记录日志
     * @Interface doLogs
     * @param $values
     * @return bool|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/29   10:47 上午
     */
    protected static function doLogs($values)
    {
        return write_log($values,__DIR__);
    }

}