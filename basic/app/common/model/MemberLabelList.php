<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2021/12/17   11:04
 * +----------------------------------------------------------------------
 * | className: 标签会员关联表
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

class MemberLabelList extends BaseModel
{
    protected $name = 'member_label_list';

    /**
     * 添加标签关系表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface add
     * @Time: 2022/2/18   23:33
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function add($data)
    {
        if(empty($data['label'])){
            $this->error = "选择的标签为空";
            return false;
        }
        $new_data = [];
        foreach ($data['label'] as $key=>$val){
            //加标签 之前先删除这个会员所在的标签
            $this->where(['member_id'=>$data['member_id'],'lable_id'=>$val])->delete();
            $new_data[] = [
                'member_id' => $data['member_id'],
                'lable_id' => $val,
                'app_id' => self::$app_id
            ];
        }
        return $this->allowField(true)->saveAll($new_data);
    }

    /**
     * 获取标签ID
     * @param $field
     * @return array|false|string
     * @Time: 2022/2/18   23:41
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getLabelIds
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public function getLabelIds($field)
    {
        return $this->where($field)->column("lable_id");
    }


    /**
     * 自动贴入标签功能
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface automatic
     * @Time: 2022/8/14   13:57
     */
    public function automatic($member_label)
    {
        //查询持有藏品的会员ID
        $member_ids = (new MemberGoods())->where(['goods_status'=>['in',['0','1']],'is_synthesis'=>0,'is_donation'=>0,'goods_id'=>$member_label['goods_id']])->group("member_id")->column("member_id");
        $new_data = [];
        foreach ($member_ids as $key=>$val){
            //加标签 之前先删除这个会员所在的标签
            $this->where(['member_id'=>$val,'lable_id'=>$member_label['id']])->delete();
            $new_data[] = [
                'member_id' => $val,
                'lable_id' => $member_label['id'],
                'app_id' => self::$app_id
            ];
        }
        return $this->allowField(true)->saveAll($new_data);
    }

    /**
     * 表格导入标签
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface automatic
     * @Time: 2022/8/14   13:57
     */
    public function importLableMember($member_list,$label_id)
    {
        //查询持有藏品的会员ID
        $new_data = [];
        foreach ($member_list as $key=>$val){
            $member_info = (new Member())->where(['phone'=>$val['phone'],'is_del'=>1])->field("member_id,is_del")->find();
            if(empty($member_info)){
                continue;
            }
            //加标签 之前先删除这个会员所在的标签
            $lable_info = $this->where(['member_id'=>$member_info['member_id'],'lable_id'=>$label_id])->find();
            if(!empty($lable_info)){
                continue;
            }
            $new_data[] = [
                'member_id' => $member_info['member_id'],
                'lable_id' => $label_id,
                'app_id' => self::$app_id
            ];
        }

        return $this->allowField(true)->saveAll($new_data);
    }
}