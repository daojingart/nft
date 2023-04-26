<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-16 10:49
 */


namespace app\common\model;


/**
 * 新闻分类
 * Class NewsCategory
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-16 10:49
 */
class NewsCategory extends BaseModel
{



    public static function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        $dataList = self::where($where)
            ->order(['sort' => 'desc'])
            ->limit($offset,$limit)
            ->where($where)
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

    /**
     * @Notes: 操作按钮
     * @Interface makeButton
     * @param $id
     * @return array
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   6:41 下午
     * @author: Mr.Zhang
     */
    private static  function makeButton($id)
    {
        return [
            '编辑' => [
                'href' => url('user.help/categoryRenew', ['id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }
}