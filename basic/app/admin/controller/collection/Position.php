<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang [1040657944@qq.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/8/30   14:46
 * +----------------------------------------------------------------------
 * | className: 持仓排行榜
 * +----------------------------------------------------------------------
 */

namespace app\admin\controller\collection;

use app\admin\controller\Controller;
use app\admin\model\collection\MemberGoods;
use app\admin\model\collection\Goods as GoodsModel;
use think\Cache;

class Position extends Controller
{
    /**
     * 持仓排行榜
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface index
     * @Time: 2022/8/30   14:46
     */
    public function index()
    {
        if($this->request->isAjax()){
            $selectResult  = (new MemberGoods())->getPositionRanking($this->request->param());
            return json($selectResult);
        }
        //查询藏品昵称 根据昵称或者ID筛选查看排行榜
        $goods_list  = (new GoodsModel())->where(['product_types'=>['<>','3'],'is_del'=>0])->field("goods_id,goods_name")->select();
        $one_goods_id = count($goods_list)>0?$goods_list[0]['goods_id']:"0";
        $goods_id = Cache::get("positionLeaderboardId");

        return $this->fetch('',compact('goods_list','one_goods_id','goods_id'));
    }

    /**
     * 设置持仓排行榜
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function setGoods()
    {
        $goods_id = $this->request->param("goods_id");
        Cache::set("positionLeaderboardId", $goods_id);
        return $this->renderSuccess("设置成功");
    }

}