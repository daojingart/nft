<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2021/12/17   10:44
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\user;

use app\common\model\MemberLabel as MemberLabelModel;
use app\common\model\MemberLabelList;
use think\Db;
class MemberLabel extends MemberLabelModel
{
    /**
     * @Notes:
     * @Interface getList
     * @param $param
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException|\think\Exception
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/11/21   03:13
     */
    public function getList($param): array
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        if (array_key_exists('label_title',$param)&&!empty($param['label_title'])){
            $where['label_title'] =['like','%'.$param['label_title'].'%'];
        }
        $list = $this
            ->where($where)
            ->limit($offset,$limit)
            ->select()
            ->toArray();
        foreach ($list as $k=>$v){
            if($v['label_type'] == 1){$label_type = "手动标签";}else{$label_type = "自动标签";}
            $list[$k]['label_type'] = $label_type;
            $list[$k]['condition'] = '--';
            $list[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
            $list[$k]['count'] = MemberLabelList::where('lable_id',$v['id'])->count();
        }
        $return['count'] = $this->where($where)->count();
        $return['data'] = $list;
        $return['code'] = '0';
        $return['msg'] = 'ok';
        return $return;
    }


    /**
     * @Notes: 操作按钮
     * @Interface makeButton
     * @param $id
     * @return array
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   6:41 下午
     * @author: Mr.Zhang
     */
    private static  function makeButton($id): array
    {
        return [
            '查看会员列表' => [
                'href' => url('user.label/member', ['id' => $id]),
                'lay-event' => '',
                'lay-type' => '1',
            ],
            '编辑' => [
                'href' => url('user.label/edit', ['id' => $id]),
                'lay-event' => '',
                'lay-type' => '1',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
                'lay-type' => '1',
            ]
        ];
    }

    /**
     * 添加标签
     * @param array $data
     * @param array $distribution
     * @param array $team
     * @return bool
     */
    public function add(array $data)
    {
        if (!isset($data['label_title']) || empty($data['label_title'])) {
            $this->error = '请先填写标签名称';
            return false;
        }
        if($data['label_type']==2){
            if(!$data['goods_id']){
                $this->error = '请先选择藏品';
                return false;
            }
        }
        $data['app_id'] = self::$app_id;
        // 开启事务
        Db::startTrans();
        try {
            // 添加标签属性
            $this->allowField(true)->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑标签
     * @param array $data
     * @param array $distribution
     * @param array $team
     * @return bool
     */
    public function edit(array $data)
    {
        if (!isset($data['label_title']) || empty($data['label_title'])) {
            $this->error = '请先填写标签名称';
            return false;
        }
        if($data['label_type']==2){
            if(!$data['goods_id']){
                $this->error = '请先选择藏品';
                return false;
            }
        }

        $data['app_id'] = self::$app_id;

        // 开启事务
        Db::startTrans();
        try {
            // 添加标签属性
            $this->allowField(true)->where('id',$data['id'])->update($data);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    public function remove($id)
    {
        //同步删除这个标签的会员
        (new MemberLabelList())->where(['lable_id'=>$id])->delete();
        return $this->allowField(true)->where('id',$id)->delete();
    }



    /**
     * @Notes: 获取标签的会员列表
     * @Interface getList
     * @param $param
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException|\think\Exception
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/11/21   03:13
     */
    public function getMemberList($param): array
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        $where['lable_id'] = $param['label_id'];
        if (!empty($param['name'])) {
            $where['name'] = ['like', '%' . $param['name'] . '%'];
        }
        if (!empty($param['phone'])) {
            $where['phone'] = $param['phone'];
        }
        $list = (new MemberLabelList())->alias("a")->join("member b","a.member_id = b.member_id")->where($where)->limit($offset,$limit)->select();
        foreach ($list as $k=>$v){
            $list[$k]['operate'] = showNewOperate(self::makeLabelButton($v['id']));
        }

        $return['count'] = (new MemberLabelList())->alias("a")->join("member b","a.member_id = b.member_id")->where($where)->count();
        $return['data'] = $list;
        $return['code'] = '0';
        $return['msg'] = 'ok';
        return $return;
    }

    /**
     * @Notes: 操作按钮
     * @Interface makeButton
     * @param $id
     * @return array
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   6:41 下午
     * @author: Mr.Zhang
     */
    private static  function makeLabelButton($id): array
    {
        return [
            '移除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
                'lay-type' => '1',
            ]
        ];
    }

    /**
     * 删除标签里面的会员
     * @param $id
     * @return false|int
     * @Time: 2022/7/22   11:37
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface memberRemove
     */
    public function memberRemove($id)
    {
        return (new MemberLabelList())->where('id',$id)->delete();
    }



}