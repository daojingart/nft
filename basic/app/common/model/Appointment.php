<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/17   16:13
 * +----------------------------------------------------------------------
 * | className:  预约管理
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


use exception\BaseException;
use think\Db;
use think\Exception;


class Appointment extends BaseModel
{
    protected $name = 'appointment';

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
     * 提交预约
     * @param $goods
     * @param $member_id
     * @return bool|string
     * @throws BaseException
     * @Time: 2022/6/17   16:33
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface toAppointment
     */
    public function toAppointment($goods,$member_id)
    {
        // 判断预约时间是否
        Db::startTrans();
        try {
            self::insert([
                'member_id' => $member_id,
                'goods_id' => $goods['goods_id'],
                'create_time' => time(),
                'update_time' => time(),
                'app_id' => self::$app_id,
                'status' => 0
            ]);
            Db::commit();
            return true;
        }catch (Exception $e){
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 查询预约记录
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getList
     * @Time: 2022/6/24   16:50
     */
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        $where['goods_id'] = $param['goods_id'];
        $list = $this->where($where)
            ->order(['create_time' => 'desc'])
            ->limit($offset,$limit)
            ->select();
        foreach ($list as $key=>$val){
            $member_info = Member::detail(['member_id'=>$val['member_id']]);
            $list[$key]['nickName'] = $member_info['name'];
            $list[$key]['status'] = $this->getFindStatus($val['status']);
        }
        $return['count'] = $this->where($where)->count();
        $return['data'] = $list;
        $return['code'] = '0';
        $return['msg'] = 'OK';
        return $return;
    }

    /**
     * 获取状态
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getFindStatus
     * @Time: 2022/6/24   16:53
     * 预约状态  0抽签中  1已抽中 2未抽中
     */
    public function getFindStatus($status)
    {
        switch ($status)
        {
            case "0":
                return "抽签中";
            case "1":
                return "已抽中";
            case "2":
                return "未抽中";
        }
    }

}