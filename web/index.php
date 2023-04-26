<?php
// 检测PHP版本
if (version_compare(PHP_VERSION, '7.1.0', '<=')) die('please use require PHP >= 7.1.0 !');
(!class_exists(\Bl\BaLiu::class)) && exit("当前服务器信息已记录,请先安装八六互联安全组件,保证程序的安全运行");
$type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'https://';
define('HOST', $type . str_replace($type, '', $_SERVER['HTTP_HOST']));
// 定义运行目录
define('WEB_PATH', __DIR__ . '/');
// 定义应用目录
define('APP_PATH', WEB_PATH . '../basic/app/');
// 加载框架引导文件
require APP_PATH . '../thinkphp/start.php';
