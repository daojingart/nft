<?php
/**
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-08-20 10:12
 */


namespace app\admin\controller\collection;


use app\admin\controller\Controller;
use app\admin\model\collection\Category as CategoryModel;
use app\admin\model\collection\MemberGoods;
use app\admin\model\collection\Goods;
use app\admin\model\collection\Writer as WriterModel;
use app\common\model\MemberBox;

/**
 * 二级市场
 * Class Market
 * @package app\admin\controller\collection
 * @Email:sliyusheng@foxmail.com
 * @Company 河南八六互联信息技术有限公司
 * @DateTime 2022-08-20 10:13
 */
class Market extends Controller
{
    /**
     * 渲染列表
     * @return mixed
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 10:29
     */
    public function index()
    {
        //商品分类
        $category = CategoryModel::where(['type' => 1, 'is_del' => 0])->field('category_id,name')->select();
        //作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $this->assign(['category' => $category, 'writer' => $writer]);
        return $this->fetch();
    }

    /**
     * 获取会员交易列表
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 10:28
     */
    public function secureMemberDeal()
    {
        $param = request()->param();
        return (new MemberGoods())->secureMemberDeal($param);
    }

    /**
     * 更新上下架
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 11:30
     */
    public function operation()
    {
        $param = request()->param();
        $res = (new MemberGoods())->operation($param);
        if ($res) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失败');
    }

    /**
     * 渲染盲盒市场列表
     * @return mixed
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 10:29
     */
    public function box()
    {
        if ($this->request->isAjax()) {
            $list = (new MemberBox())->getMarketList($this->request->param());
            return json($list);
        }
        // 商品分类
        $category = CategoryModel::where(['type' => 1, 'is_del' => 0])->field('category_id,name')->select();
        // 作家列表
        $writer = WriterModel::where(['is_del' => 0])->field('id,name')->select();
        $this->assign(['category' => $category, 'writer' => $writer]);
        return $this->fetch();
    }

    /**
     * 更新上下架
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 11:30
     */
    public function boxOperation()
    {
        $param = request()->param();
        if ((new MemberBox())->operation($param)) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError((new MemberBox())->getError() ?: '更新失败');
    }

    /**
     * 批量上下架
     * @return array
     * @Time: 2022/12/5   21:17
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface batchAllOperation
     */
    public function batchAllOperation()
    {
        $param = request()->post()['id'];
        if ((new MemberGoods())->batchAll($param)) {
            return $this->renderSuccess('下架成功');
        }
        return $this->renderError((new MemberBox())->getError() ?: '下架失败');
    }

	/**
	 * 锁定 交易中
	 * @return array
	 * @Time: 2022/12/5   21:17
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface batchAllOperation
	 */
	public function batchAlllock()
	{
		$param = request()->post()['id'];
		$model = new MemberGoods();
		if ($model->lockGoods($param,['goods_status'=>2])) {
			return $this->renderSuccess('锁定交易成功');
		}
		return $this->renderError((new MemberGoods())->getError() ?: '锁定交易失败');
	}

	/**
	 * 解锁挂售中
	 * @return array
	 * @Time: 2022/12/5   21:17
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface batchAllOperation
	 */
	public function batchAllunlock()
	{
		$param = request()->post()['id'];
		$model = new MemberGoods();
		if ($model->lockGoods($param,['goods_status'=>1])) {
			return $this->renderSuccess('解锁交易成功');
		}
		return $this->renderError($model->getError() ?: '解锁交易失败');
	}

}