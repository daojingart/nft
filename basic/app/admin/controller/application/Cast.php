<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: sliyusheng@foxmail.com
 * +----------------------------------------------------------------------
 * | Time:  2022-06-15 15:24
 * +----------------------------------------------------------------------
 * | className:  藏品铸造(合成)
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\application;

use app\common\model\SyntheticLog;
use app\admin\controller\Controller;


class Cast extends Controller
{
    /**
     * 铸造列表
     * @return mixed|\think\response\Json
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 15:29
     */
    public function index()
    {
        if (request()->isAjax()){
            return SyntheticLog::getList();
        }
        return $this->fetch();
    }
}