<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/1/5   19:54
 * +----------------------------------------------------------------------
 * | className:订单回调日志处理
 * +----------------------------------------------------------------------
 */

namespace app\queue\controller;

use app\common\helpers\Tools;
use app\common\model\JobError;
use app\notice\model\Order;
use think\queue\Job;
use think\Db;

class OrderJob
{
    public function fire(Job $job, $data)
    {
        Tools::show_msg('订单回调任务开始执行执行时间：'. json_encode($data, 320));
        $orderModel = new Order();
        Db::startTrans();
        try {
            //执行具体的空投任务
            $orderModel->processTasksJob($data);
            Db::commit();
            $job->delete();
            Tools::show_msg('任务结束');
        } catch (\Exception $e) {
            Tools::show_msg('任务出错' . $e->getMessage());
            if ($job->attempts() > 3) {
                JobError::create([
                    'type' => 'callback_order',
                    'content' => json_encode($data),
                    'error_msg' => $e->getMessage()
                ]);
                //通过这个方法可以检查这个任务已经重试了几次了
                $job->delete();
            } else {
                // 也可以重新发布这个任务
                $job->release(); //$delay为延迟时间
            }
            Db::rollback();
        }
    }

    public function failed($data)
    {
        // ...任务达到最大重试次数后，失败了
    }

}