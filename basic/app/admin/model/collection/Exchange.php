<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   16:31
 * +----------------------------------------------------------------------
 * | className:  荣誉值兑换
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\collection;

use app\common\model\Exchange as ExchangeModel;


class Exchange extends ExchangeModel
{
    /**
     * 获取兑换藏品列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time      : 2022/6/16   16:39
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($param)
    {
        $limit  = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;

        $where             = [];
        $where['a.is_del'] = $where['b.is_del'] = 0;
        if (!empty($param['goods_name'])) {
            $where['b.goods_name'] = ['like', '%' . $param['goods_name'] . '%'];
        }

        $dataList = $this->alias('a')
            ->join('goods b', 'a.goods_id=b.goods_id', 'left')
            ->where($where)
            ->field('a.*,b.goods_name,b.goods_thumb,b.stock_num')
            ->order(['a.id' => 'desc'])
            ->limit($offset, $limit)
            ->select();
        foreach ($dataList as $k => $v) {
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['id'], $v['goods_id']));
        }

        $return['count'] = $this->alias('a')->join('goods b', 'a.goods_id=b.goods_id', 'left')->where($where)->count();
        $return['data']  = $dataList;
        $return['code']  = '0';
        $return['msg']   = 'OK';

        return $return;
    }

    /**
     * 操作按钮
     * @param $id
     * @param $goods_id
     * @return array
     * @Time      : 2022/6/16   16:41
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static function makeButton($id, $goods_id)
    {
        return [
            '编辑' => [
                'href'      => url('collection.exchange/renew', ['id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href'      => "javascript:void(0)",
                'lay-event' => 'remove',
            ],
        ];
    }

    /**
     * 编辑兑换藏品
     * @param $data
     * @return false|int
     * @Time      : 2022/6/16   16:41
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function edit($data)
    {
        return $this->allowField(true)->save($data, ['id' => $this['id']]);
    }

    /**
     * 删除
     * @return false|int
     * @Time      : 2022/6/16   16:41
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface setDelete
     */
    public function setDelete()
    {
        return $this->save(['is_del' => 1], ['id' => $this['id']]);
    }
}