<?php

namespace app\common\model;

class PrizeLog extends BaseModel
{
    public function prize()
    {
        return $this->hasOne(Prize::class, 'id', 'prize_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }


    public function getList($param)
    {
        $limit  = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where  = [];
        if (!empty($param['name'])) {
            $prize_ids         = Prize::where(['name' => ['like', '%' . $param['name'] . '%']])->column('id');
            $where['prize_id'] = ['in', $prize_ids];
        }
        if (!empty($param['phone'])) {
            $member = Member::where(['phone' => $param['phone']])->find();
            if ($member) {
                $where['member_id'] = $member['member_id'];
            } else {
                $where['member_id'] = -1;
            }
        }
        $dataList = $this->where($where)
            ->with(['member' => function ($query) {
                $query->field('member_id,phone,name');
            }])
            ->limit($offset, $limit)
            ->order('id desc')
            ->select();
        foreach ($dataList as $k => $v) {
            $type_text = [1=>'红包','藏品','产品','空奖'];
            $dataList[$k]['user_name']   = $v['member']['name'];
            $dataList[$k]['user_phone']  = $v['member']['phone'];
            $dataList[$k]['prize_id']    = $v['prize']['id'];
            $dataList[$k]['type_text'] = $type_text[$v['prize_type']];
            $dataList[$k]['operate']     = showNewOperate(self::makeButton($v['id']));
        }

        $return['count'] = $this->where($where)->count();
        $return['data']  = $dataList;
        $return['code']  = '0';
        $return['msg']   = 'OK';

        return $return;
    }


    private static function makeButton($id)
    {
        return [
            '删除' => [
                'href'      => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }


}