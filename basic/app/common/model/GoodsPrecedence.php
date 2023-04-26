<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 15:16
 */


namespace app\common\model;

/**
 * 优先购数据模型
 * Class GoodsPrecedence
 * @package app\common\model
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 15:16
 */
class GoodsPrecedence extends BaseModel
{
    /**
     * 藏品关联
     */
    public function goods()
    {
        return $this->belongsTo('goods','goods_id','goods_id');
    }
    public function label()
    {
        return $this->belongsTo('MemberLabel','label_id','id');
    }

    /**
     * 获取优先购列表
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 18:07
     */
    public static function getList()
    {
        $page = request()->param('page')?:1;//当前第几页
        $list = request()->param('limit')?:10;//每页显示几条
        $where = [];
        $param = request()->param();
        if (array_key_exists('goods_name',$param)&&!empty($param['goods_name'])){
            $goods = Goods::where(['goods_name'=>['like','%'.$param['goods_name']."%"]])->find();
            if (!empty($goods)){
                $where['goods_id'] = $goods->goods_id;
            }
        }
        $data = self::with(['goods','label'])->where($where)->paginate($list,false,$config = ['page'=>$page])->toArray();
        foreach ($data['data'] as &$item){
            $item['label_title'] = $item['label']['label_title'];
            $item['goods_name'] = $item['goods']['goods_name'];
            $item['goods_thumb'] = $item['goods']['goods_thumb'];
            $item['operate'] = showNewOperate(self::makeButton($item['id']));
        }
        $arr['data'] = $data['data'];
        $arr['code'] = '0';
        $arr['msg'] = 'OK';
        $arr['count'] = $data['total'];
        return  json($arr);
    }

    private static  function makeButton($id): array
    {
        return [
            '编辑' => [
                'href' => url('user.preference/edit', ['id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }
}