<?php

namespace app\common\library;

use app\admin\model\Setting as SettingModel;
use app\common\model\Member;
use bl\Random;
use exception\BaseException;
use think\Config;
use think\Db;
use think\Exception;
use think\Hook;
use think\Request;
use think\Validate;

class Auth
{
    protected static $instance = null;
    protected        $_error   = '';
    protected        $_logined = false;
    protected        $_user    = null;
    protected        $_token   = '';
    //Token默认有效时长
    protected $keeptime   = 2592000;
    protected $requestUri = '';
    protected $rules      = [];
    //默认配置
    protected $config      = [];
    protected $options     = [];
    protected $allowFields = ['member_id', 'name', 'avatarUrl', 'phone', 'account', 'glory', 'volume_drop','code'];

    public function __construct($options = [])
    {
        if ($config = Config::get('user')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->options = array_merge($this->config, $options);
    }

    /**
     *
     * @param array $options 参数
     * @return Auth
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * 获取User模型
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * 兼容调用user模型的属性
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_user ? $this->_user->$name : null;
    }

    /**
     * 兼容调用user模型的属性
     */
    public function __isset($name)
    {
        return isset($this->_user) ? isset($this->_user->$name) : false;
    }

    /**
     * 根据Token初始化
     *
     * @param string $token Token
     * @return boolean
     */
    public function init($token)
    {
        if ($this->_logined) {
            return true;
        }
        if ($this->_error) {
            return false;
        }
        $data = Token::get($token);
        if (!$data) {
            return false;
        }
        $user_id = intval($data['user_id']);
        if ($user_id > 0) {
            $user = Member::get($user_id);
            if (!$user) {
                $this->setError('Account not exist');
                return false;
            }
            if ($user['status']['value'] != 1) {
                $this->setError('Account is locked');
                return false;
            }
            $this->_user    = $user;
            $this->_logined = true;
            $this->_token   = $token;
            return true;
        } else {
            $this->setError('You are not logged in');
            return false;
        }
    }

    /**
     * 注册用户
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     * @param array  $extend   扩展参数
     * @return boolean
     */
    public function register($username, $password, $email = '', $mobile = '', $extend = [], $invitation_code = '')
    {
        $prize_conf = SettingModel::getItem('prize');
        if (Member::where(['phone'=>$mobile])->find()) {
            $this->setError("账户已存在");
            return false;
        }
        $ip   = request()->ip();
        $time = time();
        $code = Random::alnum();
        $p_id = 0;
        if (!empty($invitation_code)) {
            $p_user_info = Member::where('code', $invitation_code)->field('member_id,p_id')->find();
            $p_id        = $p_user_info['member_id'] ?? 0;
            if (!$p_user_info) {
                $this->setError('邀请码错误');
                return false;
            }
        }
        $data               = [
            'phone'    => $username,
            'avatarUrl'      => request()->domain() . '/assets/touxiang.png',
            'code'        => $code,
            'p_id'        => $p_id,
        ];
        $params             = array_merge($data, [
            'name'  => "道友_" . getNicknameGuidV4(),
            'logintime' => $time,
            'loginip'   => $ip,
            'prevtime'  => $time,
            'status'    => '1',
        ]);
        $params['password'] = $this->getEncryptPassword($password);
        $params             = array_merge($params, $extend);
        //账号注册时需要开启事务,避免出现垃圾数据
        Db::startTrans();
        try {
            $user = Member::create($params, true);
            $this->_user = Member::get($user->member_id);
            if ($p_id!=0){
                Member::where(['member_id'=>$p_id])->setInc('prize_count',$prize_conf['prize_count']);
            }
            //设置Token
            $this->_token = Random::uuid();
            Token::set($this->_token, $user->member_id, $this->keeptime);
            //更新登录的 token
            Member::update(['login_token'=>$this->getToken()], ['member_id' => $user->member_id]);
            //设置登录状态
            $this->_logined = true;
            //注册成功的事件
            Db::commit();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
     * 用户登录
     *
     * @param string $account  手机号
     * @param string $password 密码
     * @return boolean
     */
    public function login($account, $password)
    {
        $user  = Member::get(['phone' => $account,'is_del' => 1]);
        if (!$user) {
            $this->setError("账号不存在,请先注册");
            return false;
        }
        if ($user->status['value'] != 1) {
            $this->setError('账户被禁用,请联系客服');
            return false;
        }
        if ($user->password != $this->getEncryptPassword($password)) {
            $this->setError('密码错误');
            return false;
        }
        //直接登录会员
        return $this->direct($user->member_id);
    }

    /**
     * 退出
     *
     * @return boolean
     */
    public function logout()
    {
        if (!$this->_logined) {
            $this->setError('You are not logged in');
            return false;
        }
        //设置登录标识
        $this->_logined = false;
        //删除Token
        Token::delete($this->_token);
        return true;
    }

    /**
     * 修改密码
     * @param string $newpassword       新密码
     * @param string $oldpassword       旧密码
     * @param bool   $ignoreoldpassword 忽略旧密码
     * @return boolean
     */
    public function changepwd($newpassword, $oldpassword = '', $ignoreoldpassword = false,$is_login = false)
    {

        if (!$this->_logined && $is_login) {
            $this->setError('You are not logged in');
            return false;
        }
        Db::startTrans();
        try {
            $newpassword = $this->getEncryptPassword($newpassword);
            if($is_login){
                $this->_user->save(['password' => $newpassword]);
            }else{
                (new Member())->where(['phone'=>$oldpassword])->update(['password' => $newpassword]);
            }
            Token::delete($this->_token);
            //修改密码成功的事件
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->setError($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 直接登录账号
     * @param int $user_id
     * @return boolean
     */
    public function direct($user_id)
    {
        $user = Member::get($user_id);
        if ($user) {
            $site_setting = SettingModel::getItem('site_setting');
            if(isset($site_setting['open_type']) && $site_setting['open_type']==1){
                if($user['member_type']!=10){
                    $this->setError('您不在内测名单;无法登录');
                    return false;
                }
            }
            Db::startTrans();
            try {
                $ip   = request()->ip();
                $time = time();
                //判断连续登录和最大连续登录
                if ($user->logintime < \bl\Date::unixtime('day')) {
                    $user->successions    = $user->logintime < \bl\Date::unixtime('day', -1) ? 1 : $user->successions + 1;
                    $user->maxsuccessions = max($user->successions, $user->maxsuccessions);
                }
                $user->prevtime = $user->logintime;
                //记录本次登录的IP和时间
                $user->loginip   = $ip;
                $user->logintime = $time;
                //重置登录失败次数
                $user->loginfailure = 0;
                $this->_user = $user;
                $this->_token = Random::uuid();
                Token::set($this->_token, $user->member_id, $this->keeptime);
                $this->_logined = true;
                $user->login_token = $this->getToken();
                $user->save();
                //登录成功的事件
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            $this->setError("会员信息不存在");
            return false;
        }
    }

    /**
     * 检测是否是否有对应权限
     * @param string $path   控制器/方法
     * @param string $module 模块 默认为当前模块
     * @return boolean
     */
    public function check($path = null, $module = null)
    {
        if (!$this->_logined) {
            return false;
        }

        $ruleList = $this->getRuleList();
        $rules    = [];
        foreach ($ruleList as $k => $v) {
            $rules[] = $v['name'];
        }
        $url = ($module ? $module : request()->module()) . '/' . (is_null($path) ? $this->getRequestUri() : $path);
        $url = strtolower(str_replace('.', '/', $url));
        return in_array($url, $rules) ? true : false;
    }

    /**
     * 判断是否登录
     * @return boolean
     */
    public function isLogin()
    {
        if ($this->_logined) {
            return true;
        }
        return false;
    }

    /**
     * 获取当前Token
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * 获取会员基本信息
     */
    public function getUserinfo()
    {
        $data        = $this->_user->toArray();
        $allowFields = $this->getAllowFields();
        $userinfo    = array_intersect_key($data, array_flip($allowFields));
        $userinfo    = array_merge($userinfo, Token::get($this->_token));
        unset($userinfo['user_id']);
        return $userinfo;
    }

    /**
     * 获取会员的member_id
     */
    public function getMemberId()
    {
        $data        = $this->_user->toArray();
        $allowFields = $this->getAllowFields();
        $userinfo    = array_intersect_key($data, array_flip($allowFields));
        return array_merge($userinfo, Token::get($this->_token))['user_id'];
    }


    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

    /**
     * 获取允许输出的字段
     * @return array
     */
    public function getAllowFields()
    {
        return $this->allowFields;
    }

    /**
     * 设置允许输出的字段
     * @param array $fields
     */
    public function setAllowFields($fields)
    {
        $this->allowFields = $fields;
    }



    /**
     * 检测操作密码是否正确
     * @param int    $member_id
     * @param string $operation_pwd
     * @return bool
     * @throws BaseException
     */
    public  function checkOperationPwd(int $member_id, string $operation_pwd): bool
    {
        /* 校验密码 */
        $member_salt   = self::getMemberSalt($member_id);
        $operation_pwd = self::encryptPassword($operation_pwd, $member_salt);
        return $operation_pwd === $this->_user->operation_pwd? true : false;
    }

    /**
     * 加密密码
     * @param string $password
     * @param string $salt
     * @return string
     */
    public static function encryptPassword(string $password, string $salt = ''): string
    {
        return md5(md5($password . $salt) . base64_encode($salt));
    }

    /**
     * 获取会员salt
     * @param int $member_id
     * @return string
     */
    public static function getMemberSalt(int $member_id): string
    {
        return substr(md5(md5($member_id) . base64_encode($member_id)), 0, 8);
    }


    /**
     * 获取密码加密后的字符串
     * @param string $password 密码
     * @param string $salt     密码盐
     * @return string
     */
    public function getEncryptPassword($password)
    {
        return md5($password);
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @return boolean
     */
    public function match($arr = [])
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

    /**
     * 设置会话有效时间
     * @param int $keeptime 默认为永久
     */
    public function keeptime($keeptime = 0)
    {
        $this->keeptime = $keeptime;
    }


    /**
     * 设置错误信息
     *
     * @param string $error 错误信息
     * @return Auth
     */
    public function setError($error)
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }
}
