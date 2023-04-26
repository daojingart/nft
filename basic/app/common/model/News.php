<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 09:48
 */


namespace app\common\model;

/**
 * 新闻
 * Class News
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 09:48
 */
class News extends BaseModel
{
    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
    public function category()
    {
        return $this->belongsTo('NewsCategory','category_id','id');
    }
    public static function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        if (array_key_exists('title',$param)&&!empty($param['title'])){
            $where['title'] = ['like','%'.$param['title'].'%'];
        }
        $dataList = self::where($where)
            ->with('category')
            ->order(['id' => 'desc'])
            ->limit($offset,$limit)
            ->where($where)
            ->select();
        foreach ($dataList as $k=>$v){
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
            $dataList[$k]['category_title'] = !empty($v['category'])?$v['category']['title']:'';
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
                'href' => url('user.help/renew', ['id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }
}