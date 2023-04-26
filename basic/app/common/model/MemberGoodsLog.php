<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/9/7   23:33
 * +----------------------------------------------------------------------
 * | className: 藏品流转记录表
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

class MemberGoodsLog extends BaseModel
{
    protected $name = 'member_goods_log';

    /**
     * 插入藏品流转记录
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface insertData
     * @Time: 2022/9/7   23:34
     */
    public function insertData($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

    /**
     * 获取无线上级
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getWirelessSuperiorLog
     * @Time: 2022/9/7   23:56
     */
    public function getWirelessSuperiorLog($member_goods_id,$goods_id)
    {
       $member_goods_log_list = $this->where(['goods_id'=>$goods_id])->field("id,member_goods_od,p_member_goods_id,nickname,type,hash_url,create_time")->select()->toArray();
       $list = $this->get_top_pid($member_goods_log_list,$member_goods_id);
       foreach ($list as $key=>$value){
           $hash_url = $value['hash_url'];
           if(empty($value['hash_url'])){
               $member_goods_info = (new MemberGoods())->where(['id'=>$value['member_goods_od']])->field('hash_url')->find();
               $hash_url = $member_goods_info['hash_url'];
               $this->where(['id'=>$value['id']])->update(['hash_url'=>$member_goods_info['hash_url']]);
           }
           $list[$key]['hash_url'] = $hash_url;
           $list[$key]['type'] = $value['type']==1?"交易":"转增";
       }
       return $list;
    }

    /**
     * 获取无线上级
     * @param $cate
     * @param $id
     * @return array
     * @Time: 2022/9/7   23:58
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface get_top_pid
     */
    public function get_top_pid($cate,$id){
        $arr=array();
        foreach($cate as $v){
            if($v['member_goods_od']==$id){
                $arr[]=$v;// $arr[$v['id']]=$v['name'];
                $arr=array_merge($this->get_top_pid($cate,$v['p_member_goods_id']),$arr);
            }
        }
        return $arr;
    }
}