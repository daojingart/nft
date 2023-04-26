<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/2   2:28 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className:菜单导航
 * +----------------------------------------------------------------------
 */

namespace app\api\model;

use app\common\model\Navigation as NavigationModel;
use app\common\model\PublicNav;

class Navigation extends NavigationModel
{
    /**
     * @Notes: 获取导航菜单
     * @Interface getDataList
     * @return bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/3/2   2:30 下午
     */
    public function getDataList()
    {
        return $this->order(['sort' => 'asc'])
            ->field("id,icon_name,img_id,icon_link,link_type")
            ->with("file")
            ->where(['disabled'=>10])
            ->select()->each(function ($item) {
                $item['icon_link'] = htmlspecialchars_decode($item['icon_link']);
                return $item;
            });

    }
}