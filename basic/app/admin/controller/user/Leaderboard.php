<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/9/3   23:45
 * +----------------------------------------------------------------------
 * | className: 拉新排行榜
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\user;

use app\admin\controller\Controller;
use app\common\model\Member as MemberModel;

class Leaderboard extends Controller
{
    /**
     * 拉新排行榜
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2022/9/3   23:46
     */
    public function index()
    {
        if (request()->isAjax()) {
            $list = MemberModel::getLeaderboard(array_merge($this->request->param()));
            return json($list);
        }
        return $this->fetch();
    }

}