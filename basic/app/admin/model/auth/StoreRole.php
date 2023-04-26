<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2020/12/9   10:02 上午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 商户角色模型
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\auth;

use app\admin\model\store\PDOStatement;
use app\common\model\StoreRole as StoreRoleModel;
use think\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;

class StoreRole extends StoreRoleModel
{
    /**
     * @Notes: 操作按钮
     * @Interface makeButton
     * @param $role_id
     * @return array
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   6:41 下午
     * @author: Mr.Zhang
     */
    private static  function makeButton($role_id)
    {
        return [
            '编辑' => [
                'href' => url('auth.role/renew', ['id' => $role_id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }

    /**
     * 角色信息
     * @param $id
     * @return null|static
     * @throws DbException
     */
    public static function detail($id)
    {
        return self::get($id);
    }

    /**
     * @Notes: 查询商户建立的角色
     * @Interface getList
     * @return array|bool|PDOStatement|string|Collection
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/9   10:58 上午
     */
    public function getList()
    {
        return $this->select();
    }

    /**
     * @Notes: 查询角色列表
     * @Interface getListPage
     * @param $param
     * @return bool|PDOStatement|string|Collection
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   6:41 下午
     */
    public  function getListPage($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        if (!empty($param['role_name'])) {
            $where['role_name'] = $param['role_name'];
        }
        $StoreRole = (new StoreRole)
            ->where($where)
            ->limit($offset,$limit)
            ->select();
        foreach ($StoreRole as $k=>$v){
            if($v['rule'] != '*'){
                $StoreRole[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
            }
        }
        $return['count'] = $this->getCount($param);
        $return['data'] = $StoreRole;
        $return['code'] = '0';
        $return['msg'] = 'OK';
        return $return;
    }

    /**
     * @Notes: 统计符合条件的数量
     * @Interface getCount
     * @param array $param
     * @return int|string
     * @throws Exception
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   11:25 上午
     */
    public function getCount($param = [])
    {
        $where = [];
        if (!empty($param['role_name'])) {
            $where['role_name'] = $param['role_name'];
        }
        return (new StoreRole())
            ->where($where)
            ->count();
    }

    /**
     * 更新角色信息
     * @param $data
     * @return bool
     */
    public function renew($data)
    {
        // 更新管理员信息
        if(empty($data['rule'])){
            $this->error = '请为该新增角色添加对应权限节点';
            return false;
        }
        $data['rule'] = implode(",", $data['rule']);
        if ($this->where(['id'=>$data['id']])->update($data) === false) {
            return false;
        }
        return true;
    }

    /**
     * @Notes: 插入角色信息
     * @Interface insertUser
     * @param $data
     * @return bool
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/9   11:41 上午
     */
    public  function insertUser($data)
    {
        if(empty($data['rule'])){
            $this->error = '请为该新增角色添加对应权限节点';
            return false;
        }
        //验证用户名的唯一性
        if($this->roleNameExit($data['role_name'])){
            $this->error = '角色名称已存在';
            return false;
        }
        $data['rule'] = implode(",", $data['rule']);
        $data['app_id'] = self::$app_id;
        return $this->allowField(true)->save($data);
    }

    /**
     * @Notes: 角色删除
     * @Interface setDel
     * @return false|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   3:27 下午
     */
    public function setDel()
    {
        //判断下当前角色是否绑定管理员 绑定则不能删除
        $count = (new StoreUser())->where(['role_id'=>$this['id'],'status'=>1])->count();
        if($count>0){
            $this->error = "该角色已分配{$count}个管理员请先修改管理员角色在删除！";
            return false;
        }
        return $this->delete();
    }

}