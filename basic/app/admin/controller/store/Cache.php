<?php

// +----------------------------------------------------------------------
// | 八六互联 [Eight six interconnection ]
// +----------------------------------------------------------------------
// | Copyright (c) 2008~2019 http://www.86itn.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zq
// +----------------------------------------------------------------------
// | ClassName: 缓存清理
// +----------------------------------------------------------------------

namespace app\admin\controller\store;

use app\admin\controller\Controller;
use think\Cache as Driver;

class Cache extends Controller
{
    /**
     * 清理缓存
     * @param bool $isForce
     * @return mixed
     */
    public function clear($isForce = false)
    {
        if ($this->request->isAjax()) {
            $data = $this->postData('cache');
            $this->rmCache($data['keys'], isset($data['isForce']) ? !!$data['isForce'] : false);
            //清除首页DIY缓存
            $cache_key= 'homePage';
            \think\Cache::clear('pageCache_' .$cache_key);
            return $this->renderSuccess('操作成功');
        }
        return $this->fetch('clear', [
            'cacheList' => $this->getCacheKeys(),
            'isForce' => !!$isForce ?: config('app_debug'),
        ]);
    }

    /**
     * 删除缓存
     * @param $keys
     * @param bool $isForce
     */
    private function rmCache($keys, $isForce = false)
    {
        if ($isForce === true) {
            Driver::clear();
        } else {
            $cacheList = $this->getCacheKeys();
            foreach (array_intersect(array_keys($cacheList), $keys) as $key) {
                Driver::has($cacheList[$key]['key']) && Driver::rm($cacheList[$key]['key']);
            }
        }
    }

    /**
     * 获取缓存索引数据
     */
    private function getCacheKeys()
    {
        $wxapp_id = $this->store['blapp']['app_id'];
        return [
            'setting' => [
                'key' => 'setting_guide' . $wxapp_id,
                'name' => '系统配置'
            ],
            'category' => [
                'key' => 'goods_category_' . $wxapp_id,
                'name' => '商城分类缓存'
            ],
            'goods' => [
                'key' => 'category_' . $wxapp_id,
                'name' => '分类缓存'
            ],
            'wxapp' => [
                'key' => 'blapp_' . $wxapp_id,
                'name' => '小程序设置'
            ]
        ];
    }

}
