<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/13   3:34 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 运费模板信息
 * +----------------------------------------------------------------------
 */
namespace app\common\model;

use think\Request;


class Delivery extends BaseModel
{
    protected $name = 'delivery';

    /**
     * 关联配送模板区域及运费
     * @return \think\model\relation\HasMany
     */
    public function rule()
    {
        return $this->hasMany('DeliveryRule');
    }

    /**
     * 计费方式
     * @param $value
     * @return mixed
     */
    public function getMethodAttr($value)
    {
        $method = [10 => '按件数', 20 => '按重量'];
        return ['text' => $method[$value], 'value' => $value];
    }

    /**
     * 获取全部
     * @return mixed
     */
    public static function getAll()
    {
        $model = new static;
        return $model->order(['sort' => 'asc'])->select();
    }

    /**
     * 获取列表
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $delivery_list = $this->with(['rule'])
            ->order(['sort' => 'asc'])
            ->limit($offset,$limit)
            ->select();
        foreach ($delivery_list as $k=>$v){
            $delivery_list[$k]['operate'] = showNewOperate(self::makeButton($v['delivery_id']));
        }
        $return['count'] = $this->count();
        $return['data'] = $delivery_list;
        $return['code'] = '0';
        $return['msg'] = 'OK';
        return $return;


    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    public static function makeButton($id)
    {
        return [
            '编辑' => [
                'href' => url('goods.delivery/renew', ['delivery_id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }

    /**
     * 运费模板详情
     * @param $delivery_id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($delivery_id)
    {
        return self::get($delivery_id, ['rule']);
    }

}
