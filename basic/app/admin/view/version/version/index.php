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
    <style>
        .layui-body {
            position: relative;
             margin-left:0  !important;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 900;
            width: auto;
            box-sizing: border-box;
        }
        .upgrade_admin .layui-layout-admin .layui-header{
            width: 100%;
            height: 64px;
            background-color: #fff;
            display: flex;
            align-items: center;
            padding: 0;
            margin: 0;
        }
        .upgrade_admin .layui-body {
            left: 0;
            height: calc(100vh - 64px);
            background-color: #f3f5f7;
            overflow: auto;
        }
        .upgrade_admin .layui-content-card{
            margin: 24px 120px 74px 120px;
            height: calc(100% - 98px);
            background: #fff;
            border-radius: 16px;
            padding-left: 200px;
            padding-top: 64px;
            padding-right: 80px;
            overflow: hidden;
        }
        .upgrade_admin .le-header-name{
            font-size: 16px;
            font-family: Microsoft YaHei;
            font-weight: 700;
            color: #262626;
            margin-left: 20px;
        }
        .layui-timeline-item i{
            color: #ffb80000;
            background: #dcdfe6;
        }
        .layui-timeline-item .le-left {
            position: absolute;
            left: -135px;
            text-align: right;
            width: 115px;
        }
        .layui-timeline-item .le-left .le-version{
            font-size: 20px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #222;
            text-align: right;
        }
        .layui-timeline-item .le-left .le-time{
            font-size: 14px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #999;
            text-align: right;
        }
        .layui-timeline-item .le-left .le-version .le-version-new{
            display: inline-block;
            text-align: center;
            width: 42px;
            height: 20px;
            line-height: 20px;
            background: #1E9FFF;
            border-radius: 10px 10px 0 10px;
            font-size: 14px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #fff;
        }
        .layui-timeline-item .layui-timeline-content  .le-content-info{
            width: 100%;
            background: #f7f7f7;
            border-radius: 0 16px 16px 16px;
            padding: 24px;
        }
        .layui-timeline-item .layui-timeline-content .le-content-header{
            margin-bottom: 10px;
        }
        .align-center {
            align-items: center;
        }
        .flex {
            display: flex;
        }
        .layui-timeline-item .layui-timeline-content .le-button{
            width: 118px;
            height: 32px;
            background: #1E9FFF;
            border-radius: 4px;
            font-size: 14px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #fff;
            outline: none;
            border: none;
            cursor: pointer;
            margin-right: 15px;
        }
        .layui-timeline-item .layui-timeline-content .le-prompt{
            font-size: 14px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #595959;
        }
        .layui-timeline-item .layui-timeline-content .le-host-version span:last-child {
            margin-left: 5px;
            line-height: 1;
        }
        .layui-timeline-item .layui-timeline-content .le-host-version span{
            font-size: 14px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #1E9FFF;
        }
        .layui-timeline-select{
            background: #1E9FFF !important;
        }
    </style>
</head>
<!-- 结 构 代 码 -->
<body class="layui-layout-body upgrade_admin">
    <!-- 布 局 框 架 -->
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <ul class="layui-nav">
                <div class="le-header-name">
                    <img src="/assets/admin/images/hlogo.png" alt="" style="margin-left: -15px;margin-top: 3px;margin-bottom: 4px;height: 45px;">
                    <span style="font-size: 18px">{{$setting['store']['values']['system_name']}}更新日志</span>
                </div>
            </ul>
        </div>
        <!-- 视 图 页 面 -->
        <div class="layui-body">
            <!-- 内 容 页 面 -->
            <div class="layui-tab-content">
                <div class="layui-content-card">
                    <ul class="layui-timeline">
                        {{volist name="resultArray" id="vo" empty="" key='key'}}
                        <li class="layui-timeline-item">
                            <i class="layui-icon layui-timeline-axis <?= $is_version == $vo['upgrade_vision'] ? 'layui-timeline-select' : '' ?>">&#xe63f;</i>
                            <!--更新日期设置-->
                            <div class="le-left">
                                <div class="le-version">
                                    <!--判断列表的版本与当前版本是否相等-->
                                    {{$vo.upgrade_vision}}
                                </div>
                                <div class="le-time">
                                    {{$vo.create_time}}
                                </div>
                            </div>
                            <!--更新日期设置-->
                            <!--更新主体内容-->
                            <div class="layui-timeline-content layui-text">
                                <!--最新版本更新才会有-->
                                <div class="flex align-center le-content-header">
                                    {{if $vo['upgrade_vision'] == $is_version }}
                                    <div class="le-host-version">
                                        <span class="layui-icon layui-icon-location">您当前的版本</span>
                                    </div>
                                    {{/if}}
                                </div>
                                <!--最新版本更新才会有-->
                                <div class="le-content-info">
                                        <div>
                                            <span>{{$vo.content}}</span>
                                        </div>
                                </div>
                            </div>
                            <!--更新主体内容-->
                        </li>
                        {{/volist}}
                    </ul>

                </div>
            </div>
        </div>
</div>
    <!-- 布 局 框 架 -->
</body>
</html>