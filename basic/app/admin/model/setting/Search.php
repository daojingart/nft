<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/2/19   4:06 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 通知、公告
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\setting;

use app\common\model\Search as SearchModel;

class Search extends SearchModel
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
     * @Time: 2020/12/20   4:18 下午
     */
    public function getDataList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        if (isset($param['type']) && !empty($param['type'])) {
            $where['type'] = $param['type'];
        }
        $dataList = $this->where($where)
            ->order(['sort' => 'asc'])
            ->limit($offset,$limit)
            ->where($where)
            ->select();
        foreach ($dataList as $k=>$v){
            $dataList[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
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
                'href' => url('application.search/renew', ['id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }

    /**
     * @Notes: 添加
     * @Interface add
     * @param $data
     * @return array
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/8   4:40 下午
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        $res = $this->allowField(true)->save($data);
        return array('res' => $res,'type'=>$data['type']);
    }

    /**
     * @Notes: 编辑
     * @Interface edit
     * @param $data
     * @return false|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/20   4:38 下午
     */
    public function edit($data)
    {
        return $this->allowField(true)->save($data,['id'=>$this['id']]);
    }

    /**
     * @Notes: 删除
     * @Interface setDelete
     * @return false|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/20 4:37 下午
     */
    public function setDelete()
    {
        return $this->where(['id'=>$this['id']])->delete();
    }
}