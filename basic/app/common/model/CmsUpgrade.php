<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/4/9   16:37
 * +----------------------------------------------------------------------
 * | className: 升级中心
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

class CmsUpgrade extends BaseModel
{
    protected $name = 'cms_upgrade';

    /**
     * 添加升级中心
     * @param $data
     * @return false|int
     * @Time: 2022/4/9   16:39
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface add
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

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
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        $dataList = $this->where($where)
            ->order(['id' => 'asc'])
            ->limit($offset,$limit)
            ->where($where)
            ->select();
        foreach ($dataList as $k=>$v){
            $dataList[$k]['upgrade_status'] = $v['upgrade_status']==10?"关闭更新":"开启更新";
            $dataList[$k]['upgrade_wechat_status'] = $v['upgrade_status']==20?"关闭更新":"开启更新";
            $dataList[$k]['upgrade_app_status'] = $v['upgrade_status']==20?"关闭更新":"开启更新";
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
                'href' => url('authorize.upgrade/renew', ['id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }

    /**
     * 获取详情
     * @param $id
     * @return CmsUpgrade|null
     * @throws \think\exception\DbException
     * @Time: 2022/4/9   16:59
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface details
     */
    public static function details($id)
    {
        return self::get($id);
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
}