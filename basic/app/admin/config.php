<?php

return [
    // 应用调试模式 后台调式模式
    'app_debug'              => false,
    // 默认输出类型
    'default_return_type'    => 'html',

    'template'               => [
        // layout布局
        'layout_on'     =>  true,
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
// 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,

    // 错误显示信息,非调试模式有效
    // 显示错误信息
    'Course_Cover_Picture'  => '750*460',  //课程
    'material_Cover_Picture'  => '181*247', //资料下载
    'BESIDE_LIVE_PICTURE' => '750*460', //直播封面图
    'BESIDE_WARM_LIVE_PICTURE' => '181*247', //横屏暖场图
    'BESIDE_VERTICAL_LIVE_PICTURE' => '181*247', //竖屏暖场图
];
