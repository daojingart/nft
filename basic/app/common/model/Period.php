<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   14:59
 * +----------------------------------------------------------------------
 * | className:  往期回顾
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


use think\Request;

class Period extends BaseModel
{
    protected $name = 'period';
    protected static $is_del = 1; //软删除数据

    /**
     * 详情
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($id)
    {
        return self::get($id);
    }


    /**
     * 获取往期回顾列表
     * @param $param
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/6/17   14:03
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($param)
    {
        $list = self::where(['disabled' => 0,'is_del' => 0])
            ->field('id,title,image,content,desc')
            ->order('id desc')
            ->paginate($param['listRows'],false,[
                'query' =>  Request::instance()->request()
            ])->each(function ($item){
                $item['images'] = rtrim($item['image']);
                $item['content'] = htmlspecialchars_decode($item['content']);
                unset($item['image']);
                return $item;
            });

        return $list;
    }


}