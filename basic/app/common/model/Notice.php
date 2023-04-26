<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/2/19   4:04 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 通知、公告
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class Notice extends BaseModel
{
    protected $name = 'notice';
    protected static $disabled = '-1'; //软删除数据

    /**
     * 详情
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($id)
    {
        return self::get($id);
    }

    /**
     * 获取用户信息列表
     * @param $member_id
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-21 09:14
     */
    public static function NoticeList($member_id,$type,$page)
    {
        $list = request()->param('limit')?:10;//每页显示几条
        $data = self::order('id desc')->field("content,id,create_time,status,type,title")->where(['member_id'=>$member_id,'type'=>$type])->paginate($list,false,$config = ['page'=>$page])->toArray();
        return $data['data'];
    }

    /**
     * 系统通知
     *
     * @return mixed
     * @throws \think\exception\DbException
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/7/11 15:04
     */
    public static function list($category_id,$page)
    {
        $where = [];
        $where['type'] = 2;
        $where['disabled'] = 10;
        $where['category_id'] = $category_id;

        $list = 15;//每页显示几条
        $data = self::order('id desc')
            ->field("id,create_time,title,desc,author,image_url")
            ->where($where)
            ->order(['sort' => 'asc'])
            ->paginate($list,false,$config = ['page'=>$page])
            ->toArray();
        if (!empty($data['data'])) {
            foreach ($data['data'] as &$val) {
                //判断是否存在 图片不存在则使用默认图片展示
                if (empty($val['image_url'])) {
                    $val['image_url'] = base_url() . 'assets/default.png';
                }
                if(!$val['author']){
                    $val['author'] = '作者：平台';
                }
            }
        }
        return $data['data'];
    }

    /**
     * 系统通知
     *
     * @return mixed
     * @throws \think\exception\DbException
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/7/11 15:04
     */
    public static function foreignlist()
    {
        $data = self::order('id desc')
            ->field("content,id,title")
            ->where(['type' => 2,'disabled' => 10])->select();
        $new_data = [];
        foreach ($data as $key=>$val) {
            $new_data[$key]['content'] = $val['title'];
            $new_data[$key]['link_url'] = base_url()."h5/h5.html#/pagesA/index/gonggao_detail?id=".$val['id'];
        }
        return $new_data;
    }

    /**
     * 批量发送站内信
     * @param $param
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-22 10:34
     */
    public function send($param)
    {
        //查找标签用户
        $member_list = MemberLabelList::where('lable_id',$param['label_id'])->field('lable_id,member_id')->select()->toArray();
        //拼接站内信
        $notice_data = [];
        $title = $param['mail']['title'];
        $content = $param['mail']['content'];
        foreach ($member_list as $item){
            $arr           = [
                'member_id' => $item['member_id'],
                'type' => 1,
                'title' => $title,
                'content' => $content,
                'create_time' => time(),
                'update_time' => time(),
            ];
            $notice_data[] = $arr;
        }
        if ($this->saveAll($notice_data)){
            return true;
        }
        return  false;
    }
}