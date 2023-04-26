<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/6/24   10:13 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 充值规格表
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\application;

use app\common\model\Recharge as RechargeModel;

class Recharge extends RechargeModel
{
    /**
     * @Notes: 获取数据列表
     * @Interface getDataList
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 021/6/24   10:13 上午
     */
    public function getListAll($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        $dataList = $this->where($where)->order(['sort' => 'asc'])->limit($offset,$limit)->where($where)->select();
        foreach ($dataList as $k=>$v){
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['ecard_id']));
        }
        $return['count'] = $this->where($where)->count();
        $return['data'] = $dataList;
        $return['code'] = '0';
        $return['msg'] = 'OK';
        return $return;
    }

    /**
     * @Notes: 操作按钮
     * @Interface makeButton
     * @param $ecard_id
     * @return array
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 021/6/24   10:13 上午
     * @author: Mr.Zhang
     */
    private static  function makeButton($ecard_id)
    {
        return [
            '编辑' => [
                'href' => url('application.recharge/renew', ['ecard_id' => $ecard_id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ],
        ];
    }

    /**
     * 添加新记录
     * @param $data
     * @return false|int
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

    /**
     * 编辑记录
     * @param $data
     * @return bool|int
     */
    public function edit($data)
    {
        return $this->allowField(true)->save($data);
    }

    /**
     * 删除专题
     * @param $ecard_id
     * @return bool
     * @throws \think\Exception
     */
    public function remove($ecard_id)
    {
        $this->where('ecard_id',$ecard_id)->delete();
        return true;
    }
}