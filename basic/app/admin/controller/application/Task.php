<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: liubinghong
 * +----------------------------------------------------------------------
 * | Time:  2022/6/11   11:33
 * +----------------------------------------------------------------------
 * | Email: 18368808324@163.com
 * +----------------------------------------------------------------------
 * | Created by PhpStorm.
 *
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\application;

use app\admin\controller\Controller;
use app\admin\model\Setting as SettingModel;
use app\admin\model\task\Task as TaskModel;
use app\common\model\Member;
use Exception;

class Task extends Controller
{
    /**
     * 奖励设置列表
     *
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/13 19:18
     */
    public function index()
    {
        if (false == $this->request->isAjax()) {
            return $this->fetch('index');
        }
        try {
            $params = $this->get();
            $result = (new TaskModel())->getList($params);
            return json($result);
            //return $this->renderSuccess('获取成功', '', $result);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * 添加奖励设置
     *
     * @return array|mixed
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/13 20:38
     * @author Mr.Liu
     */
    public function addAwardSetting()
    {
        if (false == $this->request->isAjax()) {
            return $this->fetch('add');
        }
        try {
            $params = $this->post();
            $result = (new TaskModel())->addAwardSetting($params);
            return $this->renderSuccess('添加成功', url('index'), $result);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * 添加奖励设置
     *
     * @return array|mixed
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/13 20:38
     * @author Mr.Liu
     */
    public function editAwardSetting()
    {
        if (false == $this->request->isAjax()) {
            $award_setting_id = $this->request->param("award_setting_id");
            $info = (new TaskModel())->getInfo($award_setting_id);
            return $this->fetch('edit',compact('info'));
        }
        try {
            $params = $this->post();
            $result = (new TaskModel())->editAwardSetting($params);
            return $this->renderSuccess('编辑成功', url('index'), $result);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * 删除
     *
     * @return array
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/14 15:23
     * @author Mr.Liu
     */
    public function delSetting()
    {
        try {
            $params = $this->get();
            $result = (new TaskModel())->delSetting($params);
            return $this->renderSuccess('删除成功', url('index'), $result);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * 奖励记录
     *
     * @return array|mixed
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/13 20:29
     * @author Mr.Liu
     */
    public function awardRecord()
    {
        if (false == $this->request->isAjax()) {
            return $this->fetch('record');
        }
        try {
            $params = $this->get();
            $result = (new TaskModel())->awardRecord($params);
            return json($result);
            //return $this->renderSuccess('获取成功', '', $result);
        } catch (Exception $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * 每日任务
     *
     * @return array|mixed
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/14 16:37
     * @author Mr.Liu
     */
    public function todayTask($key = 'todaytask')
    {
        if (!$this->request->isAjax()) {
            $values = SettingModel::getItem($key);
            return $this->fetch($key, compact('values'));
        }
        $model = new SettingModel;
        if ($model->edit($key, $this->postData($key))) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失败');
    }

    /**
     * 拉新排行榜重置
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface resetLeaderboardData
     * @Time: 2022/9/2   17:20
     */
    public function resetLeaderboardData()
    {
        $redis = initRedis();
        //记录重置的时间
        (new Member())->where(['app_id'=>'10001'])->update(['invitations_number'=>0]);
        (new Member())->where(['app_id'=>'10001'])->update(['moon_invitations_number'=>0]);
        (new Member())->where(['app_id'=>'10001'])->update(['week_invitations_number'=>0]);
        $redis->set("resetLeaderboardData", time());
        return $this->renderSuccess('重置成功');
    }



}