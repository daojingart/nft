<?php

namespace app\common\model;

class Nav extends BaseModel
{
    public function getList()
    {
        $dataList = $this
            ->order('id desc')
            ->select();
        foreach ($dataList as $k=>$v){
            $dataList[$k]['status'] = $v['status']==10 ? "显示":"禁用";
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
        }
        $return['count'] = $this->count();
        $return['data'] = $dataList;
        $return['code'] = '0';
        $return['msg'] = 'OK';
        return $return;
    }

    private static  function makeButton($id)
    {
        return [
            '编辑' => [
                'href' => url('store.nav/renew', ['id' => $id]),
                'lay-event' => '',
            ],
        ];
    }

    public function add($data)
    {
        return $this->allowField(true)->save($data);
    }
    public function edit($data)
    {
        return $this->allowField(true)->save($data,['id'=>$this['id']]);
    }
}