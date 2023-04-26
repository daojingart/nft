<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/17   9:27
 * +----------------------------------------------------------------------
 * | className: 轮播图
 * +----------------------------------------------------------------------
 */

namespace app\api\model;

use app\api\model\upload\UploadFile;
use app\common\model\Banner as BannerModel;

class Banner extends BannerModel
{
    /**
     * 获取轮播图列表
     * @return bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/17   9:27
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getBannerList
     */
    public function getBannerList($type)
    {
        $banner_list = $this->where(['is_del' => 0,'type' => $type])->field("banner_id,thumb,link_url")->select();
        return $banner_list;
    }
}