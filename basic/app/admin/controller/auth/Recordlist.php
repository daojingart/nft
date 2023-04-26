<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/11/9   21:47
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 操作日志记录表
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\auth;

use app\admin\controller\Controller;
use app\common\model\LoginLog;

class Recordlist extends Controller
{
    /**
     * @Notes:操作日志记录表
     * @Interface index
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/11/9   21:47
     */
    public function index()
    {
        if($this->request->isAjax()){
            $param = input('param.');
            $selectResult  = (new LoginLog())->listData($param);
            return json($selectResult);
        }
        return $this->fetch();
    }

}