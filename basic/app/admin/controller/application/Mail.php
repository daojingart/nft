<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-08-22 10:19
 */


namespace app\admin\controller\application;


use app\admin\controller\Controller;
use app\admin\model\user\MemberLabel;
use app\common\model\Notice;

/**
 * 站内信
 * Class Mail
 * @package app\admin\controller\application
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-08-22 10:19
 */
class Mail extends Controller
{
    /**
     * 发送站内信
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-22 10:19
     */
    public function index()
    {
        if (request()->isAjax()){
            $param = request()->param();
            $res = (new Notice())->send($param);
            if ($res){
                return  $this->renderSuccess('发送成功');
            }
            return  $this->renderError('发送失败');
        }
        $label = MemberLabel::select();
        $this->assign(['label'=>$label]);
        return $this->fetch();
    }
}