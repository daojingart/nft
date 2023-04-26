<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/27   12:27 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 城市控制器
 * +----------------------------------------------------------------------
 */
namespace app\api\controller;

use app\common\controller\Controller;
use app\common\model\Region as RegionModel;

/**
 * 公共接口
 */
class Region extends Controller
{
    /**
     * 获取城市列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/region/getAllList)
     */
    public function getAllList()
    {
        $region = new RegionModel();
        $cityList = $region->getCacheTree();
        $cityList = $region->reGetCacheTree($cityList);
        $this->success($cityList,'ok');
    }

}