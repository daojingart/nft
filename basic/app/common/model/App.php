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
 * | className: 应用基类模型层
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

use think\Cache;
use think\Db;


class App extends BaseModel
{
    protected $name = 'app';

    /**
     * 从缓存中获取应用的APPID
     * @param null $app_id
     * @return mixed|null|static
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public static function getWxappCache($app_id = null)
    {
        if (is_null($app_id)) {
            $self = new static();
            $app_id = $self::$app_id;
        }
        if (!$data = Cache::get('blapp_' . $app_id)) {
            $data = self::get($app_id, ['serviceImage', 'phoneImage', 'navbar']);
            if (empty($data)) throw new \exception\BaseException(['msg' => '未找到应用的APPID']);
            Cache::set('blapp_' . $app_id, $data);
        }
        return $data;
    }

}
