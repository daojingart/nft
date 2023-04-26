<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/16   18:31
 * +----------------------------------------------------------------------
 * | className: 首页ICON 导航区配置
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\store;

use app\common\model\Navigation as NavigationModel;

class Navigation extends NavigationModel
{
    /**
     * 获取列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/16   18:33
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        if(isset($param['type']) && !empty($param['type'])){
            $where['type'] = $param['type'];
        }
        $dataList = $this->where($where)
            ->order('id desc')
            ->limit($offset,$limit)
            ->select();
        foreach ($dataList as $k=>$v){
			$dataList[$k]['disabled'] = $v['disabled']==10?"显示":"禁用";
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
     * @param $banner_id
     * @return array
     * @Time: 2022/6/16   18:34
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static  function makeButton($id)
    {
		return [
			'编辑' => [
				'href' => url('store.navigation/renew', ['id' => $id]),
				'lay-event' => '',
			],
			'删除' => [
				'href' => "javascript:void(0)",
				'lay-event' => 'remove',
			]
		];
    }

    /**
     * 添加
     * @param $data
     * @return false|int
     * @Time: 2022/6/16   18:35
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

    /**
     * 编辑
     * @param $data
     * @return false|int
     * @Time: 2022/6/16   18:34
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface edit
     */
    public function edit($data)
    {
        return $this->allowField(true)->save($data,['id'=>$this['id']]);
    }

    /**
     * 删除
     * @return false|int
     * @Time: 2022/6/16   18:34
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface setDelete
     */
    public function setDelete()
    {
        return $this->save(['is_del' => 20],['id'=>$this['id']]);
    }
}