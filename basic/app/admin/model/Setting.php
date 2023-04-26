<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 系统配置模型
 * +----------------------------------------------------------------------
 */
namespace app\admin\model;

use app\common\model\Setting as SettingModel;
use think\Cache;


class Setting extends SettingModel
{
    /**
     * 设置项描述
     * @var array
     */
    private $describe = [
        'store' => '系统基础配置',
        'sms' => '短信通知',
        'storage' => '上传设置',
        'resource' => '资源配置',
        'trade' => '交易设置',
        'Yuncang' => '云仓配置',
        'publicwxtemplate' => '公众号消息通知',
        'wechattemplate' => '小程序订阅消息',
        'withdraw' => '提现管理',
        'customer' => '客服配置',
        'poster' => '个人中心海报',
        'promote' => '分销员配置',
        'sign' => '签到配置',
        'my_personal' => '个人中心',
        'manage' => '弹窗广告',
        'integral' => '积分配置',
        'user' => '用户协议',
        'privacy' => '隐私政策',
        'apply_online_notice' => '核销须知',
        'code_setting' => '兑换码配置',
        'supply' => '供应链配置',
        'distribution' => '全局分销配置',
        'teacher' => '导师配置',
        'express' => '快递100配置',
        'dySystem' => '抖音配置',
        'collection' => '藏品配置',
        'certification' => '实名认证',
        'agreement' => '协议配置',
        'withdrawal' => '提现配置',
        'service' => '服务费配置',
        'drop' => '空投配置',
        'todaytask' => '每日任务',
        'blockchain' => '上链配置',
        'read' => '须知配置',
        'order' => '订单配置',
        'calendar' => ' 日历分享',
        'behaviorcode' => '行为验证码',
        'payprotocol' => '连连支付协议',
        'site_setting' => '站点配置',
		'index' => '商城配置',
        'prize' => '奖品设置',
    ];

    /**
     * 更新系统设置
     * @param $key
     * @param $values
     * @return bool
     * @throws \think\exception\DbException
     */
    public function edit($key, $values)
    {
        $redis = initRedis();
        $model = self::detail($key) ?: $this;
        // 删除客服配置的缓存 ：非全局系统配置
        $redisKey = 'setting_' . $key;
        $redis->del($redisKey);
        Cache::rm('setting_guide' . self::$app_id);
        return $model->save([
            'key' => $key,
            'describe' => $this->describe[$key],
            'values' => $values,
            'app_id' => self::$app_id,
        ]) !== false;
    }

}
