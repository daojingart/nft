<?php

namespace app\admin\controller\application;

use app\admin\controller\Controller;
use app\admin\model\Setting as SettingModel;
use app\common\model\Goods;
use app\common\model\Prize as prizeModel;
use app\common\model\PrizeLog as PrizeLogModel;

class Prize extends Controller
{
    public function index()
    {
        if ($this->request->isAjax()) {
            $param        = request()->param();
            $selectResult = (new prizeModel())->getList($param);
            return json($selectResult);
        }
        return $this->fetch();
    }

    public function add()
    {
        $param = request()->param();
        if (request()->isAjax()) {
            if (prizeModel::create($param, true)) {
                return $this->renderSuccess('创建成功');
            }
            return $this->renderError('创建失败');
        }

        return $this->fetch();
    }

    public function edit($id)
    {
        $param = request()->param();
        if (request()->isAjax()) {
            if (prizeModel::update($param, ['id' => $param['id']], true)) {
                return $this->renderSuccess('更新成功');
            }
            return $this->renderError('更新失败');
        }
        $this->assign('data', prizeModel::get($id));
        return $this->fetch();
    }

    public function prize($key = 'prize')
    {
        if (!$this->request->isAjax()) {
            $goods = Goods::field('goods_name,goods_id')->where('is_del',0)->select()->toArray();
            $values = SettingModel::getItem($key);
            $goods_check_data = Goods::field('goods_name,goods_id')->where(['goods_id'=>['in',$values['goods_id']]])->select()->toArray();
            return $this->fetch($key, compact('values','goods','goods_check_data'));
        }
        $model = new SettingModel;
        if ($model->edit($key, $this->postData($key))) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失败');
    }

    /**
     * 中奖记录
     * @ApiAuthor 2023/4/6-liyusheng
     */
    public function log()
    {
        if ($this->request->isAjax()) {
            $param        = request()->param();
            $selectResult = (new PrizeLogModel())->getList($param);
            return json($selectResult);
        }
        return $this->fetch();
    }

    public function export()
    {
        if ($this->request->isAjax()) {
            $param        = request()->param();
            $selectResult = (new PrizeLogModel())->getList($param);
            return json($selectResult);
        }
    }
}