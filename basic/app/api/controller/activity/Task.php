<?php

namespace app\api\controller\activity;

use app\common\components\helpers\RedisUtils;
use app\common\controller\Controller;
use app\api\model\task\AwardSetting as AwardSettingModel;

/**
 * 签到奖励
 */
class Task extends Controller
{
    /**
     * 获取签到页面的信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.task/getTaskInfo)
     * @ApiReturnParams   (name="glory", type="string", description="积分")
     * @ApiReturnParams   (name="volume_drop", type="string", description="空投卷")
     * @ApiReturnParams   (name="sian_number", type="string", description="连续签到天数")
     * @ApiReturnParams   (name="today_task", type="string", description="每日任务数组 is_finish 1=已完成 0=未完成")
     * @ApiReturnParams   (name="task_list", type="string", description="任务列表")
     * @ApiReturnParams   (name="name", type="string", description="任务名称")
     * @ApiReturnParams   (name="glory", type="string", description="奖励积分")
     * @ApiReturnParams   (name="invite_num", type="string", description="邀请人数")
     * @ApiReturnParams   (name="invite", type="string", description="已经邀请人数")
     * @ApiReturnParams   (name="is_finish", type="string", description="1=已完成 0=未完成")
     */
    public function getTaskInfo()
    {
        $model = new AwardSettingModel();
        $memberInfo = $this->auth->getUser();
        $taskInfo = $model->getTaskInfo($memberInfo);
        $this->success($taskInfo);
    }


    /**
     * 签到
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/activity.task/sign)
     */
    public function sign()
    {
        $model = new AwardSettingModel();
        $memberInfo = $this->auth->getUser();
        $lockKey = "member:sign:member_id:{$memberInfo['member_id']}";
        if (RedisUtils::lock($lockKey, 60)) {
            $this->error('签到通道拥挤');
        }
        if ($model->sign($memberInfo)) {
            $this->success('签到成功',[]);
        }
        RedisUtils::unlock($lockKey);
        $this->error($model->getError() ?: '签到失败');
    }

}