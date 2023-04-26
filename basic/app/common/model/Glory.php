<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/14   16:55
 * +----------------------------------------------------------------------
 * | className:  荣誉值流水
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


class Glory extends BaseModel
{
    protected $name = 'glory_log';


    /**
     * 获取荣誉值流水列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   16:54
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGloryList
     */
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;


        $where = [];
        if (array_key_exists('name',$param)&&$param['name']!==''){
            $where['name']=['like','%'.$param['name']."%"];
        }
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
     * 获取荣誉值流水列表
     * @param $param
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   16:54
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGloryList
     */
    public function gloryRecode($memberInfo)
    {
        $where = [];
        $where['member_id'] = $memberInfo['member_id'];
        $dataList = $this->where($where)
            ->order(['id' => 'desc'])
            ->select()->toArray();

        foreach ($dataList as $key => $value){
            $dataList[$key]['amount'] = intval($value['amount']);
            // 收支类型
            if($value['amount'] > 0){
                $dataList[$key]['amount_type'] = '收入';
            }else{
                $dataList[$key]['amount_type'] = '支出';
            }
        }
        return $dataList;
    }
}