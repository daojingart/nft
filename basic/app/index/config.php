<?php

return [
    // 应用调试模式 后台调式模式
    'app_debug'              => true,
    // 默认输出类型
    'default_return_type'    => 'html',

    'template'               => [
        // layout布局
        'layout_on'     =>  false,
        'layout_name'   =>  'layouts/layout',
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'php',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}}',
        // 标签库标签开始标记
        'taglib_begin' => '{{',
        // 标签库标签结束标记
        'taglib_end'   => '}}',
    ],

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件 重新处理类
//    'exception_tmpl'         => APP_PATH . 'common' . DS . 'view' . DS . 'tpl' . DS . 'think_exception.tpl',
    // 错误显示信息,非调试模式有效
    'error_message'          => '页面丢失了,快去联系开发小哥哥吧！',
    // 显示错误信息
    'show_error_msg'         => true,
    'Course_Cover_Picture'  => '750*460',  //课程
    'material_Cover_Picture'  => '181*247', //资料下载
    'BESIDE_LIVE_PICTURE' => '750*460', //直播封面图
    'BESIDE_WARM_LIVE_PICTURE' => '181*247', //横屏暖场图
    'BESIDE_VERTICAL_LIVE_PICTURE' => '181*247', //竖屏暖场图
];
