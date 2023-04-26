<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 09:49
 */


namespace app\admin\controller\user;
use app\common\model\News;

use app\admin\controller\Controller;
use app\common\model\NewsCategory;

/**
 * 帮助中心
 * Class Help
 * @package app\admin\controller\user
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-06-14 09:49
 */
class Help extends Controller
{
    /**
     * 新闻列表
     * @return mixed|\think\response\Json
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 13:33
     */
    public function index()
    {
        if (request()->isAjax()){
            return json(News::getList($this->request->param()));
        }
        return $this->fetch();
    }

    /**
     *添加新闻
     * @return array|mixed
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 13:33
     */
    public function add()
    {
        if (request()->isAjax()){
            $param = $this->request->param();
            if (News::create($param['notice'])){
                return  $this->renderSuccess('创建成功',url('index'));
            }
            return $this->renderError('创建失败');
        }
        $category = NewsCategory::field('id,title')->select();
        $this->assign('category',$category);
        return $this->fetch();
    }

    /**
     * 编辑新闻
     * @return mixed
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-14 13:33
     */
    public function renew($id)
    {
        if (request()->isAjax()){
            $param = request()->param();
            if (News::update($param['notice'],['id'=>$param['id']])){
                return  $this->renderSuccess('更新成功',url('index'));
            }
            return  $this->renderError('更新失败');
        }
        $category = NewsCategory::field('id,title')->select();
        $this->assign('category',$category);
        $model = News::get($id);
        return $this->fetch('',['model'=>$model]);
    }

    /**分类列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-16 10:42
     */
    public function categoryIndex()
    {
        if (request()->isAjax()){
            return json(NewsCategory::getList($this->request->param()));
        }
        return $this->fetch('category_index');
    }

    /**
     * 创建分类
     * @return array|mixed
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-16 10:52
     */
    public function categoryAdd()
    {
        if (request()->isAjax()){
            $param = $this->request->param();
            if (NewsCategory::create($param['notice'])){
                return  $this->renderSuccess('创建成功',url('categoryIndex'));
            }
            return $this->renderError('创建失败');
        }
        return $this->fetch('category_add');
    }

    /**
     *
     * @param $id
     * @return array|mixed
     * @throws \think\exception\DbException
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-16 10:58
     */
    public function categoryRenew($id)
    {
        if (request()->isAjax()){
            $param = request()->param();
            if (NewsCategory::update($param['notice'],['id'=>$param['id']])){
                return  $this->renderSuccess('更新成功',url('categoryIndex'));
            }
            return  $this->renderError('更新失败');
        }
        $model = NewsCategory::get($id);
        return $this->fetch('',['data'=>$model]);
    }
}