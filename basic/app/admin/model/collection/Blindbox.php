<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/14   10:48
 * +----------------------------------------------------------------------
 * | className: 盲盒管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\collection;

use app\admin\model\Setting;
use app\common\model\Blindbox as BlindboxModel;
use TencentCloud\Dcdb\V20180411\Models\Database;
use think\Db;
use think\Env;

class Blindbox extends BlindboxModel
{

    /**
     * 获取列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   10:49
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where['blindbox_id'] = $param['blindbox_id'];
        $dataList = $this->where($where)
            ->order(['sort' => 'asc'])
            ->limit($offset,$limit)
            ->select();
        foreach ($dataList as $k => $v){
            $goods_info = (new Goods())->where(['goods_id'=>$v['goods_id']])->find();
            $dataList[$k]['asset_status'] = empty($goods_info['asset_id'])?'<span class="layui-badge">未上链</span>':'<span class="layui-badge layui-bg-blue">已上链</span>';
            $dataList[$k]['sort'] = $goods_info['goods_sort'];
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
        }
        $return['count'] = $this->where($where)->count();
        $return['data'] = $dataList;
        $return['code'] = '0';
        $return['msg'] = 'OK';

        return $return;
    }

    /**
     * 操作按钮
     * @param $id
     * @return array
     * @Time: 2022/6/14   10:50
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static  function makeButton($id)
    {
        $returnArray = [
            '编辑' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'renewGoods',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
        $blockchain = Setting::getItem('blockchain');
        if ($blockchain['default'] == 'BD' || $blockchain['default'] == 'WC') {
            $returnArray['上链发行'] = [
                'href' => "javascript:void(0)",
                'lay-event' => 'windChain',
            ];
        }
        return $returnArray;
    }

    /**
     * 添加
     * @param $data
     * @return array|false|\think\Collection|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/14   14:11
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add($data,$goods_id)
    {
        $insertData = [
            'goods_id' => $goods_id,
            'blindbox_id' => $data['blindbox_id'],
            'app_id' => self::$app_id,
            'goods_name' => $data['goods_name'],
            'goods_price' => $data['goods_price'],
            'goods_thumb' => $data['goods_thumb'],
            'stock_num' => $data['stock_num'],
            'probability' => isset($data['probability'])?$data['probability']:0,
        ];
        return $this->allowField(true)->save($insertData);
    }

    /**
     * 编辑
     * @param $data
     * @return false|int
     * @Interface edit
     */
    public function edit($data,$goods_id)
    {
        return $this->allowField(true)->save($data,['goods_id' => $goods_id]);
    }


    /**
     * 删除
     * @return false|int
     * @Time: 2022/6/14   11:09
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface setDelete
     */
    public function setDelete()
    {
        //删除这个盲盒 删除对应的商品的
        (new Goods())->where(['goods_id'=>$this['goods_id']])->update(['is_del'=>1]);
        return $this->where(['id' => $this['id']])->delete();
    }

}