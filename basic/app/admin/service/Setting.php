<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/1/18   23:09
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\admin\service;

use app\common\server\base\BaseService;
use app\common\model\Setting as SettingModel;

class Setting extends BaseService
{
    /**
     * 获取基础配置
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSystem
     * @Time: 2022/1/18   23:10
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public static function getBaseSystem():array
    {
        return  SettingModel::getItem('store');
    }

    /**
     * 获取个人中心
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSystem
     * @Time: 2022/1/18   23:10
     * @todo [描述] // 待办。提示自己或他人还需要做些什么
     */
    public static function geMyPersonal():string
    {
        return  SettingModel::getItem('my_personal');
    }

}