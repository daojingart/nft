<?php

namespace app\common\model;

class Prize extends BaseModel
{
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        if (!empty($param['name'])) {$where['name'] = ['like', '%' . $param['name'] . '%'];}
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


    private static function makeButton($id)
    {
        return [
            '编辑' => [
                'href' => url('application.prize/edit', ['id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }

}