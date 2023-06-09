八六互数字藏品发行交易平台【八六数藏系统】
===============
## 公司介绍
河南八六互联信息技术有限公司开发的抽奖拼团商城系统是依托于八六互联多年的技术沉淀，通过优秀的产品经理,调研设计，并开始研发.
在经过技术团队的精心打磨下产品初具雏形,河南八六互联是一家技术服务型技术,多年的互联网行业经验，开发优秀的互联网产品，欢迎新老客户使用。

## 产品架构
+ 基于MVC系统架构
+ 前后端分离
+ 前端TOKEN 基于JWT验证
+ 引入中间件Redis
+ 采用Mysql 开源数据库
+ 基于Layui+AmazeUI 开发后台管理系统
+ 集成微信、支付宝第三方支付接口
+ 集成腾域、腾讯、阿里、互亿多家短信产品
+ 集成七牛云第三方存储
+ 基于thinkPHP 5.0 开发
+ 基于Uniapp 框架支持跨端开发
+ 友好的二开便捷性

> 系统的运行环境要求
>
> 【PHP版本 7.4】
>
> 【Mysql 版本5.7】
>
> 【Nginx 1.18】
>
> 【Redis】
>

## 移除禁用函数

```
putenv
pcntl_fork
pcntl_signal
```

⚠️ 框架采用Tp5进行二次开发;核心目录已做修改请勿尝试升级Tp5.0核心框架以免照成不必要的损失

## 目录结构

目录结构如下：

~~~
basic  系统目录
├─app           应用目录
│  ├─admin              后台管理模块
│  ├─common             公共模块
│  ├─api                API 接口模块
│  │─notice             第三方回调模块
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  ├─database.php       数据库配置文件
│─extend                扩展类库目录
│─thinkphp              框架系统目录
├─vendor                第三方类库目录（Composer依赖库）
web                    WEB目录（对外访问目录）
├─index.php            入口文件
├─h5                   公众号前端入口文件
├─router.php           快速测试文件
└─.htaccess            用于apache的重写
~~~

> router.php用于php自带webserver支持，可用于快速测试
> 切换到public目录后，启动命令：php -S localhost:8888  router.php
> 上面的目录结构和名称是可以改变的，这取决于你的入口文件和配置参数。

## 命名规范

`该系统`遵循PSR-2命名规范和PSR-4自动加载规范，并且注意如下规范：

### 目录和文件

*   目录不强制规范，驼峰和小写+下划线模式均支持；
*   类库、函数文件统一以`.php`为后缀；
*   类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
*   类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

### 函数和类、属性命名

*   类的命名采用驼峰法，并且首字母大写，例如 `User`、`UserType`，默认不需要添加后缀，例如`UserController`应该直接命名为`User`；
*   函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 `get_client_ip`；
*   方法的命名使用驼峰法，并且首字母小写，例如 `getUserName`；
*   属性的命名使用驼峰法，并且首字母小写，例如 `tableName`、`instance`；
*   以双下划线“__”打头的函数或方法作为魔法方法，例如 `__call` 和 `__autoload`；

### 常量和配置

*   常量以大写字母和下划线命名，例如 `APP_PATH`和 `THINK_PATH`；
*   配置参数以小写字母和下划线命名，例如 `url_route_on` 和`url_convert`；

### 数据表和字段

*   数据表和字段采用小写加下划线方式命名，并注意字段名不要以下划线开头，例如 `think_user` 表和 `user_name`字段，不建议使用驼峰和中文作为数据表字段命名。

### 特殊机制
* 队列 配合 Supervisor 做进程守护
* php think queue:listen --queue sendair 空投和白名单发放
* php think queue:listen --queue order 订单回调

## 版权信息

该系统为商业授权系统未经河南八六互联信息技术有限公司授权不能使用该系统,强行破解、反编译等采取任何手段未经授权的情况下使用本系统视为侵犯本系统的合法权益，
公司将采取法律手段维护自身权益,对于未经授权的系统出现的一切问题与本公司(河南八六互联信息技术有限公司)无关。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2021-2028 by 河南八六互联信息技术有限公司 (https://86itn.com/)

All rights reserved。

系统著作权所有者为河南八六互联信息技术有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)
