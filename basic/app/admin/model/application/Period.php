<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   15:12
 * +----------------------------------------------------------------------
 * | className: 往期回顾
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\application;

use app\common\model\Period as PeriodModel;

class Period extends PeriodModel
{
    /**
     * 获取列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   15:12
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;

        $where = [];
        $where['is_del'] = 0;
        if (!empty($param['title'])) {$where['title'] = ['like', '%' . $param['title'] . '%'];}

        $dataList = $this->where($where)
            ->order(['id' => 'desc'])
            ->limit($offset, $limit)
            ->select();
        foreach ($dataList as $k => $v) {
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
        }

        $return['count'] = $this->where($where)->count();
        $return['data'] = $dataList;
        $return['code'] = '0';
        $return['msg'] = 'OK';

        return $return;
    }

    /**
     * 操作按钮
     * @param $id
     * @return array
     * @Time: 2022/6/16   15:17
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static function makeButton($id)
    {
        return [
            '编辑' => [
                'href' => url('application.period/renew', ['id' => $id]),
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
     * @Time: 2022/6/16   15:17
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

    /**
     * 编辑
     * @param $data
     * @return false|int
     * @Time: 2022/6/16   15:17
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface edit
     */
    public function edit($data)
    {
        return $this->allowField(true)->save($data, ['id' => $this['id']]);
    }

    /**
     * 删除
     * @return false|int
     * @Time: 2022/6/16   15:18
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface setDelete
     */
    public function setDelete()
    {
        return $this->save(['is_del' => self::$is_del], ['id' => $this['id']]);

    }

}