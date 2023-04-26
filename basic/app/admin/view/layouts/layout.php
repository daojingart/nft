<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="renderer" content="webkit"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-title" content="BL-Admin"/>
    <title>{{$setting['store']['values']['system_name']}}</title>
    <!-- 依 赖 样 式 【河南八六互联信息技术有限公司】 -->
    <link rel="stylesheet" href="/assets/admin/css/pear.css" />
    <!-- 加 载 样 式-->
    <link rel="stylesheet" href="/assets/admin/css/load.css" />
    <!-- 布 局 样 式 -->
    <link rel="stylesheet" href="/assets/admin/css/admin.css" />
    <link href="{{$icon}}" rel="shortcut icon">
    <!-- 依 赖 脚 本 -->
    <script src="/assets/admin/jquery.min.js"></script>
    <script src="/assets/admin/layui/layui.js"></script>
    <script src="/assets/admin/watermark.js"></script>
    <script src="/assets/admin/pear.js"></script>
    <script src="/assets/admin/jquery.form.min.js"></script>
    <script src="/assets/admin/modules/webuploader.html5only.js"></script>
    <script src="/assets/admin/modules/art-template.js"></script>
    <script src="/assets/admin/app.js"></script>
    <script src="/assets/admin/modules/file.library.js"></script>

    <!-- 依 赖 脚 本 -->
    <script>
        BASE_URL = '<?= isset($base_url) ? $base_url : '' ?>';
        STORE_URL = '<?= isset($store_url) ? $store_url : '' ?>';
    </script>
    <!-- 依 赖 脚 本 -->

</head>
<!-- 结 构 代 码 -->
<body class="layui-layout-body pear-admin">
    <!-- 布 局 框 架 -->
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 顶 部 左 侧 功 能 -->
            <ul class="layui-nav layui-layout-left">
                <li class="refresh layui-nav-item"><a href="#" class="layui-icon layui-icon-refresh-1 refresh-button"></a></li>
            </ul>
            <!-- 顶 部 右 侧 菜 单 -->
            <div id="control" class="layui-layout-control"></div>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item layui-hide-xs">
                    <a  href="https://doc.86itn.cn/find" target="_blank" id="upgrade">
                        帮助手册
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs layui-hide-left"></li>
                <li class="layui-nav-item layui-hide-xs">
                    <a href="#" data-toggle="dropdown" data-hover="dropdown" aria-expanded="true">
                        关注八六互联
                    </a>
                    <dl class="layui-nav-child" style="text-align: center">
                        关注接收最新资讯
                        <div>
                            <img height="115px" src="http://qt.86itn.com/code.jpg" alt="">
                        </div>
                    </dl>
                </li>
                <li class="layui-nav-item layui-hide-xs layui-hide-left"></li>
                <li class="layui-nav-item layui-hide-xs">
                    <a href="{{:url('version.version/index')}}" target="_blank" id="upgrade">
                        <i class="layui-icon layui-icon-upload-drag" style="color: red;font-size: 16px"></i>
                         当前版本号{{$version}}
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs layui-hide-left"></li>
                <li class="layui-nav-item layui-hide-xs">
                    <a href="#">访问H5</a>
                    <dl class="layui-nav-child" style="text-align: center">
                        微信扫码访问店铺
                        <div>
                            <img height="115px" src="https://api.pwmqr.com/qrcode/create/?url={{$host}}" alt="河南八六互联出品">
                        </div>
                    </dl>
                </li>
                <li class="layui-nav-item layui-hide-xs layui-hide-left"></li>
                <li class="layui-nav-item">
                    <!-- 头 像 -->
                    <a href="javascript:;">
                        <img src="/assets/admin/images/myface.png" class="layui-nav-img">
                        <span class="layui-nav-more"></span>{{$store['user']['user_name']}}</a>
                    <!-- 功 能 菜 单 -->
                    <dl class="layui-nav-child layui-anim layui-anim-upbit">
                        <dd><a href="<?= url('auth.user/renew',['store_user_id'=>$store['user']['store_user_id']]) ?>">基本资料</a></dd>
                        <dd><a href="<?= url('login/logout') ?>" class="logout">注销登录</a></dd>
                    </dl>
                </li>
            </ul>
        </div>

        <!-- 侧 边 区 域 -->
        <div class="left-sidebar dis-flex">
            <?php $menus = $menus ?: [];?>
            <?php $group = $group ?: 0;?>
            <!-- 一级菜单 -->
            <ul class="sidebar-nav">
                <li class="sidebar-nav-heading">
                    {{$setting['store']['values']['system_name']}}
                </li>
                <?php foreach ($menus as $key => $item):
                    ?>
                    <li class="sidebar-nav-link">
                        <a href="<?= isset($item['index']) ? url($item['index']) : 'javascript:void(0);' ?>" class="<?= $item['active'] ? 'active' : '' ?>">
                            <?php if (isset($item['is_svg']) && $item['is_svg'] === true): ?>
                                <svg class="icon sidebar-nav-link-logo" aria-hidden="true">
                                    <use xlink:href="#<?= $item['icon'] ?>"></use>
                                </svg>
                            <?php else: ?>
                                <i class="layui-icon  {{if !empty($item.icon)}}{{$item.icon}}{{else/}}  layui-icon-console {{/if}}"></i>
                            <?php endif; ?>

                            <?= $item['name'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- 一级菜单 -->

            <!-- 子级菜单-->
            <?php $second = isset($menus[$group]['submenu']) ? $menus[$group]['submenu'] : []; ?>
            <?php if (!empty($second)) :?>
                <ul class="left-sidebar-second" style="overflow-y: auto;padding-bottom: 20px">
                    <li class="sidebar-second-title">
                        <span style="border: #0b8df1 2px solid;margin-right: 6px;display: inline;"></span>
                        <?= $menus[$group]['name'] ?>
                    </li>
                    <li class="sidebar-second-item">
                        <?php foreach ($second as $item) :
//                            pre($item);
                            ?>
                            <?php if (!isset($item['submenu']) && $item['disabled']==1): ?>
                                <!-- 二级菜单-->
                                <a href="<?= url($item['index']) ?>" class="<?= $item['active'] ? 'active' : '' ?>">
                                    <?= $item['name']; ?>
                                </a>
                            <?php else: ?>
                                <!-- 三级菜单-->
                                <div class="sidebar-third-item">
                                    <a href="javascript:void(0);" class="sidebar-nav-sub-title sidebar-nav-sub-title-node">
                                        <span style="border: #0b8df1 2px solid;margin-right: 6px;display: inline;"></span>
                                        <?= $item['name']; ?>
                                    </a>
                                    <ul class="sidebar-third-nav-sub">
                                        <?php foreach ($item['submenu'] as $third) : ?>
                                            <li>
                                                <a class="<?= $third['active'] ? 'active' : '' ?>" href="<?= url($third['index']) ?>">
                                                    <?= $third['name']; ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </li>
                </ul>
            <?php endif; ?>
        </div>

        <!-- 视 图 页 面 -->
        <div class="layui-body <?= empty($second) ? 'no-sidebar-second' : '' ?>">
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    {__CONTENT__}
                </div>
            </div>
        </div>
        <!-- 遮 盖 层 -->
        <div class="pear-cover"></div>
    </div>
    <!-- 移 动 端 便 捷 操 作 -->
    <div class="pear-collasped-pe collaspe">
        <a href="#" class="layui-icon layui-icon-shrink-right"></a>
    </div>
    <!-- 框 架 初 始 化 -->
    <script>
        layui.use(['admin','jquery','convert','popup'], function() {
            var admin = layui.admin;
            var $ = layui.jquery;
            admin.setConfigType("json");
            admin.setConfigPath("/config/pear.config.json");
            admin.render();

        })
        var text=$("#demodiv>input").val();

    </script>

</body>
</html>