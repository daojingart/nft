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
 * | className: 商户用户表模型
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\auth;

use app\common\model\StoreUser as StoreUserModel;
use app\common\server\LoginLog;
use Exception;
use think\Collection;
use think\Cookie;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\exception\PDOException;
use think\Paginator;
use think\Session;


class StoreUser extends StoreUserModel
{
    /**
     * 管理后台登录
     * @param $data
     * @return bool
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public function login($data)
    {
        // 验证用户名密码是否正确
        if (!$user = self::useGlobalScope(false)->with(['blapp','role'])->where([
            'user_name' => $data['user_name'],
            'password' => blhlhash($data['password']),
            'status' =>1
        ])->find()) {
            $this->error = '登录失败, 用户名或密码错误';
            return false;
        }
        if (empty($user['blapp'])) {
            $this->error = '登录失败, 未找到对应系统信息,请联系开发者处理';
            return false;
        }
        // 保存登录状态
        Cookie::set('blhlstore', [
            'user' => [
                'store_user_id' => $user['id'],
                'app_id' => $user['app_id'],
                'user_name' => $user['user_name'],
                'user_rule' => $user['role']['rule'],
            ],
            'blapp' => $user['blapp']->toArray(),
            'is_login' => true,
        ]);
        (new LoginLog())->insertLoginLog(1, "登录管理员后台");
        return true;
    }

    /**
     * 商户信息
     * @param $store_user_id
     * @return null|static
     * @throws DbException
     */
    public static function detail($store_user_id)
    {
        return self::get(['id'=>$store_user_id]);
    }

    /**
     * 更新当前管理员信息
     * @param $data
     * @return bool
     */
    public function renew($data)
    {
        if ($data['password'] !== $data['password_confirm']) {
            $this->error = '确认密码不正确';
            return false;
        }
        unset($data['password_confirm']);
        $data['password'] = blhlhash($data['password']);
        // 更新管理员信息
        if ($this->where(['id'=>$data['id']])->update($data) === false) {
            return false;
        }
        //判断当前登录用户修改的是否是自己,如果是自己则重新登录
        $admin_info = Session::get('blhlstore');
        if($admin_info['user']['store_user_id'] == $data['id']){
            // 更新session
            Session::set('blhlstore', [
                'user' => [
                    'store_user_id' => $data['id'],
                    'user_name' => $admin_info['user']['user_name'],
                    'user_rule' => $admin_info['user']['user_rule'],
                ],
                'blapp' => $admin_info['blapp'],
                'is_login' => true,
            ]);
        }
        return true;
    }

    /**
     * @Notes: 插入管理员信息
     * @Interface insertUser
     * @param $data
     * @return bool
     * @throws PDOException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/9   11:41 上午
     */
    public  function insertUser($data)
    {
       //验证用户名的唯一性
        if($this->usernameExit($data['user_name'])){
            $this->error = '用户名已存在';
            return false;
        }
        if ($data['password'] !== $data['password_confirm']) {
            $this->error = '确认密码不正确';
            return false;
        }
        if (!$data['role_id']) {
            $this->error = '请选择所属角色';
            return false;
        }
        // 启动事务
        $this->startTrans();
        try{
            // 新增管理员记录
            $data['password'] = blhlhash($data['password']);
            $data['app_id'] = self::$app_id;
            $this->allowField(true)->save($data);
            // 提交事务
            $this->commit();
            return true;
        } catch (Exception $e) {
            // 回滚事务
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }


    /**
     * @Notes: 获取管理员列表
     * @Interface getList
     * @param $param
     * @return bool|PDOStatement|string|Collection|Paginator
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/9   1:37 上午
     */
    public  function getList($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        if (!empty($param['phone'])) {
            $where['phone'] = $param['phone'];
        }
        $StoreUser = (new StoreUser)->where(['status'=>['<>',self::$status]])
            ->where($where)
            ->order(['create_time' => 'desc'])
            ->limit($offset,$limit)
            ->select();
        foreach ($StoreUser as $k=>$v){
            $StoreUser[$k]['operate'] = showNewOperate(self::makeButton($v['id']));
        }
        $return['count'] = $this->getCount($param);
        $return['data'] = $StoreUser;
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
                'href' => url('auth.user/renew', ['store_user_id' => $id]),
                'lay-event' => '',
            ],
            '删除' => [
                'href' => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];
    }

    /**
     * @Notes: 会员软删除
     * @Interface setDel
     * @return false|int
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   3:27 下午
     */
    public function setDel()
    {
        //角色默认超级管理员不能删除
        if ($this['role_id']  == '1' && $this['id'] == '10001') {
            $this->error = '超级管理员不允许删除';
            return false;
        }
        return $this->save(['status' => self::$status]);
    }


    /**
     * @Notes: 统计符合条件的数量
     * @Interface getCount
     * @param array $param
     * @return int|string
     * @throws \think\Exception
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2020/12/12   11:25 上午
     */
    public function getCount($param = [])
    {
        $where = [];
        if (!empty($param['phone'])) {
            $where['phone'] = $param['phone'];
        }
        return (new StoreUser)->where(['status' => ['<>',self::$status]])
            ->where($where)
            ->count();
    }

}
