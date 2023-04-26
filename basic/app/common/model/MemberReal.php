<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 13:50
 */


namespace app\common\model;


class MemberReal extends BaseModel
{

    /**
     * 插入实名认证信息
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface insertData
     * @Time: 2022/10/17   19:36
     */
    public function insertData($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

    /**
     *  获取认证详情
     * @param $data
     * @return MemberReal|null
     * @throws \think\exception\DbException
     * @Time: 2022/10/17   21:04
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getDetails
     */
    public function getDetails($data)
    {
        return self::get($data);
    }

    /**
     * 关联用户表
     * @return \think\model\relation\HasOne
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 13:50
     */
    public function member()
    {
        return $this->hasOne('member','member_id','member_id');
    }

    /**
     * 实名认证列表
     * @param $param
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/29   15:17
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getList
     */
    public static function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where= [];
        if(!empty($param['nickName'])){
            $where['name']=['like','%'.$param['nickName'].'%'];
        }
        if(!empty($param['phone'])){
            $where['phone']=$param['phone'];
        }
        if(!empty($param['status'])){
            $where['real_status']=$param['status'];
        }
        $data = self::where($where)
            ->order(['create_time' => 'desc'])
            ->with('member')
            ->limit($offset,$limit)
            ->select();
        foreach ($data as $k=>$item){
            if (empty($item['member'])){
                $member_info = (new Member())->where(['phone'=>$item['phone']])->find();
                $item['gender_text']=$member_info['gender']['text'];
                $item['member_id']=$member_info['member_id'];
                $item['phone']=$member_info['phone'];
                $item['nickName']=$member_info['name'];
                $item['real_status']=$member_info['real_status']['value'];
                $item['gender_text']=$member_info['gender']['text'];
                $item['real_status_text']=$member_info['real_status']['text'];
                self::where(['id'=>$item['id']])->update(['member_id'=>$member_info['member_id']]);
            }else{
                $item['gender_text']=$item['member']['gender']['text'];
                $item['member_id']=$item['member']['member_id'];
                $item['phone']=$item['member']['phone'];
                $item['nickName']=$item['member']['name'];
                $item['real_status']=$item['member']['real_status']['value'];
                $item['gender_text']=$item['member']['gender']['text'];
                $item['real_status_text']=$item['member']['real_status']['text'];
            }
        }
        $return['count'] =self::where($where)->order(['create_time' => 'desc'])->count();
        $return['data'] = $data;
        $return['code'] = '0';
        $return['msg'] = 'OK';
        return $return;
    }

    /**
     *  人工审核实名认证
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface humanReviewReal
     * @Time: 2022/10/17   21:08
     */
    public  function humanReviewReal($data)
    {
        $memberReal = $this->where('member_id', $data['member_id'])->find();
        if (!empty($memberReal)) {
            if($data['real_status']['value'] == 3){
                $this->where(['id'=>$memberReal['id']])->delete();
            }
            if($data['real_status']['value'] == 1){
                $this->error = "审核中,请勿重复提交";
                return false;
            }
        }
        return true;
    }
}