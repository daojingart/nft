<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: liubinghong
 * +----------------------------------------------------------------------
 * | Time:  2022/6/13   19:19
 * +----------------------------------------------------------------------
 * | Email: 18368808324@163.com
 * +----------------------------------------------------------------------
 * | Created by PhpStorm.
 *
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\task;


use app\common\model\AwardRecord;
use app\common\model\AwardSetting;
use Exception;

class Task extends AwardSetting
{

    /**
     * 奖励设置列表
     *
     * @param $params
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/13 19:31
     */
    public function getList($params)
    {
        $limit = isset($param['limit'])?$param['limit']:'10';
        $param['page'] = isset($param['page'])?$param['page']:'1';
        $offset = ($param['page'] - 1) * $limit;

        $where = [];
        $where['is_delete'] = 0;

        if (isset($params['name']) && !empty($params['name'])) {
            $where['name'] = ['like','%'.$params['name'].'%'];
        }

        $list = $this->where($where)
            ->limit($offset,$limit)
            ->order('award_setting_id desc')
            ->select();

        if (!empty($list)) {
            foreach ($list as &$val) {
                $val['operate'] = showNewOperate(self::makeButton($val['award_setting_id']));
            }
        }

        $return['count'] = $this->where($where)->count();
        $return['data'] = $list;
        $return['code'] = '0';
        $return['msg'] = 'ok';
        return $return;
    }

    /**
     * 奖励记录
     *
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/14 09:24
     * @author Mr.Liu
     */
    public function awardRecord($params)
    {
        $limit = isset($param['limit'])?$param['limit']:'10';
        $param['page'] = isset($param['page'])?$param['page']:'1';
        $offset = ($param['page'] - 1) * $limit;

        $where = [];
        $where['ar.is_delete'] = 0;

        if (isset($params['name']) && !empty($params['name'])) {
            $where['ar.name'] = ['like','%'.$params['name'].'%'];
        }
        if (isset($params['member_name']) && !empty($params['member_name'])) {
            $where['m.name'] = $params['member_name'];
        }
        if (isset($params['member_mobile']) && !empty($params['member_mobile'])) {
            $where['m.phone'] = $params['member_mobile'];
        }

        $list = (new AwardRecord())->alias('ar')
            ->join('snake_member m','ar.member_id= m.member_id','left')
            ->field('ar.*,m.name as member_name,m.phone')
            ->where($where)
            ->limit($offset,$limit)
            ->order('ar.award_record_id desc')
            ->select();
        $return['count'] = (new AwardRecord())->alias('ar')
            ->join('snake_member m','ar.member_id= m.member_id','left')
            ->field('ar.*,m.name as member_name,m.phone')
            ->where($where)
            ->count();
        $return['data'] = $list;
        $return['code'] = '0';
        $return['msg'] = 'ok';
        return $return;
    }

    /**
     * 方法说明
     *
     * @param $params
     * @return array
     * @throws Exception
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/14 14:09
     * @author Mr.Liu
     */
    public function delSetting($params)
    {
        if (!isset($params['award_setting_id']) || empty($params['award_setting_id'])) {
            throw new Exception('缺少比传参数');
        }
        $res = $this->where(['award_setting_id' => $params['award_setting_id']])->update([
            'is_delete' => 1
        ]);
        return [];
    }

    /**
     * 操作
     *
     * @param $award_setting_id
     * @return \string[][]
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/13 19:42
     */
    private static  function makeButton($award_setting_id)
    {
        return [
            '编辑' => [
                'href' => url('application.task/editAwardSetting', ['award_setting_id' => $award_setting_id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];

    }

    /**
     * 添加奖励设置
     *
     * @param $params
     * @return array
     * @throws Exception
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/13 20:47
     * @author Mr.Liu
     */
    public function addAwardSetting($params) : array
    {
        if (!isset($params['name']) || empty($params['name'])) {
            throw new Exception('缺少奖励名称');
        }
        if (!isset($params['invite_num']) || empty($params['invite_num'])) {
            throw new Exception('缺少邀请人数');
        }
        if (!isset($params['honor_num']) || empty($params['honor_num'])) {
            throw new Exception('缺少奖励荣誉值');
        }
        $params['app_id'] = self::$app_id;
        $res = (new AwardSetting())->save($params);
        if (!$res) {
            throw new Exception('添加失败');
        }
        return [];
    }

    /**
     * 编辑
     *
     * @param $params
     * @return array
     * @throws Exception
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/14 15:36
     * @author Mr.Liu
     */
    public function editAwardSetting($params) : array
    {
        if (!isset($params['award_setting_id']) || empty($params['award_setting_id'])) {
            throw new Exception('缺少奖励名称');
        }
        if (!isset($params['name']) || empty($params['name'])) {
            throw new Exception('缺少奖励名称');
        }
        if (!isset($params['invite_num']) || empty($params['invite_num'])) {
            throw new Exception('缺少邀请人数');
        }
        if (!isset($params['honor_num']) || empty($params['honor_num'])) {
            throw new Exception('缺少奖励荣誉值');
        }
        $res = (new AwardSetting())->where(['award_setting_id' => $params['award_setting_id']])->update(
            $params
        );
        if (!$res) {
            throw new Exception('添加失败');
        }
        return [];
    }

    /**
     * 获取设置信息
     *
     * @param $award_setting_id
     * @return array|bool|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/14 15:31
     * @author Mr.Liu
     */
    public function getInfo($award_setting_id)
    {
        return (new AwardSetting())->where(['award_setting_id' => $award_setting_id])->find();
    }

    /**
     * 每日任务
     *
     * @param $params
     * @return array
     * @throws Exception
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/14 18:47
     * @author Mr.Liu
     */
    public function todayTask($params)
    {
        if (!isset($params['sign_honor_num']) || empty($params['sign_honor_num'])) {
            throw new Exception('缺少每日签到设置');
        }
        if (!isset($params['honor_num']) || empty($params['honor_num'])) {
            throw new Exception('缺少实名认证设置');
        }
        return [];
    }

}