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
 * | className: API 基类控制器
 * +----------------------------------------------------------------------
 */

namespace app\common\controller;

use app\common\helpers\Tools;
use app\common\library\Auth;
use app\common\library\Token;
use Bl\BaLiu;
use Bl\Tools\EncrypTool;
use Bl\Tools\FirewallTool;
use think\Cache;
use think\exception\HttpResponseException;
use think\Loader;
use think\Request;
use think\Response;

class Controller
{

    /*应用的APP_ID*/
    protected $app_id;

    /**
     * 不需要验证签名的方法
     * @var array
     */
    protected $noNeedSign = [];

    /**
     * 不需要加密的方法
     * @var array
     */
    protected $noNeedEncrypt = [];

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['*'];

    public $redis;

    /**
     * 构造方法
     * @access public
     * @param Request $request Request 对象
     */
    public function __construct(Request $request = null)
    {
        $this->request = is_null($request) ? Request::instance() : $request;
        if ($this->request->isOptions()) {
            $this->success('success');
        }
        BaLiu::instance();
        // 控制器初始化
        $this->_initialize();
        //当前应用的APPID
        $this->app_id = 10001;
    }


    /**
     * @Notes     :基类初始化
     * @Interface _initialize
     * @author    : Mr.Zhang
     * @copyright : 河南八六互联信息技术有限公司
     * @Time      : 2021/4/12   2:17 下午
     */
    protected function _initialize()
    {
        //跨域请求检测
//        check_cors_request();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
        $this->auth = Auth::instance();
        $this->redis = initRedis();
        //验证签名
        $this->checkSign();
        $controllername = Loader::parseName($this->request->controller());
        $actionname     = strtolower($this->request->action());
        $token = $this->request->server('HTTP_TOKEN', $this->request->request('token', \think\Cookie::get('token')));
        $token = $token ?: $this->request->param('token');
        $path = str_replace('.', '/', $controllername) . '/' . $actionname;
        $this->auth->setRequestUri($path);
        FirewallTool::instance()->run(0,$this->request->ip(),"/api/".$path);
        if (!$this->auth->match($this->noNeedLogin)) {
            $this->auth->init($token);
            //检测是否登录
            if (!$this->auth->isLogin()) {
                $this->error("请先登录", null, 401);
            }
            //账号唯一登录
            if($token != $this->auth->login_token){
                Token::rm($token);
                $this->error("账号在其他设备登录,请重新登录！", null, 401);
            }
            FirewallTool::instance()->run($this->auth->member_id,$this->request->ip(),"/api/".$path);
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($path)) {
                    $this->error("You have no permission", null, 403);
                }
            }
        } else {
            // 如果有传递token才验证是否登录状态
            if ($token) {
                $this->auth->init($token);
            }
        }
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
     * 检验参数
     * @param array $rule
     * @param array $message
     * @param array $params
     * @return mixed
     */
    protected function checkParam($rule, $message = [], $params = []): array
    {
        if ($params) {
            $data = $params;
        } else {
            $data = $this->request->param();
        }
        $res = $this->validate($data, $rule, $message);
        ($res !== true) && base_exception(['msg' => $res]);

        return $data;
    }

    /**
     * 验证签名
     * @return void
     */
    private function checkSign()
    {
        if (!$this->match($this->noNeedSign)) {
            $time = $this->request->server('HTTP_TIME', 0, 'int');
            (!$time) && $this->error("请求签名错误 code:-3");
            ($time < (time() - 3600) || $time > (time() + 3600)) && $this->error("请求签名错误： code:-4");
            $device = $this->request->server('HTTP_DEVICE');
            (!$device) && $this->error("请求签名参数缺失 -1");
            $version = $this->request->server('HTTP_VERSION');
            (!$version) && $this->error("请求签名参数缺失 -2");
            $client = $this->request->server('HTTP_CLIENT');
            (!$client) && $this->error("请求签名参数缺失 -3");
            $server_sign = EncrypTool::instance()->genSign();

            $sign = $this->request->server('HTTP_SIGN');
            ($server_sign['sign'] !== $sign) && $this->error("请求签名错误");
            // 是否校验签名次数
            if ($checkSignNum = 1) {
                $key = "sign:" . $sign;
                $num = Cache::get($key);
                $num = $num ?: 0;
                if ($num >= $checkSignNum) {

                    $this->error("请求签名过期，请刷新后重试");
                }
                Cache::set($key, $num + 1, 3600);
            }
        }
    }

    /**
     * 操作失败返回的数据 API专用
     * @param string $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型
     * @param array  $header 发送的 Header 信息
     */
    protected function error($msg = '', $data = null, $code = 0, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 操作成功返回的数据 API专用
     * @param string|mixed $msg    提示信息或直接返回数据
     * @param mixed        $data   要返回的数据
     * @param int          $code   错误码，默认为1
     * @param string       $type   输出类型
     * @param array        $header 发送的 Header 信息
     */
    protected function success($msg = '', $data = null, $code = 1, $type = null, array $header = [])
    {
        if (!is_string($msg) && $data == null) {
            $data = $msg;
            $msg  = 'success';
        }
        $this->result($msg, $data, $code, $type, $header);
    }


    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed  $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型，支持json/xml/jsonp
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function result($msg, $data = null, $code = 0, $type = null, array $header = [])
    {

        $sign    = $this->request->server('HTTP_SIGN');
        $time    = $this->request->server('REQUEST_TIME');
        $encrypt = 0;
        if (!$this->match($this->noNeedEncrypt)) {
            $data    = json_encode($data);
            $data    = Tools::aes_encrypt($data, $sign, "bl_key_{$time}");
            $encrypt = 1;
        }
        $runtime = round(microtime(true) - THINK_START_TIME, 10);
        $reqs    = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $result = [
            'code'      => $code,
            'msg'       => $msg,
            'encrypt'   => $encrypt,
            'time'      => $time,
            'sign'      => $sign,
            'used_time' => $runtime,
            'reqs'      => $reqs,
            'data'      => $data,
        ];
        // 如果未设置类型则自动判断
        $response = Response::create($result, 'json')->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @return boolean
     */
    protected function match($arr = [])
    {
        $request = Request::instance();
        $arr     = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr) {
            return false;
        }
        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr)) {
            return true;
        }
        // 没找到匹配
        return false;
    }

}
