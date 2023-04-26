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
 * | className: 系统配置基类
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

use think\Cache;

class Setting extends BaseModel
{
    protected $name = 'setting';
    protected $createTime = false;

    /**
     * @Notes: 获取器转化数组格式输出
     * @Interface getValuesAttr
     * @param $value
     * @return mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:34 上午
     */
    public function getValuesAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * @Notes: 修改器转化json格式输入
     * @Interface setValuesAttr
     * @param $value
     * @return false|string
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:35 上午
     */
    public function setValuesAttr($value)
    {
        return json_encode($value);
    }

    /**
     * @Notes: 根据键值获取配置内容信息
     * @Interface getItem
     * @param $key
     * @param null $wxapp_id
     * @return array|mixed
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:35 上午
     */
    public static function getItem($key, $wxapp_id = null)
    {
        $redis = initRedis();
        $redisKey = 'setting_' . $key;
        if(!$redis->get($redisKey)){
            $data = self::getAll($wxapp_id);
            $settingKey = isset($data[$key]) ? $data[$key]['values'] : [];
            $redis->set($redisKey, json_encode($settingKey));
        }
        return json_decode($redis->get($redisKey),true);
    }

    /**
     * @Notes: 获取键值详情信息
     * @Interface detail
     * @param $key
     * @return Setting|null
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:36 上午
     */
    public static function detail($key)
    {
        return self::get(compact('key'));
    }

    /**
     * @Notes: 系统设置
     * @Interface getAll
     * @param null $wxapp_id
     * @return array
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/27   10:36 上午
     */
    public static function getAll($wxapp_id = null)
    {
        $self = new static;
        $data = array_column(collection($self::all())->toArray(), null, 'key');
        return array_merge_multiple($data,[]);
    }
}
