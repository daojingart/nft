<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/13   20:21
 * +----------------------------------------------------------------------
 * | className: 会员藏品管理
 * +----------------------------------------------------------------------
 */

namespace app\common\model;


use app\admin\model\Setting;
use app\api\model\collection\Goods;
use app\notice\model\Order as OrderModel;
use app\common\controller\Task;
use app\common\model\MemberGoodsCollection;
use exception\BaseException;
use think\Exception;
use think\Request;
use think\Db;


class MemberGoods extends BaseModel
{
    protected $name = 'member_goods';

    /**
     * 详情
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($id)
    {
        return self::get($id);
    }

    /**
     * 获取我的藏品列表  1.0.9 废弃
     * @param $param
     * @param $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/6/17   17:04
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGoodsList
     */
    public function getGoodsList($param,$where)
    {
        if (isset($param['goods_name']) && !empty($param['goods_name'])) {
            $where['goods_name'] = ['like','%'.$param['goods_name'].'%'];
        }
        if(isset($param['category_id']) && $param['category_id']>0){
            $goods_ids = (new Goods())->where(['category_id'=>$param['category_id']])->column("goods_id");
            $where['goods_id'] = ['in',$goods_ids];
        }
        $query = self::where($where);
        if (isset($param['sort_type']) && !empty($param['sort_type'])) {
            switch ($param['sort_type']) {
                case 1:
                    $query->order('create_time asc');
                    break;
                case 2:
                    $query->order('create_time desc');
                    break;
                case 3:
                    $query->order('goods_price asc');
                    break;
                case 4:
                    $query->order('goods_price desc');
                    break;
            }
        }
        $query->field('id,goods_id,goods_name,goods_thumb,writer_name,goods_status,cast_status,sale_price as goods_price,total_num as stock_num,order_no,sale_status,source_type,create_time,collection_number');
        $list = $query->paginate($param['listRows'],false,[
            'query' => Request::instance()->request()
        ]);
        return $list;
    }

    /**
     * 获取已经挂售的订单列表
     * @param $param
     * @param $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/9/14   17:12
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getSaleGoodsList
     */
    public function getSaleGoodsList($param,$where)
    {
        $query = self::where($where);
        $query->field('id,goods_id,goods_name,goods_thumb,writer_name,sale_price as goods_price,order_no,sale_status,collection_number');
		$list = $query->paginate($param['listRows'],false,[
            'query' => Request::instance()->request()
        ])->each(function ($item) use ($where){
            if($where['goods_status']==3){
                $order_info = (new Order())->where(['sale_goods_id'=>$item['id'],'pay_status'=>2])->field("order_no,member_id")->find();
                if(!empty($order_info)){
                    $member_info = Member::detail($order_info['member_id']);
                    $item['pay_name'] = $member_info['name'];
                }else{
                    $item['pay_name'] = "--";
                }
            }else{
                $item['pay_name'] = '--';
            }
        });
        return $list;
    }

    /**
     * 市场藏品列表
     * @param $param
     * @param $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/6/17   17:04
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGoodsList
     */
    public function getMarketGoodsList($param,$where1,$member_id)
    {
		$where = [];
        if (isset($param['goods_name']) && !empty($param['goods_name'])) {
            $where['goods_name'] = ['like','%'.$param['goods_name'].'%'];
        }
		if(isset($param['category_id']) && $param['category_id']>0){
			$where['category_id'] = $param['category_id'];
		}
		if(isset($param['is_concern']) && $param['is_concern']==1){
			$goods_ids = (new MemberGoodsCollection())->where(['member_id'=>$member_id])->column("goods_id");
			$where1['goods_id'] = ['in',$goods_ids];
		}
		//查询藏品表的数据
		$goods_data_id = (new Goods())->where($where)->column('goods_id');
		//查询关闭寄售的
		$open_goods_data_id = (new Goods())->where(['is_open_consignment'=>30])->column('goods_id');
		//获取开启寄售藏品的数据
		$goods_data_id = array_diff($goods_data_id,$open_goods_data_id);
		//查询藏品表的数据
		$query = self::where($where1);
		$member_goods_list = $query->field("goods_name,writer_name,goods_id,goods_price,goods_thumb,id")->group("goods_id")->select()->toArray();
		$member_goods_goods_id = array_column($member_goods_list, 'goods_id','id');
		$in_goods_id = array_intersect($member_goods_goods_id,$goods_data_id);
		$in_goods_id = array_flip($in_goods_id);
		//结束查询藏品表的数据
		//查询在售数量
		$count = $this->where(['goods_status'=>['in',['1','2']],'cast_status' => 2,'is_synthesis' => 0,'is_donation' => 0,'sale_status' => 10])
			->field('goods_id,count(goods_id) as consignment_number,min(sale_price) as goods_price')
			->group("goods_id")->select()->toArray();
		$sale_price = array_column($count,'goods_price','goods_id');
		$count = array_column($count,'consignment_number','goods_id');
		//查询在售数量结束


		//查询流通量
		$circulate = $this->where(['goods_status'=>['in',['1','2','0']],'is_synthesis'=>0,'is_donation'=>0])
			->field('goods_id,count(goods_id) as circulate')
			->group("goods_id")->select()->toArray();
		$circulate = array_column($circulate,'circulate','goods_id');
		//查询流通量结束

		$order = [];
		if (isset($param['sort_type']) && !empty($param['sort_type'])) {
			switch ($param['sort_type']) {
				case 1:
					$order['create_time'] = "desc";
					break;
				case 2:
					$order['create_time'] = "asc";
					break;
				case 3:
					$order['sale_price'] = "asc";
					break;
				case 4:
					$order['sale_price'] = "desc";
					break;
			}
		}
		//获取当前商品的最高寄售金额
		$goods_list = $this->field('goods_id,goods_name,goods_thumb,writer_name,member_id')
					->whereIn("id",$in_goods_id)
					->order($order)
					->paginate($param['listRows'],false,[
						'query' => Request::instance()->request()
					])->each(function ($item) use ($member_id,$count,$circulate,$sale_price) {
						$collection = false;
						if($member_id){
							$collection = (new MemberGoodsCollection())->getIsCollection($member_id, $item['goods_id']);
						}
						if($sale_price[$item['goods_id']]){
							$item['goods_price'] = $sale_price[$item['goods_id']];
						}else{
							$item['goods_price'] = \app\common\model\Goods::where(['goods_id'=>$item['goods_id']])->value('top_price_limit');
						}
						$item['is_collection'] =$collection;
						$item['consignment_number'] = $count[$item['goods_id']] ?? 0;
						$item['circulate'] = $circulate[$item['goods_id']] ?? 0;
						return $item;
					});
        return $goods_list;
    }

    /**
     * 市场某个藏品的列表
     * @param $param
     * @param $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/6/17   17:04
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGoodsList
     */
    public function getMarketDetailsGoodsList($param,$where)
    {
        $where['goods_id'] = $param['goods_id'];
        $query = self::where($where);
        if (isset($param['sort_type']) && !empty($param['sort_type'])) {
            switch ($param['sort_type']) {
                case 1:
                    $query->order('create_time asc');
                    break;
                case 2:
                    $query->order('create_time desc');
                    break;
                case 3:
                    $query->order('goods_price asc');
                    break;
                case 4:
                    $query->order('goods_price desc');
                    break;
            }
        }
        $query->field('id,goods_id,goods_name,goods_thumb,writer_name,goods_status,cast_status,sale_price as goods_price,total_num as stock_num,order_no,sale_status,source_type,create_time,collection_number');
        $list = $query->paginate($param['listRows'],false,[
            'query' => Request::instance()->request()
        ]);
        return $list;
    }


    /**
     * 获取外面合集的藏品列表  8.12新增
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface groupGoodsList
     * @Time: 2022/8/12   23:20
     */
    public function groupGoodsList($params)
    {
        $where = [];
        $where['member_id'] = $params['member_id'];
        if (isset($params['goods_name']) && !empty($params['goods_name'])) {
            $where['goods_name'] = ['like','%'.$params['goods_name'].'%'];
        }
        $where['goods_status'] = ['in',['0','1','2']];
        $where['is_synthesis'] = 0;
        $where['is_donation'] = 0;
        $goods_list = $this->where($where)->field("goods_id,goods_name,goods_thumb")->group("goods_id")->page($params['page'],10)->select()->toArray();
        foreach ($goods_list as $key=>$val){
            $count_number = $this->where(['member_id'=>$params['member_id'],'goods_id'=>$val['goods_id'],'is_donation'=>0,'is_synthesis'=>0,'goods_status'=>['in',['0','1']]])->count();
            $goods_list[$key]['number'] = $count_number;
        }
        return $goods_list;
    }

    /**
     * 获取我的藏品列表 8.12新增
     * @param $params
     * @Time: 2022/8/12   23:57
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface getMyGoodsList
     */
    public function getMyGoodsList($params)
    {
        $where = [];
        $where['member_id'] = $params['member_id'];
        $where['goods_id'] = $params['goods_id'];
        $goods_info = (new Goods())->where(['goods_id'=>$params['goods_id']])->field("product_types")->find();
        //判断是藏品  还是 盲盒
        if(in_array($goods_info['product_types'], ['1','2','4','5'])){
            $where['goods_status'] = ['in',['0','1','2']];
            $where['is_synthesis'] = 0;
            $where['is_donation'] = 0;
            return  $this->where($where)->order("collection_number asc")->field("id,goods_name,goods_thumb,create_time,cast_status,sale_status,goods_status,collection_number,goods_price,sale_price")->page($params['page'],10)->select()->toArray();
        }
    }


    /**
     * 获取市场交易列表
     * @param $param
     * @param $where
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @Time: 2022/6/17   17:04
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface getGoodsList
     */
    public function getForeignGoodsList($where)
    {
        $query_list = $this->where($where)->group("goods_id")->field('goods_id')->select()->toArray();
        $new_data = [];
        foreach ($query_list as $key=>$val){
            $details = $this->where($where)->where(['goods_id'=>$val['goods_id']])->order("sale_price asc")->find();
            $new_data[$key]['img'] = $details['goods_thumb'];
            $new_data[$key]['price'] = $details['sale_price'];
            $new_data[$key]['name'] = $details['goods_name'];
        }
        return $new_data;
    }

    /**
     * 提交确认转增
     * @param $member_id
     * @param $info
     * @param $goods_id
     * @return bool|string
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/20   10:13
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface toDonation
     */
    public function toDonation($member_info,$info,$goods_id)
    {
        // 判断此藏品是否属于当前会员
        $goods_info = $this->where(['member_id' => $member_info['member_id'],'id' => $goods_id])->find();
        if(!$goods_info){
            $this->error = '藏品不存在';
            return false;
        }
        if($goods_info['goods_status'] != 0){
            $this->error = '藏品已挂售;无法转增';
            return false;
        }
        if($goods_info['cast_status'] != 2){
            $this->error = '藏品未铸造;无法转增';
            return false;
        }
        if($goods_info['is_synthesis'] != 0){
            $this->error = '藏品已合成;无法转增';
            return false;
        }
        if($goods_info['is_donation'] != 0){
            $this->error = '藏品已转赠;无法转增';
            return false;
        }
        // 商品信息
        $goods = (new Goods())->where(['goods_id' => $goods_info['goods_id']])->find();
        // 获取转增时间配置
        $minute = Setting::getItem('collection')['time'];
        if($goods['is_open_increase']==20){ //单独设置转增时间
            $minute = $goods['increas_minute'];
        }
        if($goods['is_open_increase']==30){ //单独设置转增时间
            $this->error = '不符合转赠规则,无法转赠';
            return false;
        }
        // 判断当前是否可以转增
        $new_time = time();
        $end_time = $minute*60+strtotime($goods_info['create_time']);
        if($new_time < $end_time){
            $this->error = '转赠时间未到,不可转赠';
            return false;
        }
        Db::startTrans();
        try {
            // 修改此藏品持有人状态
            $this->where(['id' => $goods_info['id']])->update([
                'is_donation' => 1,
                'donation_time' => time(),
                'donation_member_id' => $info['member_id'],
                'donation_member_phone' => $info['phone'],
                'update_time' => time()
            ]);
            // 新增转增会员藏品记录
            $this->insert([
                'member_id' => $info['member_id'],
                'goods_id' => $goods_info['goods_id'],
                'phone' => $info['phone'],
                'nickname' => $info['name'],
                'goods_no' => $goods_info['goods_no'],
                'goods_name' => $goods_info['goods_name'],
                'goods_thumb' => $goods_info['goods_thumb'],
                'goods_price' => $goods_info['goods_price'],
                'total_num' => $goods_info['total_num'],
                'writer_id' => $goods_info['writer_id'],
                'writer_name' => $goods_info['writer_name'],
                //'hash_url' => $goods_info['hash_url'],
                'source_type' => $goods_info['source_type'],
                'create_time' => time(),
                'collection_number' => $goods_info['collection_number'],
                'app_id' => self::$app_id
            ]);
            $member_goods_insertId = $this->getLastInsID();

            (new GoodsLog())->insertData([
                'member_id' => $goods_info['member_id'],
                'nickname' => $goods_info['nickname'],
                'goods_id' => $goods_info['goods_id'],
                'sale_type' => '-1',
                'goods_name' => $goods_info['goods_name'],
                'goods_thumb' => $goods_info['goods_thumb'],
                'goods_price' => $goods_info['goods_price'],
                'issue_name' => $goods_info['issue_name']??'',
                'buy_phone' => $info['phone'],
                'buy_member' => $info['member_id'],
                'create_time' => time(),
                'member_goods_id' => $member_goods_insertId,
            ]);

            (new GoodsLog())->insertData([
                'member_id' => $info['member_id'],
                'nickname' => $info['name'],
                'goods_id' => $goods_info['goods_id'],
                'sale_type' => '9',
                'goods_name' => $goods_info['goods_name'],
                'goods_thumb' => $goods_info['goods_thumb'],
                'goods_price' => $goods_info['goods_price'],
                'issue_name' => $goods_info['issue_name']??'',
                'buy_phone' => $goods_info['phone'],
                'buy_member' => $goods_info['member_id'],
                'create_time' => time(),
                'member_goods_id' => $member_goods_insertId,
            ]);

            (new OrderModel())->increaseQuestionList($member_goods_insertId,$goods_info['asset_id'],$goods_info['shard_id'],$member_info['member_id'],$info['member_id'],"increase_question_list");
            Db::commit();
            return true;
        }catch (Exception $e){
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


	/**
	 * 提交确认转增
	 * @param $member_id
	 * @param $info
	 * @param $goods_id
	 * @return bool|string
	 * @throws BaseException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @Time: 2022/6/20   10:13
	 * @author: [Mr.Ai] [18612421593@163.com]
	 * @Interface toDonation
	 */
	public function toAllDonation($member_info,$info,$goods_id)
	{
		// 判断此藏品是否属于当前会员
		$goods_info = $this->where(['member_id' => $member_info['member_id'],'id' => $goods_id])->find();
		if(!$goods_info){
			$this->error = '藏品不存在';
			return false;
		}
		if($goods_info['goods_status'] != 0){
			$this->error = '藏品已挂售;无法转增';
			return false;
		}
		if($goods_info['cast_status'] != 2){
			$this->error = '藏品未铸造;无法转增';
			return false;
		}
		if($goods_info['is_synthesis'] != 0){
			$this->error = '藏品已合成;无法转增';
			return false;
		}
		if($goods_info['is_donation'] != 0){
			$this->error = '藏品已转赠;无法转增';
			return false;
		}
		Db::startTrans();
		try {
			// 修改此藏品持有人状态
			$this->where(['id' => $goods_info['id']])->update([
				'is_donation' => 1,
				'donation_time' => time(),
				'donation_member_id' => $info['member_id'],
				'donation_member_phone' => $info['phone'],
				'update_time' => time()
			]);
			// 新增转增会员藏品记录
			$this->insert([
				'member_id' => $info['member_id'],
				'goods_id' => $goods_info['goods_id'],
				'phone' => $info['phone'],
				'nickname' => $info['name'],
				'goods_no' => $goods_info['goods_no'],
				'goods_name' => $goods_info['goods_name'],
				'goods_thumb' => $goods_info['goods_thumb'],
				'goods_price' => $goods_info['goods_price'],
				'total_num' => $goods_info['total_num'],
				'writer_id' => $goods_info['writer_id'],
				'writer_name' => $goods_info['writer_name'],
				//'hash_url' => $goods_info['hash_url'],
				'source_type' => $goods_info['source_type'],
				'create_time' => time(),
				'collection_number' => $goods_info['collection_number'],
				'app_id' => self::$app_id
			]);
			$member_goods_insertId = $this->getLastInsID();
			(new GoodsLog())->insertData([
				'member_id' => $goods_info['member_id'],
				'nickname' => $goods_info['nickname'],
				'goods_id' => $goods_info['goods_id'],
				'sale_type' => '-1',
				'goods_name' => $goods_info['goods_name'],
				'goods_thumb' => $goods_info['goods_thumb'],
				'goods_price' => $goods_info['goods_price'],
				'issue_name' => $goods_info['issue_name']??'',
				'buy_phone' => $info['phone'],
				'buy_member' => $info['member_id'],
				'create_time' => time(),
				'member_goods_id' => $member_goods_insertId,
			]);

			(new GoodsLog())->insertData([
				'member_id' => $info['member_id'],
				'nickname' => $info['name'],
				'goods_id' => $goods_info['goods_id'],
				'sale_type' => '9',
				'goods_name' => $goods_info['goods_name'],
				'goods_thumb' => $goods_info['goods_thumb'],
				'goods_price' => $goods_info['goods_price'],
				'issue_name' => $goods_info['issue_name']??'',
				'buy_phone' => $goods_info['phone'],
				'buy_member' => $goods_info['member_id'],
				'create_time' => time(),
				'member_goods_id' => $member_goods_insertId,
			]);

			(new OrderModel())->increaseQuestionList($member_goods_insertId,$goods_info['asset_id'],$goods_info['shard_id'],$member_info['member_id'],$info['member_id'],"increase_question_list");
			Db::commit();
			return true;
		}catch (Exception $e){
			Db::rollback();
			$this->error = $e->getMessage();
			return false;
		}
	}

    /**
     * 提交确认挂售
     * @param $info
     * @param $price
     * @return bool|string
     * @Time: 2022/6/20   15:02
     * @author: [Mr.Ai] [1861242159$3@163.com]
     * @Interface toSale
     */
    public function toSale($info,$price)
    {
        Db::startTrans();
        try {
            // 修改藏品挂售状态
            self::where(['id' => $info['id']])->update([
                'goods_status' => 1,
                'list_time' => time(),
                'sale_price' => $price,
                'sale_status' => 10
            ]);
            Db::commit();
            return true;
        }catch (Exception $e){
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 提交确认回收
     * @param $info
     * @param $member_id
     * @return bool|string
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time: 2022/6/20   18:12
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface toRecovery
     */
    public function toRecovery($info,$member_id)
    {
        // 获取平台出售信息
        $goodsModel = new Goods();
        $goods_info = $goodsModel->where(['goods_id' => $info['goods_id'],'recovery_status' => 1,'recovery_num' => ['>',0]])->find();
        if(!$goods_info){
            throw new BaseException(['msg' => '藏品异常']);
        }
        if($goods_info['recovery_num'] < 1){
            throw new BaseException(['msg' => '回收已满，不可回收']);
        }
        if($goods_info['recovery_status']==0){
            throw new BaseException(['msg' => '异常请求,系统已经拦截自动处理中...']);
        }
        Db::startTrans();
        try {
            // 修改会员藏品状态
            self::where(['member_id' => $member_id,'id' => $info['id']])->update([
                'goods_status' => 4,
                'update_time' => time()
            ]);
            // 修改藏品回收数量
            $goodsModel->where(['goods_id' => $goods_info['goods_id']])->setDec('recovery_num',$info['total_num']);
            //用户回收给平台藏品 藏品用户进行交易则藏品回收后进行转增  另外打款给会员账户 先进行账户的打款
            $dealRecord = [
                'member_id' => $info['member_id'],
                'type' => 4, //下单
                'amount' => $goods_info['recovery_price'],
                'remark' => "平台回收藏品,奖励".$goods_info['recovery_price']
            ];
            (new Finance())->allowField(true)->save($dealRecord);
            //创建回购记录
            $model = (new \app\api\model\order\Order());
            $model->save([
                'member_id'      => $member_id,
                'order_type'     => 4,
                'app_id'         => 10001,
                'order_no'       => $this->orderNo(),
                'total_price'    => 0,
                'pay_price'      => 0,
                'order_status'   => 4,
                'pay_status'     => 2,
                'sale_member_id' => 0,
                'sale_goods_id'  => 0,
                'is_limit'       =>10,
            ]);
            $goodsList = [
                'order_id'    => $model->getLastInsID(),
                'member_id'   => $member_id,
                'app_id'      => 10001,
                'goods_id'    => $goods_info['goods_id'],
                'goods_name'  => $goods_info['goods_name'],
                'goods_image' => $goods_info['goods_thumb'],
                'goods_no'    => $goods_info['goods_no'],
                'pay_price'   => $goods_info['goods_price'],
                'total_price' => $goods_info['goods_price'],
                'product_num' => 1,
                'order_type'  => 4,
            ];
            //保存订单商品信息
            (new OrderGoods())->allowField(true)->save($goodsList);
            //执行藏品的收回
//            $res = (new Task())->addCollection($info['asset_id'], $info['shard_id'], $info['member_id'],0);
//            if($res['code'] != 1){
//                return $this->renderError('转增失败');
//            }
            Db::commit();
            return true;
        }catch (Exception $e){
            pre($e);
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 提交改价
     * @param $param
     * @return MemberGoods
     * @Time: 2022/6/20   22:31
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface toPrice
     */
    public function toPrice($param)
    {
        return self::where(['id' => $param['id']])->update(['sale_price' => $param['price'],'update_time' => time()]);
    }

    /**
     * 修改上/下架
     * @param $param
     * @return MemberGoods
     * @Time: 2022/6/20   22:35
     * @author: [Mr.Ai] [18612421593@163.com]
     * @Interface toStatus
     */
    public function toStatus($param)
    {
        return self::where(['id' => $param['id']])->update(['sale_status' => $param['status'],'update_time' => time(),'goods_status'=>0]);
    }

    /**
     * 添加藏品
     *
     * @param $params
     * @return false|int
     * @author Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/21 18:55
     */
    public function add($params)
    {
        return self::allowField(true)->save($params);
    }

    /**
     * 上链方法
     *
     * @return bool
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @copyright 河南八六互联信息技术有限公司
     * @Time 2022/6/22 15:56
     * @author Mr.Liu
     */
    public function asyncCochain()
    {
        $blockchain = Setting::getItem('blockchain');
        //获取所有铸造中的藏品
        $memberGoodsList = $this->where(['cast_status' => 1])->order("id desc")->select()->toArray();
        if (empty($memberGoodsList)) {
            return true;
        }
        $time = time();
        //百度链上链
        if ($blockchain['default'] == 'BD') {
            foreach ($memberGoodsList as $val) {
                $res = (new Task())->querysds($val['asset_id'], $val['shard_id']);
                if ($res['code'] == 1 && $res['data']['status'] == 0) {
                    (new MemberGoods())->where(['id' => $val['id']])->update([
                        'hash_url' => $res['data']['tx_id'],
                        'cast_status' => 2,
                        'cast_time' => $time
                    ]);
                }
            }
        } else if ($blockchain['default'] == 'WC') {
            foreach ($memberGoodsList as $val) {
                $res =  (new Task())->getWindUpDetails($val['operation_id']);
                if ($res['code'] == 1 && $res['data']['status'] == 1) {
                    (new MemberGoods())->where(['id' => $val['id']])->update([
                        'hash_url' => $res['data']['tx_id'],
                        'shard_id' => $res['data']['nft_id'],
                        'cast_status' => 2,
                        'cast_time' => $time
                    ]);
                }
            }
        }
        return true;
    }

    protected function orderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).sprintf('%03d',rand(0,999));
    }

    /**
     * 插入空投的藏品
     * @author: [Mr.Zhang] [1040657944@qq.com]
     * @Interface insertGoodsData
     * @Time: 2023/1/5   22:07
     */
    public function insertGoodsData($data)
    {
        $data['app_id'] = 10001;
        $data['create_time'] = time();
        $data['update_time'] = time();
        return $this->allowField(true)->save($data);
    }

    /**
     * 藏品流通量
     */
    public function circulate($goods_id)
    {
        return $this->where(['goods_id'=>$goods_id,'goods_status'=>['in',['0','1','2']],'is_synthesis'=>0,'is_donation'=>0])->count();
    }

}