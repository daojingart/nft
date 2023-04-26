<?php


namespace app\common\constants;


class Constant
{
    //全局默认每页数量
    const LIMIT = 20;
    const PAGE = 1;
    //全局正确状态
    const STATUS_SUCCESS = 1;
    //全局错误状态
    const STATUS_FAIL = 0;
    //全局未授权或者未登录
    const STATUS_NO_LOGIN = -1;
    //全局未授权个人信息
    const STATUS_NO_AUTH = -2;
    //全局未绑定手机号
    const STATUS_NO_MOBILE = -3;
    //全局店铺关闭
    const STATUS_SHOP_CLOSE = -4;
    //全局正确状态
    const STATUS_SUCCESS_MSG = '';
    //全局错误状态
    const STATUS_FAIL_MSG = 'ERROR';
    //登录缓存有效期
    const LOGIN_CACHE_TIME = 18000;
    //拒绝访问
    const ACCESS_DENIED = 'access denied';

    // 是否删除 0-否 1-是
    const IS_DELETE_NO = 0;
    const IS_DELETE_YES = 1;

    // 开关 0-关 1-开
    const SWITCH_CLOSE = 0;
    const SWITCH_OPEN = 1;

    //开关数组
    const SWITCH_LIST = [
        self::SWITCH_CLOSE,
        self::SWITCH_OPEN,
    ];
}
