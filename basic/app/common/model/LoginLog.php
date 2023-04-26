<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/11/10   23:28
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className:登录日志记录操作
 * +----------------------------------------------------------------------
 */

namespace app\common\model;

class LoginLog extends BaseModel
{
    protected $name = 'login_log';

    /**
     * 类型
     * @param $value
     * @return mixed
     */
    public function getTypeAttr($value)
    {
        $status = [1 => '登录', 2 => '修改' ,3=>'添加' ,4=>'删除'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * @Notes: 添加记录
     * @Interface add
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/11/10   23:30
     */
    public function add($data)
    {
        //触发器增加佣金
        $data = array_merge($data, [
            'app_id' => self::$app_id,
        ]);
        return $this->allowField(true)->save($data);
    }

    /**
     * @Notes:获取数据集
     * @Interface listData
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/11/11   11:32
     */
    public function listData($param)
    {
        $limit = $param['limit'];
        $offset = ($param['page'] - 1) * $limit;
        $where = [];
        if (!empty($param['create_time'])) { //根据下单时间筛选
            $create_time = explode('~',$param['create_time']);
            $where['create_time'] = ['between time',[trim($create_time[0]),trim($create_time[1])]];
        }
        if (!empty($param['nickname'])) { //根据管理员昵称
            $where['nickname'] = ['like', '%' . $param['nickname'] . '%'];
        }
        $StoreUser = $this
            ->where($where)
            ->order(['create_time' => 'desc'])
            ->limit($offset,$limit)
            ->select();
        $return['count'] = $this->where($where)->count();
        $return['data'] = $StoreUser;
        $return['code'] = '0';
        $return['msg'] = 'OK';
        return $return;
    }

}