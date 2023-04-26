<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/13   11:00
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\collection;

use app\common\model\Category as CategoryModel;

class Category extends CategoryModel
{
    /**
     * 获取分类列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   18:35
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;

        $where = [];
        $where['is_del'] = 0;
        if(!empty($param['name'])){$where['name'] = ['like','%'.$param['name'].'%'];}
        $dataList = $this->where($where)
            ->order(['sort' => 'desc'])
            ->limit($offset,$limit)
            ->select();
        foreach ($dataList as $k => $v){
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['category_id']));
        }

        $return['count'] = $this->where($where)->count();
        $return['data'] = $dataList;
        $return['code'] = '0';
        $return['msg'] = 'OK';

        return $return;
    }

    /**
     * 操作按钮
     * @param $category_id
     * @return array
     * @Time: 2022/6/13   11:03
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static  function makeButton($category_id)
    {
        return [
            '编辑' => [
                'href' => url('collection.category/renew', ['category_id' => $category_id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }

    /**
     * 添加
     * @param $data
     * @return false|int
     * @Time: 2022/6/13   11:03
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return  $this->allowField(true)->save($data);
    }

    /**
     * 编辑
     * @param $data
     * @return false|int
     * @Time: 2022/6/13   11:03
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface edit
     */
    public function edit($data)
    {
        return $this->allowField(true)->save($data,['category_id' => $this['category_id']]);
    }

    /**
     * 删除
     * @return false|int
     * @Time: 2022/6/13   11:03
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface setDelete
     */
    public function setDelete()
    {
        return $this->save(['is_del' => self::$is_del],['category_id' => $this['category_id']]);
    }
}