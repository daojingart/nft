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
 * | className: 微信信息配置模型
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

use exception\BaseException;
use wechat\WechatAccesstoken;
use think\Cache;
use think\Db;

class WxSetting extends BaseModel
{
    protected $name = 'wx_setting';

    /**
     * 查询获取微信信息,获取缓存一个小时
     * @Notes: 获取当前登录商户的微信信息
     * @Interface getWxSettingInfo
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/9   3:50 下午
     */
    public function getWxSettingInfo()
    {
        $cacheKey = 'wx_setting_10001';
        if(!Cache::store("redis")->get($cacheKey)){
            $cacheValues = self::get(['app_id'=> config("app_id")]);
            Cache::store("redis")->set($cacheKey, json_encode($cacheValues));
        }
        return json_decode(Cache::store("redis")->get($cacheKey),true);
    }

    /**
     * 获取应用配置信息
     * @param $goods_id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail()
    {
        $cacheKey = 'wx_setting_10001';
        if(!Cache::store("redis")->get($cacheKey)){
            $cacheValues = self::get(['app_id'=> config("app_id")]);
            Cache::store("redis")->set($cacheKey, json_encode($cacheValues));
        }
        return json_decode(Cache::store("redis")->get($cacheKey),true);
    }

    /**
     * 删除wxapp缓存
     * @return bool
     */
    public static function deleteCache()
    {
        $cacheKey = 'wx_setting_10001';
        return Cache::store("redis")->rm($cacheKey);
    }

    /**
     * @Notes:存储用户微信相关信息
     * @Interface insertData
     * @param array $data
     * @return false|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/9   4:39 下午
     */
    public function add(array $data)
    {
        if(isset($data['order_pay'])){
            $data['wx_template'] = json_encode($data);
        }
        if(isset($data['wxapp_order_pay'])){
            $data['wxapp_template'] = json_encode($data);
        }
        if(isset($data['wx_open'])){
            $data['gateway'] = json_encode($data);
        }
        if(isset($data['android_down_link'])){
            $data['app_upgrade'] = json_encode($data);
        }
        if(isset($data['guide_page'])){
            $data['app_guide_page'] = json_encode($data);
        }
        if(isset($data['user_agreement'])){
            $data['wx_protocol'] = json_encode($data);
        }
        //存在则写入证书
        if(isset($data['apiclient_key'])){
            $this->writeCertificate($data);
        }
        $data['app_id'] = self::$app_id;
        self::deleteCache();
        return $this->allowField(true)->save($data,['wx_id' => 1]);
    }

    /**
     * @Notes: 写入证书
     * @Interface writeCertificate
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/5/8   2:42 下午
     */
    public function writeCertificate($data)
    {
        //写入CERT证书
        file_put_contents(CERT_LOG_PATH,$data['apiclient_cert']);
        //写入KEY证书
        file_put_contents(KEY_LOG_PATH,$data['apiclient_key']);
    }

}
