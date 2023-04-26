<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2023/1/3   13:48
 * +----------------------------------------------------------------------
 * | className:
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\user;

use app\admin\controller\Controller;
use app\common\model\Member as MemberModel;

class Consume extends Controller
{
    /**
     * 消费排行榜导出
     * @return mixed|\think\response\Json
     * @Time: 2023/1/3   13:56
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     */
    public function index()
    {
        if (request()->isAjax()) {
            $list = MemberModel::getConsume(array_merge($this->request->param()));
            return json($list);
        }
        return $this->fetch();
    }

    /**
     * 修改消费金额
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface editAmountSpent
     * @Time: 2023/1/3   14:05
     */
    public function editAmountSpent()
    {
        $param = $this->request->param();
        if((new \app\common\model\Member())->where(['member_id'=>$param['member_id']])->update(['amount_spent'=>$param['amount_spent']])){
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError('修改失败');
    }


    /**
     * 清空消费排行榜数据
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface clean
     * @Time: 2023/1/3   14:19
     */
    public function clean()
    {
        if((new \app\common\model\Member())->where(['amount_spent'=>['>',0]])->update(['amount_spent'=>0])){
            return $this->renderSuccess('清空成功');
        }
        return $this->renderError('清空失败');
    }

}