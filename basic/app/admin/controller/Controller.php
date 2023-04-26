<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 基类控制器
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller;

use app\admin\model\auth\StoreNode;
use app\admin\model\Setting;
use app\admin\service\Menus;
use app\common\server\LoginLog;
use Bl\BaLiu;
use Bl\Nft\AdminController;
use Exception;
use think\Cookie;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Request;


class Controller extends \think\Controller
{
    /**
     * @var array //存储登录信息
     */
    protected $store;

    /**
     * @var string //获取控制器名称
     */
    protected $controller = '';

    /**
     * @var string //获取方法名称
     */
    protected $action = '';

    /**
     * @var string //获取子路由
     */
    protected $routeUri = '';

    /**
     * @var string //获取分组名称
     */
    protected $group = '';

    /**
     * @var string //验证白名单
     */

    protected $allowAllAction = [
        // 登录页面
        'login/login',
    ];

    /* @var array $notLayoutAction 无需全局layout */
    protected $notLayoutAction = [
        // 登录页面
        'login/login',
    ];


        /**
     * @Notes     : 后台初始化
     * @Interface _initialize
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2020/12/15   1:55 下午
     */
    public function _initialize()
    {
        //初始化信息
        BaLiu::instance();
        // 商家登录信息
        $this->store = Cookie::get('blhlstore');
        // 当前路由信息
        $this->getRouteinfo();
        // 验证登录
        $this->checkLogin();
        // 全局layout
        $this->layout();
        //写入操作日志
        $this->createActionLog();
    }

    /**
     * @Notes     : 全局信息展示
     * @Interface layout
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2020/12/15   1:54 下午
     */
    private function layout()
    {
        // 验证请求是否合法
        if (!in_array($this->routeUri, $this->notLayoutAction)) {
            //获取二维码
            // 输出到view
            $this->assign([
                'base_url'  => base_url(),                      // 当前域名
                'store_url' => url('/admin'),              // 后台模块url
                'menus'     => $this->menus(),                     // 后台菜单
                'group'     => $this->group,
                'store'     => $this->store,                       // 存储后台登录信息
                'setting'   => Setting::getAll() ?: null,        // 当前登录系统的配置
                'host'      => HOST . "/h5/h5.html",
                'version'   => getVersion(),
                'icon' => $setting = setting::getAll()['store']['values']['ico_images']
            ]);
        }
    }

    /**
     * 解析当前路由参数 （分组名称、控制器名称、方法名）
     */
    protected function getRouteInfo()
    {
        // 控制器名称
        $this->controller = toUnderScore($this->request->controller());
        // 方法名称
        $this->action = $this->request->action();
        // 控制器分组 (用于定义所属模块)
        $groupStr    = strstr($this->controller, '.', true);
        $this->group = $groupStr !== false ? $groupStr : $this->controller;
        // 当前uri
        $this->routeUri = $this->controller . '/' . $this->action;
    }

    /**
     * 写入操作日志
     * @author    : [Mr.Zhang] [1040657944@qq.com]
     * @Interface createActionLog
     * @Time      : 2022/2/18   23:03
     * @todo      [描述] // 待办。提示自己或他人还需要做些什么
     */
    protected function createActionLog()
    {

        switch ($this->action) {
            case "add"://增加数据
                if ($this->request->isAjax()) {
                    (new LoginLog())->insertLoginLog(2, "增加数据");
                }
                break;
            case "renew":
                if ($this->request->isAjax()) {
                    (new LoginLog())->insertLoginLog(2, "更新数据");
                }
                break;
            case "remove"://增加数据
                (new LoginLog())->insertLoginLog(2, "删除数据");
                break;
        }


    }

    /**
     * 后台菜单配置
     * @return mixed
     * @throws \think\exception\DbException
     */
    protected function menus()
    {
        //判断下当前登录角色
        $nodeStr = rtrim($this->store['user']['user_rule'], ',');
        // 超级管理员没有节点数组 * 号表示
        $where          = '*' == $nodeStr ? [] : 'id in(' . $nodeStr . ')' . "";
        $StoreNodeModel = new StoreNode();
        $data           = $StoreNodeModel->getNodeTrees(objToArray($StoreNodeModel->getNodeTreeList($where)));
        static $menus = [];
        if (empty($menus)) {
            $menus = (new Menus())->getMenus($this->routeUri, $this->group, $data);
        }
        return $menus;
    }


    /**
     * @Notes     : 白名单验证登录
     * @Interface checkLogin
     * @return bool
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   2:33 下午
     */
    private function checkLogin()
    {
        // 验证当前请求是否在白名单
        if (in_array($this->routeUri, $this->allowAllAction)) {
            return true;
        }
        //验证登录状态
        if (!AdminController::instance()->checkLogin($this->store)) {
            $this->redirect('login/login');
            return false;
        }
        return true;
    }


    /**
     * @Notes     : 获取当前应用的APPID
     * @Interface getBlappId
     * @return mixed
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/3/26   2:34 下午
     */
    protected function getBlappId()
    {
        return $this->store['blapp']['app_id'];
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @param int    $code
     * @param string $msg
     * @param string $url
     * @param array  $data
     * @return array
     */
    protected function renderJson($code = 1, $msg = '', $url = '', $data = [])
    {
        return AdminController::instance()->renderJson($code, $msg, $url, $data);
    }

    /**
     * 返回操作成功json
     * @param string $msg
     * @param string $url
     * @param array  $data
     * @return array
     */
    protected function renderSuccess($msg = 'success', $url = '', $data = [])
    {
        return $this->renderJson(1, $msg, $url, $data);
    }

    /**
     * 返回操作失败json
     * @param string $msg
     * @param string $url
     * @param array  $data
     * @return array
     */
    protected function renderError($msg = 'error', $url = '', $data = [])
    {
        return $this->renderJson(0, $msg, $url, $data);
    }

    /**
     * 获取post数据 (数组)
     * @param $key
     * @return mixed
     */
    protected function postData($key)
    {
        return $this->request->post($key . '/a');
    }

    /**
     * 获取post数据
     *
     * @param string|null $name
     * @param null        $defaultValue
     * @return array|mixed|null
     * @throws Exception
     * @author    Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time      2021/12/1 17:08
     */
    public function post(string $name = null, $defaultValue = null)
    {
        return $this->request->post($name, $defaultValue);
    }

    /**
     * 获取get数据
     *
     * @param string|null $name
     * @param null        $defaultValue
     * @return array|mixed
     * @copyright 河南八六互联信息技术有限公司
     * @Time      2021/12/1 17:07
     * @author    Mr.Liu
     */
    public function get(string $name = null, $defaultValue = null)
    {
        return $this->request->get($name, $defaultValue);
    }

}
