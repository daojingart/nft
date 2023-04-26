<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-20 17:14
 */


namespace app\common\model;


class CardCategory extends BaseModel
{

    public static function getList()
    {
        $param = request()->param();
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;

        $where = [];
        $dataList = self::where($where)
            ->order('id desc')
            ->limit($offset,$limit)
            ->select();
        foreach ($dataList as $k=>$v){
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
        }

        $return['count'] = self::where($where)->count();
        $return['data'] = $dataList;
        $return['code'] = '0';
        $return['msg'] = 'OK';
        return $return;
    }

    private static  function makeButton($id)
    {
        return [
            '编辑' => [
                'href' => url('store.card/edit', ['id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }



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
     * 删除
     * @return false|int
     * @Time: 2022/6/16   18:34
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface setDelete
     */
    public function setDelete()
    {
        return $this->where(['id'=>$this['id']])->delete();
    }

}