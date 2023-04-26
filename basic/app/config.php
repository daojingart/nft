<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Env;

return [
    // +----------------------------------------------------------------------
    // | 应用设置 V1.0.7
    // +----------------------------------------------------------------------
    // 应用调试模式
    'app_debug'              => Env::get('app.debug', false),
    // 应用Trace
    'app_trace'              => Env::get('app.trace', false),
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [APP_PATH . 'helper.php', THINK_PATH . 'helper.php', APP_PATH . 'functions.php'],
    // 默认输出类型
    'default_return_type'    => 'json',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'htmlspecialchars',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['admin'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => '',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'              => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'      => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'   => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'        => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'         => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'        => false,

    'exception_handle' => '\\exception\\ExceptionHandler',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'   => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => ['error'],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace' => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache' => [

        'type' => 'complex',

		'default' => [
			// 驱动方式
			'type'       => 'Redis',
			// 服务器地址
			'host'       => Env::get('redis.host', '127.0.0.1'),
			'port'       => Env::get('redis.port', 6379),
			'password'   => Env::get('redis.password'),
			'select'     => 5,
			'expire'     => (86400 * 3),
			'persistent' => true,
			'timeout'    => Env::get('redis.timeout', 0.3),
			'prefix'     => Env::get('app.name', 'bl') . "_" . Env::get('redis.prefix', 'c:'),
		],


		'redis' => [
            // 驱动方式
            'type'       => 'Redis',
            // 服务器地址
            'host'       => Env::get('redis.host', '127.0.0.1'),
            'port'       => Env::get('redis.port', 6379),
            'password'   => Env::get('redis.password'),
            'select'     => Env::get('redis.select', 8),
            'expire'     => (86400 * 3),
            'persistent' => true,
            'timeout'    => Env::get('redis.timeout', 0.3),
            'prefix'     => Env::get('redis.prefix', 'nft_c:'),
        ],
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'       => [
        'host'         => Env::get('redis.host', '127.0.0.1'),
        'port'         => Env::get('redis.port', 6379),
        'password'     => Env::get('redis.password'),
        'select'       => Env::get('redis.select', 3),
        'session_name' => 'nft_s:',
        'expire'       => 86400,
        // 驱动方式 支持redis memcache memcached
        'type'         => 'redis',
        // 是否自动开启 SESSION
        'auto_start'   => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'        => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'      => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],

    // 自定义配置文件
    'key'           => 'HeNanBaLiuHuLian',
    'version'    => '3.0.0',
    'exp'           => '86400',
    'open_platform' => '2', //是否绑定开放平台 1没有绑定  2绑定了
    'app_id'        => '10001',
    'is_wechat'     => '1', //1 开启小程序模块  2关闭小程序模块
    'in_online'     => Env::get('app.debug', false) ? 'test' : 'online', //1 开启小程序模块  2关闭小程序模块

    // +----------------------------------------------------------------------
    // | Token设置
    // +----------------------------------------------------------------------
    'token'                       => [
        // 驱动方式
        'type'        => 'Redis',
        // 缓存前缀
        'key'         => 'TniLrDKEsyYHxwe0IPJtclQUg65modSF',
        // 加密方式
        'hashalgo'    => 'ripemd160',
        // 缓存有效期 0表示永久缓存
        'expire'      => (86400 * 3),
        'host'        => Env::get('redis.host', '127.0.0.1'),
        'port'        => Env::get('redis.port', 6379),
        'password'    => Env::get('redis.password'),
        'select'      => 8,
        'timeout'     => 0,
        'persistent'  => true,
        'userprefix'  => Env::get('project.name', 'bl') . '_up:',
        'tokenprefix' => Env::get('project.name', 'bl') . '_tp:',
    ],
    'payConfig' => [
        'wechat' => [
            'notify_url' => Env::get('ws.timer_host_url').'notice/Notify/order',
        ],
        'aliPay' => [
            'notify_url' => Env::get('ws.timer_host_url').'notice/Notify/order',
            'return_url' => Env::get('ws.timer_host_url'),
        ],
        'llPay' => [
            'openacct_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notify/llOpenAccount', //开户异步回调地址
            'openacct_return_url' =>  Env::get('ws.timer_host_url').'Notify/llOpenAccount', //开户同步跳转
            'bindcard_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notify/llBindCard', //绑定银行卡异步回调地址
            'unset_bindcard_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notify/llBindCard', //解除绑定银行卡异步回调地址
            'pay_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notify/llBindCard', //支付异步回调地址
            'withdrawal_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notify/llBindCard', //提现异步支付通知
        ],
        'sdPay' => [
            'openacct_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notifysd/Cloudnotify', //开户异步回调地址
            'return_url' =>  Env::get('ws.timer_host_url'), //开户同步跳转
            'quick_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notifysd/quickNotifyUrl', //快捷支付异步回调地址
            'account_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notifysd/accountNotifyUrl', //钱包开户费异步回调地址
            'c2c_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notifysd/synchronizeNotifyC2C', //二级交易市场分账通知
            'c2b_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notifysd/synchronizeNotifyC2B', //一级市场云账户交易异步通知
        ],
        'adaPay' => [
            'bindCard_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notifyada/adaBindCard', //绑卡异步回调通知
            'unbindCard_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notifyada/unbindCard', //绑卡异步回调通知
            'pay_notify_url' =>  Env::get('ws.timer_host_url').'/notice/Notifyada/payment', //绑卡异步回调通知
        ],
        'hyPay' => [
            'bindCard_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notifyhy/hyBankNotice', //绑定银行卡申请回调
            'return_url' =>  Env::get('ws.timer_host_url'), //开户同步跳转
            'pay_notify_url' => Env::get('ws.timer_host_url').'notice/Notifyhy/hyCallback', //支付回调通知
            'pay_return_url' => Env::get('ws.timer_host_url').'notice/Notifyhy/hyReturn', //支付回调通知
        ],
        'hytPay' => [
            'openWallet_notify_url' =>  Env::get('ws.timer_host_url').'notice/Notifyhf/walletNoticle', //钱包开户
            'return_url' =>  Env::get('ws.timer_host_url'), //开户同步跳转
            'pay_notify_url' => Env::get('ws.timer_host_url').'notice/Notifyhf/asynchronousFree', //手续费支付回调通知
            'pay_return_url' => Env::get('ws.timer_host_url').'h5/h5.html#/pages/main/personal', //手续费支付回调通知
            'wallet_notify_url' => Env::get('ws.timer_host_url').'notice/Notifyhf/callbackPaynotify', //正常支付回调
        ],

    ],

];
