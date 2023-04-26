<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/11/10   23:34
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 日志记录
 * +----------------------------------------------------------------------
 */

namespace app\common\server;

use app\common\model\StoreNode;
use think\Controller;
use think\Session;
use app\common\model\LoginLog as LoginLogModel;

class LoginLog extends Controller
{

    /**
     * @Notes: 添加日志 每个控制曹组方法统一调用
     * @Interface insertLoginLog
     * @param $type
     * @param $content
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/11/10   23:46
     */
    public function insertLoginLog($type,$content)
    {
        //获取登录商户的信息
        $blhlstore = Session::get('blhlstore');
        //查询操作的模块 获取当前操作的路径
        $controller = toUnderScore($this->request->controller());
        $action = $this->request->action();
        //查询数据库
        $storeNodeInfo['node_name'] = "登录系统后台";
        if($type != 1){
            $storeNodeInfo = (new StoreNode())->where(['controller_name'=>$controller,'action_name'=>$action])->find();
        }
        $login_ip = $this->request->ip();
        (new LoginLogModel())->add([
            'member_id' => $blhlstore['user']['store_user_id'],
            'login_ip' => $login_ip,
            'function' => $storeNodeInfo['node_name'],
            'type' => $type,
            'content' => $content,
            'nickname' => $blhlstore['user']['user_name'],
        ]);
    }

}