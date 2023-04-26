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
 * | className: 地区公共模型
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

use think\Cache;


class Region extends BaseModel
{
    protected $name = 'region';
    protected $createTime = false;
    protected $updateTime = false;

    /**
     * 根据id获取地区名称
     * @param $id
     * @return string
     */
    public static function getNameById($id)
    {
        $region = self::getCacheAll();
        return $region[$id]['name'];
    }

    /**
     * 根据名称获取地区id
     * @param $name
     * @param int $level
     * @param int $pid
     * @return mixed
     */
    public static function getIdByName($name, $level = 0, $pid = 0)
    {
        return static::useGlobalScope(false)->where(compact('name', 'level', 'pid'))
            ->value('id');
    }

    /**
     * 根据名称获取地区id
     * @param $name
     * @param int $level
     * @param int $pid
     * @return mixed
     */
    public static function getIdByShortName($shortname, $level = 0)
    {
        return static::useGlobalScope(false)->where(compact('shortname', 'level'))
            ->value('id');
    }

    /**
     * @param $name
     * @param int $level
     * @param int $pid
     * @return mixed
     */
    private static function add($name, $level = 0, $pid = 0)
    {
        $model = new static;
        $model->save(compact('name', 'level', 'pid'));
        Cache::rm('region');
        return $model->getLastInsID();
    }

    /**
     * 获取所有地区(树状结构)
     * @return mixed
     */
    public static function getCacheTree()
    {
        return self::regionCache()['tree'];
    }

    /**
     * @Notes: 重新建立数组索引
     * @Interface reGetCacheTree
     * @param $cityList
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/28   4:11 下午
     */
    public function reGetCacheTree($cityList)
    {
        $cityList = array_merge($cityList);

        foreach ($cityList as $k=>$v){
            if(isset($cityList[$k]['city'])){
                $cityList[$k]['city'] = array_merge($cityList[$k]['city']);
            }
        }
        foreach ($cityList as $k=>$v){
            if(isset($v['city'])){
                foreach ($v['city'] as $key=>$val){
                    if(!empty($val['region'])){
                        $cityList[$k]['city'][$key]['region'] = array_values($val['region']);
                    }

                }
            }

        }
        return $cityList;
    }

    /**
     * 获取所有地区
     * @return mixed
     */
    public static function getCacheAll()
    {
        return self::regionCache()['all'];
    }

    /**
     * 获取地区缓存
     * @return mixed
     */
    private static function regionCache()
    {
        if (!Cache::get('region')) {
            // 所有地区
            $all = $allData = self::useGlobalScope(false)->column('id, pid, name, level', 'id');
            // 格式化
            $tree = [];
            foreach ($allData as $pKey => $province) {
                if ($province['level'] == 1) {    // 省份
                    $tree[$province['id']] = $province;
                    unset($allData[$pKey]);
                    foreach ($allData as $cKey => $city) {
                        if ($city['level'] == 2 && $city['pid'] == $province['id']) {    // 城市
                            $tree[$province['id']]['city'][$city['id']] = $city;
                            unset($allData[$cKey]);
                            foreach ($allData as $rKey => $region) {
                                if ($region['level'] == 3 && $region['pid'] == $city['id']) {    // 地区
                                    $tree[$province['id']]['city'][$city['id']]['region'][$region['id']] = $region;
                                    unset($allData[$rKey]);
                                }
                            }
                        }
                    }
                }
            }
            Cache::set('region', compact('all', 'tree'),'7200');
        }
        return Cache::get('region');
    }

}
