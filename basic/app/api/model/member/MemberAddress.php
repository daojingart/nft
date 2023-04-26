<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 15:49
 */


namespace app\api\model\member;

use app\common\model\MemberAddress as MemberAddressModel;
use exception\BaseException;

/**
 * 用户地址业务
 * Class MemberAddress
 * @package app\api\model\member
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 15:49
 */
class MemberAddress extends MemberAddressModel
{
    /**
     * 添加地址
     * @param $member_id
     * @param $data
     * @return bool
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 15:50
     */
    public function add($member_id, $data)
    {
        if ($data['select_status']==1){
            $this->update(['select_status'=>0],['member_id'=>$member_id],true);
        }
        $region = explode(',', $data['region']);
        $this->allowField(true)->save(array_merge([
            'member_id' => $member_id,
            'app_id' => self::$app_id,
            'province_id' => $region[0],
            'city_id' => $region[1],
            'region_id' => $region[2],
        ], $data));
        return true;
    }

    /**
     * 编辑收货地址
     * @param $member_id
     * @param $data
     * @return bool
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 16:19
     */
    public function edit($member_id,$data)
    {
        if ($data['select_status']==1){
            $this->update(['select_status'=>0],['member_id'=>$member_id],true);
        }
        if (!$this->where('member_id',$member_id)->find()){
            //第一次直接默认
            $data['select_status'] = 1;
        }
        $region = explode(',', $data['region']);
        $arr = array_merge([
            'member_id' => $member_id,
            'app_id' => self::$app_id,
            'province_id' => $region[0],
            'city_id' => $region[1],
            'region_id' => $region[2],
        ], $data);

        $this->allowField(true)->update($arr,['address_id'=>$arr['id']],true);
        return true;
    }

    /**
     * 获取用户收货地址
     * @param $member_id 用户id
     * @return bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 16:21
     */
    public function getList($member_id)
    {
        return  $this->field("address_id,name,phone,province_id,city_id,region_id,detail,select_status")->where(['member_id'=>$member_id])->select();
    }

    /**
     * 删除用户地址
     * @param $memebr_id 用户id
     * @param $data
     * @return bool
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 16:35
     */
    public function del($memebr_id,$data)
    {
        $address = $this->where(['member_id'=>$memebr_id,'address_id'=>$data['id']])->find();
        if (!$address){
            throw new BaseException(['msg'=>'删除的地址不存在']);
        }
        if ($address->select_status==1){
            throw new BaseException(['msg'=>'不能删除默认地址请先更换']);
        }
        if ($address->delete()){
            return true;
        }
        return  false;
    }

    /**
     * 获取地址详情
     * @param $member_id
     * @param $param
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-27 14:17
     */
    public function MmeberAddressInfo($member_id,$data)
    {
        return  $this->field("address_id,name,phone,province_id,city_id,region_id,detail,select_status")->where(['member_id'=>$member_id,'address_id'=>$data['id']])->find();
    }

}