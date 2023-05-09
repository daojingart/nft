<?php

namespace app\api\controller\goods;

use app\common\controller\Controller;
use app\common\model\Category;
use app\common\model\MemberBox;
use app\common\model\MemberGoods;
use app\common\model\Search;
use app\common\model\Writer;
use app\common\model\Blindbox;



/**
 * 二级市场
 */
class Marketgoods extends Controller
{
    protected $noNeedLogin = [
        'getCategoryList','getGoodsList','getMarketGoodsList','getMarketBoxList','search'
    ];

    /**
     * 获取藏品的分类
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.marketGoods/getCategoryList)
     * @ApiReturnParams   (name="category_id", type="string", description="分类的ID")
     * @ApiReturnParams   (name="name", type="string", description="分类的名称")
     */
    public function getCategoryList()
    {
        $this->success('ok',(new Category())->where(['is_del'=>0])->field("category_id,name")->select());
    }

    /**
     * 藏品/盲盒/回收藏品列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.marketgoods/getGoodsList)
     * @ApiParams   (name="type", type="string", required=true, description="类型 1==藏品 2==盲盒藏品 3==回收藏品")
     * @ApiParams   (name="category_id", type="string", required=true, description="分类的ID")
     * @ApiParams   (name="goods_name", type="string", required=true, description="藏品的标题")
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiParams   (name="sort_type", type="string", required=true, description="1=时间排序 2=最热排序 3=价格正序  4=价格倒叙")
     * @ApiParams   (name="is_concern", type="string", required=true, description="1=获取关注的列表  0 不获取关注的列表")
     * @ApiReturnParams   (name="is_concern", type="string", description="true=收藏  false=未收藏")
     */
    public function getGoodsList()
    {
        $param = array_merge($this->request->param(),['listRows' => 10]);
        if(!isset($param['type']) || !$param['type'] || $param['type']>3 || $param['type']<1){
            $this->error('参数错误');
        }
        $member_id = 0;
        if(isset($param['is_concern']) && $param['is_concern']==0 && !$this->auth->isLogin()){
            $this->error('请先登录');
        }
        if($this->auth->isLogin()){
            $member_id = $this->auth->getMemberId();
        }
        $list = [];
        // 获取用户交易藏品
        switch ($param['type'])
        {
            case "1":
                $where = ['goods_status' =>['<>',4],'cast_status' => 2,'is_synthesis' => 0,'is_donation' => 0];
                $model = new MemberGoods();
                $list = $model->getMarketGoodsList($param,$where,$member_id);
                break;
            case "2":
                $where = ['box_status' =>['in',['20','30']]];
                $model = new MemberBox();
                $list = $model->getMarketGoodsList($param,$where,$member_id);
                break;
            case "3":
                $model = new \app\api\model\collection\Goods();
                $list = $model->getlist($param,$member_id);
                break;
        }
        $this->success('ok',$list);
    }

    /**
     * 二级市场藏品列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.marketgoods/getMarketGoodsList)
     * @ApiParams   (name="goods_id", type="string", required=true, description="藏品的ID")
     * @ApiParams   (name="sort_type", type="string", required=true, description="排序 1=时间排序 2=时间倒叙 3=价格正序  4=价格倒叙")
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="goods_status", type="string", description="藏品状态  0持有中  1挂售中  2交易中 3已出售  4已回收")
     * @ApiReturnParams   (name="cast_status", type="string", description="铸造状态  1铸造中  2铸造成功  3铸造失败")
     */
    public function getMarketGoodsList()
    {
        $param = array_merge($this->request->param(),['listRows' => 10]);
        if(!$param['goods_id']){
           $this->error('参数错误');
        }
        $where = ['goods_status' =>['in',['1','2']],'cast_status' => 2,'is_synthesis' => 0,'is_donation' => 0,'sale_status' => 10];
        $this->success('ok',(new MemberGoods())->getMarketDetailsGoodsList($param,$where));
    }

    /**
     * 二级市场盲盒列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.marketgoods/getMarketBoxList)
     * @ApiParams   (name="goods_id", type="string", required=true, description="藏品的ID")
     * @ApiParams   (name="sort_type", type="string", required=true, description="排序 1=时间排序 2=时间倒叙 3=价格正序  4=价格倒叙")
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function getMarketBoxList()
    {
        $param = array_merge($this->request->param(),['listRows' => 10]);
        if(!$param['goods_id']){
            $this->error('参数错误');
        }
        $where = ['box_status' =>['in',['20','30']]];
        $this->success('ok',(new MemberBox())->getMarketDetailsGoodsList($param,$where));
    }

    /**
     * 获取推荐热词
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.marketgoods/search)
     * @ApiReturnParams   (name="title", type="string", description="搜索的热词名称")
     */
    public function search()
    {
        $this->success('ok',Search::list(['type'=>1]));
    }


    /**
     * 藏品详情
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.marketgoods/getDetail)
     * @ApiParams   (name="goods_id", type="string", required=true, description="藏品的ID")
     * @ApiParams   (name="type", type="string", required=true, description="1 藏品  2盲盒  3我的回收")
     * @ApiReturnParams   (name="title", type="string", description="搜索的热词名称")
     */
    public function getDetail()
    {
        $member_id = $this->auth->member_id; //会员ID
        $goods_id = $this->request->param('goods_id'); //藏品表的ID
        $type = $this->request->param('type');
        if(!$goods_id || !$type){
            $this->error('参数错误');
        }
        $MemberGoodsModel = new MemberGoods();
        // 会员挂售藏品
        $goods_status = 0;
        $is_show =10;
        $goods = [];
        $collection_number = '';
        $is_goods_type = '1';
        switch ($type)
        {
            case "1":
                $detail = $MemberGoodsModel->where(['id' => $goods_id,'cast_status' => 2,'is_synthesis' => 0,'is_donation' => 0])->find();
                $goods_price = $detail['sale_price'];
                $collection_number = $detail['collection_number'];
                $goods_status = $detail['goods_status'];
                $total_num = $detail['total_num'];
                $writer_name = $detail['writer_name'];
                $create_time = date('Y-m-d H:i:s',$detail['list_time']);
                // 获取视频链接
                $goods_info = (new \app\common\model\Goods())->where(['goods_id' => $detail['goods_id']])->find();
                $is_suffix = $goods_info['is_suffix'];
                $is_goods_id = $goods_info['goods_id'];
                $d_images = $goods_info['d_images'];
                $is_pedestal = $goods_info['is_pedestal'];
                $original_number = $goods_info['original_number'];
                $audio_link_url = $goods_info['audio_link_url'];
                $video_link_url = $goods_info['video_link_url'];
                $goods_type = $goods_info['goods_type'];
                $have_num = $total_num;
                $content = htmlspecialchars_decode($goods_info['content'],true);
                $is_goods_type = '1';
                // 判断是否拥有此藏品
                $is_buy = 0;
                if($detail['member_id'] == $member_id){
                    $is_buy = 1;
                }
                break;
            case "2":
                $detail = (new MemberBox())->where(['id' => $goods_id,'box_status' => 20])->find();
                $detail_goods = (new \app\common\model\Goods())->where(['goods_id' => $detail['goods_id']])->find();
                $is_goods_type = 2;
                $content = htmlspecialchars_decode($detail_goods['content'],true);
                $original_number = $detail_goods['original_number'];
                $is_goods_id =  $detail['goods_id'];
                $is_suffix = $detail_goods['is_suffix'];
                $is_pedestal = $detail_goods['is_pedestal'];
                $goods_price = $detail['sale_price'];
                $goods_status = $detail['box_status'];
                $d_images = $detail_goods['d_images'];
                $writer_name = "";
                $audio_link_url = "";
                $video_link_url = "";
                $goods_type = 3;
                $have_num = "";
                $total_num = 1;
                $create_time = date('Y-m-d H:i:s',$detail['sale_time']);
                // 判断是否拥有此藏品
                $is_buy = 0;
                if($detail['member_id'] == $member_id){
                    $is_buy = 1;
                }
                break;
            case "3":
                $detail = (new \app\common\model\Goods())->where(['goods_id' => $goods_id,'recovery_status' => 1,'recovery_num' => ['>',0]])->find();
                $writer_info = (new Writer())->where(['id' => $detail['writer_id']])->find();
                $original_number = $detail['original_number'];
                $is_goods_id = $detail['goods_id'];
                $content = htmlspecialchars_decode($detail['content'],true);
                $is_goods_type = 1;
                $writer_name = $writer_info['name'];
                $goods_price = $detail['recovery_price'];
                $total_num = $detail['recovery_num'];
                $create_time = $detail['create_time'];
                $d_images = $detail['d_images'];
                $audio_link_url = $detail['audio_link_url'];
                $video_link_url = $detail['video_link_url'];
                $goods_type = $detail['goods_type'];
                $is_suffix = $detail['is_suffix'];
                $is_pedestal = $detail['is_pedestal'];
                // 获取此藏品持有数量
                $goods_num = $MemberGoodsModel->where([
                    'goods_id' => $goods_id,
                    'goods_status' => 0,
                    'cast_status' => 2,
                    'is_synthesis' => 0,
                    'is_donation' => 0,
                    'member_id' => $member_id
                ])->count();
                $have_num = $goods_num;
                $is_buy = $goods_num>0?1:0;
                break;
        }
        if(empty($detail)){
            $this->error('数据异常,请稍后重新或者反馈平台！');
        }
        $circulate_number = (new MemberGoods())->circulate($is_goods_id);
        // 重新组装数据
        $info = [
            'id' => $detail['id'] ?? 0,
            'goods_id' => $detail['goods_id'],
            'goods_status' =>$goods_status,
            'goods_name' => $detail['goods_name'],
            'goods_price' => $goods_price,
            'goods_thumb' => $detail['goods_thumb'],
            'content' => $content,
            'goods_no' => isset($detail['goods_no'])?$detail['goods_no']:'',
            'total_num' => $total_num,
            'writer_name' => $writer_name,
            'collection_number' => $collection_number,
            'hash_url' => isset($detail['hash_url'])?$detail['hash_url']:'',
            'create_time' => $create_time,
            'd_images' => $d_images,
            'audio_link_url' => $audio_link_url,
            'video_link_url' => $video_link_url,
            'goods_type' => $goods_type,
            'have_num' => $have_num,
            'is_buy' => $is_buy,
            'is_suffix' => $is_suffix,
            'is_pedestal' => $is_pedestal,
            'original_number' => $original_number, //发行量
            'circulation_number' => goodsCirculation($is_goods_id,$is_goods_type),
            'is_box_show' => $is_show,
            'circulate_number' => $circulate_number
        ];
        if(isset($detail['id'])){
            $info['member_id'] = $detail['member_id'];
        }
        $this->success('ok',$info);
    }


    /**
     * 获取我的藏品列表
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/goods.marketgoods/getMygoodsList)
     * @ApiParams   (name="page", type="string", required=true, description="分页")
     * @ApiParams   (name="sort", type="string", required=true, description="1=价格正序 2=价格降序 默认降序")
     * @ApiParams   (name="is_consignment", type="string", required=true, description="1=查看全部  2=查看在售 默认查看寄售")
     * @ApiParams   (name="goods_id", type="string", required=true, description="藏品ID")
     * @ApiReturnParams   (name="goods_status", type="string", description="0持有中  1挂售中  2交易中 3已出售  4已回收")
     * @ApiReturnParams   (name="id", type="string", description="藏品ID")
     * @ApiReturnParams   (name="collection_number", type="string", description="藏品编号")
     * @ApiReturnParams   (name="sale_price", type="string", description="寄售价格  如果goods_status==0 价格需要前端判断显示--")
     */
    public function getMygoodsList()
    {
        $page = $this->request->post('page',1);
        $sort = $this->request->post('sort',1);
        $goods_id = $this->request->post('goods_id',1);
        $is_consignment = $this->request->post('is_consignment',1);
        if(!$page || !$sort || !$is_consignment){
            $this->error('参数错误');
        }
        $sort_where  = [];
        switch ($sort)
        {
            case "1": //价格
                $sort_where['sale_price'] = 'asc';
                break;
            case "2": //价格
                $sort_where['sale_price'] = 'desc';
                break;
            case "3":
                $sort_where['collection_number'] = 'asc';
                break;
            case "4":
                $sort_where['collection_number'] = 'desc';
                break;
        }
        $where = [];
        if($is_consignment ==1){
            $where = ['goods_status' =>['in',['0','1','2']],'cast_status' => 2,'is_synthesis' => 0,'is_donation' => 0];
        }else{
            $where = ['goods_status' =>['in',['1','2']],'cast_status' => 2,'is_synthesis' => 0,'is_donation' => 0,'sale_status' => 10];
        }
        $where['goods_id'] = $goods_id;
        $model = new MemberGoods();
        $list = $model->where($where)->order($sort_where)->page($page,10)->field("goods_status,id,collection_number,sale_price")->select();
        $this->success("获取数据成功",$list);
    }

}