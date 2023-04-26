<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-15 11:05
 */


namespace app\admin\service;
use app\common\model\GoodsSynthetic;
/**
 * 合成处理
 * Class Synthetic
 * @package app\admin\service
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-15 11:05
 */
class Synthetic extends BaseService
{
    /**
     * 获取列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 11:06
     */
    public static function getList()
    {
        $where = [];
        $param = request()->param();
        if (array_key_exists('name',$param)&&!empty($param['name'])){
            $where['name'] = ['like','%'.$param['name'].'%'];
        }
        $page = request()->param('page')?:1;//当前第几页
        $list = request()->param('limit')?:10;//每页显示几条
        $data = GoodsSynthetic::with(['goods'])->where($where)->order('sort desc')->paginate($list,false,$config = ['page'=>$page])->toArray();
        foreach ($data['data'] as &$item){
            $item['goods_thumb'] =  $item['goods']['goods_thumb'];
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
            '编辑' => [
                'href' => url('application.synthetic/edit', ['id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ],
            '合成藏品设置' => [
                'href' => url('application.conditions/index', ['id' => $id]),
                'lay-event' => 'goods',
            ],
            '合成白名单' => [
                'href' => url('application.synthetic/whitelist', ['id' => $id]),
                'lay-event' => 'goods',
            ],
        ];
    }
}