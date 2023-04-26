<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-15 14:02
 */


namespace app\common\model;

/**
 * 合成商品条件模型
 * Class GoodsSyntheticCount
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-15 14:02
 */
class GoodsSyntheticCount extends BaseModel
{
    /**
     * 关联模型
     * @return \think\model\relation\BelongsTo
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-21 11:19
     */
    public function goods()
    {
        return $this->belongsTo('goods','goods_id','goods_id');
    }
    /**
     * 获取列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 11:06
     */
    public static function getList()
    {
        $page = request()->param('page')?:1;//当前第几页
        $list = request()->param('limit')?:10;//每页显示几条
        $data = self::order('sort desc')->where('synthetic_id',input('id'))->paginate($list,false,$config = ['page'=>$page])->toArray();
        foreach ($data['data'] as &$item){
            $item['operate'] = showNewOperate(self::makeButton($item['id']));
        }
        $arr['data'] = $data['data'];
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = $data['total'];
        return  json($arr);
    }
    private static  function makeButton($id)
    {
        return [
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ],
        ];
    }
}