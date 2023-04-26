<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/13   17:54
 * +----------------------------------------------------------------------
 * | className:  会员藏品库管理
 * +----------------------------------------------------------------------
 */
namespace app\admin\controller\collection;


use app\admin\controller\Controller;
use app\admin\model\collection\MemberGoods as MemberGoodsModel;


class Order extends Controller
{
    /**
     * 会员藏品列表
     * @return mixed
     * @Time: 2022/6/13   18:06
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface index
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 全部
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   20:38
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface all_list
     */
    public function all_list()
    {
        return $this->getList([
            'is_donation' => 0
        ]);
    }

    /**
     * 铸造中
     * @Interface cast_list
     */
    public function cast_list()
    {
        return $this->getList(['cast_status' => 1]);
    }

    /**
     * 持有中
     * @Interface hold_list
     */
    public function hold_list()
    {
        return $this->getList(['goods_status' => 0,'is_donation' => 0,'cast_status'=>2,'is_synthesis'=>0]);
    }

    /**
     * 挂售中
     * @Interface listing_list
     */
    public function listing_list()
    {
        return $this->getList(['goods_status' => 1,'is_synthesis'=>0]);
    }

    /**
     * 交易中
     * @Interface trade_list
     */
    public function trade_list()
    {
        return $this->getList(['goods_status' => 2,'is_synthesis'=>0,'is_donation'=>0]);
    }

    /**
     * 已出售
     * @Interface sell_list
     */
    public function sell_list()
    {
        return $this->getList(['goods_status' => 3]);
    }

    /**
     * 已转增
     * @Interface donation_list
     */
    public function donation_list()
    {
        return $this->getList(['is_donation' => 1]);
    }

    /**
     * 已合成
     * @Interface synthesis_list
     */
    public function synthesis_list()
    {
        return $this->getList(['is_synthesis' => 1,'is_donation'=>0]);
    }

    /**
     * 铸造失败
     * @Interface castfail_list
     */
    public function castfail_list()
    {
        return $this->getList(['cast_status' => 3]);
    }


    /**
     * 获取列表
     * @param array $filter
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/13   19:02
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    private function getList($filter = [])
    {
        $model = new MemberGoodsModel();
        return  $model->getList($filter,$this->request->param());
    }

    /**
     * 销毁藏品 删除藏品
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface destroyCollection
     * @Time: 2022/7/11   21:57
     */
    public function destroyCollection($id)
    {
       //销毁这个藏品;删除这个藏品;然后执行销毁这个藏品
        $memberGoods = MemberGoodsModel::detail($id);
        if($memberGoods->remove()){
            return $this->renderSuccess('删除成功');
        }
        return $this->renderError('删除失败');
    }

    /**
     * 修改作品编号
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface editModificationNumber
     * @Time: 2022/7/14   12:56
     */
    public function editModificationNumber()
    {
        $param = $this->request->param();
        $member_goods_info = (new MemberGoodsModel())->where(['collection_number'=>$param['course_name']])->find();
        if(!empty($member_goods_info)){
            return $this->renderError('编号已存在,修改失败！');
        }
        if((new MemberGoodsModel())->where(['id'=>$param['id']])->update(['collection_number'=>$param['course_name']])){
            return $this->renderSuccess('编号修改成功！');
        }
        return $this->renderError('编号修改失败！未知错误');
    }





}