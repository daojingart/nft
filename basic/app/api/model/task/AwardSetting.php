<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: liubinghong
 * +----------------------------------------------------------------------
 * | Time:  2022/6/17   15:55
 * +----------------------------------------------------------------------
 * | Email: 18368808324@163.com
 * +----------------------------------------------------------------------
 * | Created by PhpStorm.
 *
 * +----------------------------------------------------------------------
 */

namespace app\api\model\task;


use app\common\model\AwardRecord;
use app\common\model\AwardSetting as AwardSettingModel;
use app\admin\model\Setting;
use app\common\model\Glory;
use app\common\model\Member;

class AwardSetting extends AwardSettingModel
{
    /**
     * 获取任务信息
     * @param $params
     * @return array
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/17 16:12
     */
    public function getTaskInfo($memberInfo)
    {
        $returnArray = [];
        $returnArray['glory'] = intval($memberInfo['glory']);
        $returnArray['volume_drop'] = $memberInfo['volume_drop'];
        $returnArray['sian_number'] = $memberInfo['sian_number'];
        $value = Setting::getItem("todaytask");
        $sign_finish = 0;
        // 当天是否签到
        $signInfo = (new AwardRecord())
            ->where(['is_delete' => 0,'member_id' => $memberInfo['member_id']])
            ->whereTime('create_time','today')
            ->find();
        if (!empty($signInfo)) {$sign_finish = 1;}
        //判断是否是连续签到 是连续签到 会员表的数量+1
        // 每日任务
        $returnArray['today_task'] = [
            [
                'name' => '每日签到',
                'glory' => $value['sign_honor_num'],
                'is_finish' => $sign_finish ?? 0
            ],
            [
                'name' => '实名认证',
                'glory' => $value['honor_num'],
                'is_finish' => $memberInfo['real_status']['value'], // 会员是否实名认证
            ]
        ];

        // 每周任务  0//去完成  != 0已完成
        $awardList = $this->where(['is_delete' => 0])->select()->toArray();
        if (!empty($awardList)) {
            foreach ($awardList as $val) {
                $Glory = (new Glory())->where(['content'=>$val['award_setting_id'],'member_id'=>$memberInfo['member_id']])->find();
                $returnArray['task_list'][] = [
                    'name' => $val['name'],
                    'glory' => intval($val['honor_num']),
                    'invite_num' => $val['invite_num'],
                    'invite' => 0,
                    'is_finish' => empty($Glory)?0:1
                ];
            }
        }
        //获取最大的签到天数
        return $returnArray;
    }

    /**
     * 签到
     *
     * @param $memberInfo
     * @return false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/20 12:04
     * @author Mr.Liu
     */
    public function sign($memberInfo)
    {
        $signInfo = (new AwardRecord())
            ->where(['is_delete' => 0,'member_id' => $memberInfo['member_id']])
            ->whereTime('create_time','today')
            ->find();
        if (!empty($signInfo)) {
            $this->error = '今日已签到';
            return false;
        }
        $memberModel = new Member();
        $yesterday = strtotime(date("Y-m-d",strtotime("-1 day")));
        $number = 1;
        if($yesterday == $memberInfo['sian_number_time']){
            $number = $memberInfo['sian_number']+1;
        }

        $memberModel->where(['member_id'=>$memberInfo['member_id']])->update(['sian_number'=>$number,'sian_number_time'=>strtotime(date("Y-m-d"))]);
        $value = Setting::getItem("todaytask");
        $insertData = [
            'app_id' => self::$app_id,
            'member_id' => $memberInfo['member_id'],
            'type' => 1,
            'name' => '每日签到',
            'honor_num' => $value['sign_honor_num'],
            'remark' => '用户每日签到获取'
        ];
        $integralData = [
            'member_id' => $memberInfo['member_id'],
            'type' => 1,
            'amount' => $value['sign_honor_num'],
            'remark' => '每日签到'
        ];
        $res = (new Glory())->allowField(true)->save($integralData);
        if (!$res) {
            $this->error = '签到失败';
            return false;
        }
        return (new AwardRecord())->save($insertData);
    }

    /**
     * 获取任务列表
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getTaskList
     * @Time: 2022/6/24   18:12
     */
    public function getTaskList($refereeId)
    {
        $redis = initRedis();
        $redis_key = "resetLeaderboardData";
        $where = [];
        $where['p_id'] = $refereeId;
        if($redis->exists($redis_key)){
            $time = $redis->get("resetLeaderboardData");
            $where['create_time'] = ['>',$time];
        }
        //判断当前会员邀请的会员数量
        $member_count = (new Member())->where($where)->count();
        $this->startTrans();
        try{
            $award_data = $this->where(['is_delete'=>0,'invite_num'=>['<=',$member_count]])->order("award_setting_id desc")->find();
            $glory = (new Glory())->where(['member_id'=>$refereeId,'content'=>$award_data['award_setting_id']])->lock(true)->find();
            if (empty($glory) && !empty($award_data)){
                $integralData = [
                    'member_id' => $refereeId,
                    'type' => 1,
                    'amount' => $award_data['honor_num'],
                    'remark' => "完成{$award_data['name']}任务奖励",
                    'content' =>$award_data['award_setting_id']
                ];
                (new Glory())->allowField(true)->save($integralData);
            }
            // 提交事务
            $this->commit();
            return true;
        } catch (\Exception $e) {
            write_log($e, __DIR__);
            // 回滚事务
            $this->rollback();
            return false;
        }
    }

    /**
     * 实名认证 任务奖励发放
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface realtask
     * @Time: 2022/10/17   21:38
     */
    public function realtask($member_id)
    {
        $value = Setting::getItem("todaytask");
        if ($value['honor_num'] > 0) {
            $integralData = [
                'member_id' => $member_id,
                'type'      => 1,
                'amount'    => $value['honor_num'],
                'remark'    => '实名认证',
            ];
            (new Glory())->allowField(true)->save($integralData);
        }
        return true;
    }

}