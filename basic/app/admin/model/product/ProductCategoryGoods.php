<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 11:05
 */


namespace app\admin\model\product;

use app\common\model\Notice;
use app\common\model\ProductCategoryGoods as ProductCategoryModel;
use think\Cache;

/**
 * 产品分类业务
 * Class ProductCategory
 * @package app\admin\model\product
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-07-26 11:06
 */
class ProductCategoryGoods extends ProductCategoryModel
{
    /**
     * 获取分类列表
     */
    public function getListAll($param)
    {
        $limit = $param['limit']??20;
        $offset = ($param['page'] - 1) * $limit;
        $list = $this
            ->order(['category_id' => 'asc'])
            ->limit($offset,$limit)
            ->select()->toArray();
        $list = getTrees($list);
        foreach ($list as $k=>$v){
            $list[$k]['operate'] = showNewOperate(self::makeButton($v['category_id']));
            $list[$k]['name'] =str_repeat('-',$v['level']).$list[$k]['name'];
        }
        $return['count'] = $this->getListTotal();
        $return['data'] = $list;
        $return['code'] = '0';
        $return['msg'] = 'ok';
        return $return;
    }

    /**
     * 获取产品分类
     * @param $param
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-07-26 11:52
     */
    public function getList()
    {
        $list = $this->order(['category_id' => 'asc'])
            ->select()->toArray();
        $list = getTrees($list);
        foreach ($list as $k=>$v){
            $list[$k]['name'] =str_repeat('-',$v['level']).$list[$k]['name'];
        }
        return $list;
    }

    private static  function makeButton($id)
    {
        return [
            '编辑' => [
                'href' => url('goods.category/renew', ['category_id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }
    /**
     * 统计数量
     */
    public function getListTotal()
    {
        return $this
            ->count();
    }
    /**
     * 添加分类
     */
    public function add($data)
    {
        $this->deleteCache();
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

    /**
     * 删除缓存
     * @return bool
     */
    private function deleteCache()
    {
        return Cache::rm('category_' . static::$app_id);
    }

    /**
     * 编辑记录
     * @param $data
     * @return bool|int
     */
    public function edit($data)
    {
        $this->deleteCache();
        return $this->allowField(true)->save($data);
    }
    /**
     * 删除分类
     */
    public function remove($category_id)
    {
        if ($goodsCount = (new Notice())->where(compact('category_id'))->count()) {
            $this->error = '该分类下存在' . $goodsCount . '个公告，不允许删除';
            return false;
        }
        $this->deleteCache();
        return $this->delete();
    }

}