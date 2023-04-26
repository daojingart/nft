<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: sliyusheng@foxmail.com
 * +----------------------------------------------------------------------
 * | Time: 2022-06-15 14:03
 * +----------------------------------------------------------------------
 * | className:  合成条件
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\application;


use app\admin\controller\Controller;
use app\admin\model\collection\Writer as WriterModel;
use app\common\model\GoodsSynthetic;
use app\common\model\GoodsSyntheticCount;


class Conditions extends Controller
{
    /**
     * 商品合成条件列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 14:03
     */
    public function index()
    {
        if (request()->isAjax()){
            return GoodsSyntheticCount::getList();
        }
        $id = input('id');
        $this->assign(['id'=>$id]);
        return $this->fetch();
    }

    /**
     * 添加合成藏品条件商品
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-06-15 14:16
     */
    public function add($id)
    {
        if (request()->isAjax()){
            $param = request()->param();
            if (GoodsSynthetic::where('id',$param['conditions']['synthetic_id'])->find()->goods_id==$param['conditions']['goods_id']){
                return  $this->renderError('合成条件不能是当前藏品');
            }
            if (GoodsSyntheticCount::create($param['conditions'])){
                return  $this->renderSuccess('创建成功',url('index',['id'=>$id]));
            }
            return $this->renderError('创建失败');
        }
        $this->assign(['id'=>$id]);
        return $this->fetch();
    }

    /**
     * 合成碎片添加
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface goods
     * @Time: 2022/8/19   10:39
     */
    public function goods()
    {
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $types = $this->request->param('types');
        $this->assign(['writer' => $writer,'types'=>$types]);
        $this->view->engine->layout(false);
        return $this->fetch();
    }
}