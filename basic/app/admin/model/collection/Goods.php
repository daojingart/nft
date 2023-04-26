<?php
/**
 * +----------------------------------------------------------------------
 * | BL-Admin 后台管理框架
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.Ai [18612421593@163.com]
 * +----------------------------------------------------------------------
 * | Time:  2022/6/11   17:39
 * +----------------------------------------------------------------------
 * | className:  藏品管理
 * +----------------------------------------------------------------------
 */

namespace app\admin\model\collection;


use app\admin\model\Setting;
use app\common\components\helpers\StockUtils;
use app\common\controller\Task;
use app\common\model\ChainThList;
use app\common\model\Goods as GoodsModel;
use app\common\model\Calendar as CalendarModel;
use app\common\model\Member;
use app\common\model\Purchase;
use app\notice\model\Order as OrderNoticeModel;
use exception\BaseException;
use TencentCloud\Apigateway\V20180808\Models\ServiceReleaseVersion;
use think\Db;
use think\Exception;


class Goods extends GoodsModel
{

    /**
     * 获取藏品列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time      : 2022/6/13   10:10
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getList($param)
    {
        $limit                  = $param['limit'];
        $offset                 = ($param['page'] - 1) * $limit;
        $where                  = [];
        $where['is_del']        = 0;

        $where['product_types'] = isset($param['types']) ? $param['types'] : 2;
        if (!empty($param['goods_name'])) {
            $where['goods_name'] = ['like', '%' . $param['goods_name'] . '%'];
        }
        if (!empty($param['writer_id'])) {
            $where['writer_id'] = $param['writer_id'];
        }
        if (!empty($param['category_id'])) {
            $where['category_id'] = $param['category_id'];
        }
        if (!empty($param['product_types'])) {
            $where['product_types'] = $param['product_types'];
        }
        // 创建时间
        if (!empty($param['create_name'])) {
            $create_name          = explode('~', $param['create_name']);
            $where['create_time'] = ['between', [strtotime(trim($create_name[0])), strtotime(trim($create_name[1]))]];
        }
        // 开售时间
        if (!empty($param['start_time'])) {
            $start_time          = explode('~', $param['start_time']);
            $where['start_time'] = ['between', [strtotime(trim($start_time[0])), strtotime(trim($start_time[1]))]];
        }

        $list = $this->where($where)
            ->order(['goods_sort' => 'desc'])
            ->with(['category', 'writer'])
            ->limit($offset, $limit)
            ->select();
        $PurchaseModel = new Purchase();
        $new_list = [];
		$values = Setting::getItem('blockchain');
        foreach ($list as $k => $v) {
			$new_list[$k]['asset_status']        = empty($v['asset_id']) ? '<span class="layui-badge">未上链</span>' : '<span class="layui-badge layui-bg-blue">已上链</span>';
			if(isset($values['WC']['version']) && $values['WC']['version'] == 2){
				$text = '<span class="layui-badge">上链中</span>';
				if(empty($v['asset_id']) && $v['hash']==0){
					$text = '<span class="layui-badge">未上链</span>';
				}
				if($v['asset_id'] && $v['hash']){
					$text = '<span class="layui-badge layui-bg-blue">已上链</span>';
				}
				$new_list[$k]['asset_status']        = $text;
			}
            $new_list[$k]['goods_id']            = $v['goods_id'];
            $new_list[$k]['goods_name']          = $v['goods_name'];
            $new_list[$k]['goods_thumb']         = $v['goods_thumb'];
            $new_list[$k]['goods_price']         = $v['goods_price'];
            $new_list[$k]['product_types']       = $v['product_types'];
            $new_list[$k]['goods_category_name'] = $v['category']['name'];
            $new_list[$k]['sales_actual']        = $v['sales_actual'];
            $new_list[$k]['goods_sort']          = $v['goods_sort'];
            $new_list[$k]['goods_status']        = $v['goods_status']['text'];
            $new_list[$k]['create_time']         = $v['create_time'];
            $new_list[$k]['writer_name']         = $v['writer']['name'];
            $new_list[$k]['buy_num']             = $v['buy_num'];
            $new_list[$k]['is_sold']             = $v['stock_num'];
            $new_list[$k]['is_sold_number']      = $v['stock_num'];
            $new_list[$k]['stock_num']           = $v['stock_num'];
            $new_list[$k]['sales_initial']       = $v['sales_initial'];
            $new_list[$k]['original_number']     = $v['original_number'];
            $new_list[$k]['start_time']          = $v['start_time'] ? date('Y-m-d H:i:s', $v['start_time']) : '';

            if ($v['product_types'] == 4) {
                // 获取申购藏品预约时间
                $purchase_info                          = $PurchaseModel->where(['goods_id' => $v['goods_id']])->find()->toArray();
                $new_list[$k]['appointment_start_time'] = date('Y-m-d H:i:s', $purchase_info['appointment_start_time']);
                $new_list[$k]['appointment_end_time']   = date('Y-m-d H:i:s', $purchase_info['appointment_end_time']);
                $new_list[$k]['draw_time']              = date('Y-m-d H:i:s', $purchase_info['draw_time']);
            }

            $new_list[$k]['operate'] = showNewOperate(self::makeButton($v['goods_id'], $v['product_types']));
        }
        $return['count'] = $this->getListTotal($where);
        $return['data']  = $new_list;
        $return['code']  = '0';
        $return['msg']   = 'OK';

        return $return;
    }


    /**
     * 获取藏品列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time      : 2022/6/13   10:10
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getReserveGoodsList($param)
    {
        $limit                  = $param['limit'];
        $offset                 = ($param['page'] - 1) * $limit;
        $where                  = [];
        $where['is_del']        = 0;
        $where['product_types'] = isset($param['types']) ? $param['types'] : 2;
        if (!empty($param['goods_name'])) {
            $where['goods_name'] = ['like', '%' . $param['goods_name'] . '%'];
        }
        if (!empty($param['writer_id'])) {
            $where['writer_id'] = $param['writer_id'];
        }
        if (!empty($param['category_id'])) {
            $where['category_id'] = $param['category_id'];
        }
        // 创建时间
        if (!empty($param['create_name'])) {
            $create_name          = explode('~', $param['create_name']);
            $where['create_time'] = ['between', [strtotime(trim($create_name[0])), strtotime(trim($create_name[1]))]];
        }
        // 开售时间
        if (!empty($param['start_time'])) {
            $start_time          = explode('~', $param['start_time']);
            $where['start_time'] = ['between', [strtotime(trim($start_time[0])), strtotime(trim($start_time[1]))]];
        }

        //取出现在的藏品 id  做判断
        $CalendarInfo = (new CalendarModel())->where(['id' => $param['calendar_id']])->find();

        $list          = $this->where($where)
            ->order(['goods_sort' => 'desc'])
            ->with(['category', 'writer'])
            ->limit($offset, $limit)
            ->select();
        $PurchaseModel = new Purchase();
        $new_list      = [];
        foreach ($list as $k => $v) {
            $new_list[$k]['LAY_CHECKED'] = false;
            if (in_array($v['goods_id'], explode(',', $CalendarInfo['goods_id']))) {
                $new_list[$k]['LAY_CHECKED'] = true;
            }
            $new_list[$k]['asset_status']        = empty($v['asset_id']) ? '<span class="layui-badge">未上链</span>' : '<span class="layui-badge layui-bg-blue">已上链</span>';
            $new_list[$k]['goods_id']            = $v['goods_id'];
            $new_list[$k]['goods_name']          = $v['goods_name'];
            $new_list[$k]['goods_thumb']         = $v['goods_thumb'];
            $new_list[$k]['goods_price']         = $v['goods_price'];
            $new_list[$k]['goods_category_name'] = $v['category']['name'];
            $new_list[$k]['sales_actual']        = $v['sales_initial'];
            $new_list[$k]['goods_sort']          = $v['goods_sort'];
            $new_list[$k]['goods_status']        = $v['goods_status']['text'];
            $new_list[$k]['create_time']         = $v['create_time'];
            $new_list[$k]['writer_name']         = $v['writer']['name'];
            $new_list[$k]['buy_num']             = $v['buy_num'];
            $new_list[$k]['is_sold']             = $v['stock_num'];
            $new_list[$k]['stock_num']           = $v['stock_num'];
            $new_list[$k]['sales_initial']       = $v['sales_initial'];
            $new_list[$k]['start_time']          = $v['start_time'] ? date('Y-m-d H:i:s', $v['start_time']) : '';

            if ($v['product_types'] == 4) {
                // 获取申购藏品预约时间
                $purchase_info                          = $PurchaseModel->where(['goods_id' => $v['goods_id']])->find()->toArray();
                $new_list[$k]['appointment_start_time'] = date('Y-m-d H:i:s', $purchase_info['appointment_start_time']);
                $new_list[$k]['appointment_end_time']   = date('Y-m-d H:i:s', $purchase_info['appointment_end_time']);
                $new_list[$k]['draw_time']              = date('Y-m-d H:i:s', $purchase_info['draw_time']);
            }

            $new_list[$k]['operate'] = showNewOperate(self::makeButton($v['goods_id'], $v['product_types']));
        }

        $return['count'] = $this->getListTotal($where);
        $return['data']  = $new_list;
        $return['code']  = '0';
        $return['msg']   = 'OK';

        return $return;
    }


    /**
     * 获取总条数
     * @param $where
     * @return int|string
     * @throws \think\Exception
     * @Time      : 2022/6/13   10:35
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface getListTotal
     */
    public function getListTotal($where)
    {
        return $this->where($where)
            ->order(['goods_sort' => 'asc'])
            ->with(['category', 'writer'])
            ->count();
    }

    /**
     * 操作按钮
     * @param $goods_id
     * @param $product_types
     * @return array
     * @Time      : 2022/6/16   11:40
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static function makeButton($goods_id, $product_types)
    {
        $blockchain = Setting::getItem('blockchain');
        if ($product_types == 1) { // 平台
            $returnArray = [
                '兑换码' => [
                    'href'      => url('collection.goods/getConvertList', ['goods_id' => $goods_id]),
                    'lay-event' => '',
                ],
                '编辑'   => [
                    'href'      => url('collection.goods/renew', ['goods_id' => $goods_id]),
                    'lay-event' => '',
                ],
                '删除'   => [
                    'href'      => "javascript:void(0)",
                    'lay-event' => 'remove',
                ],
            ];
            if ($blockchain['default'] == 'BD' || $blockchain['default'] == 'WC') {
                $returnArray['上链发行'] = [
                    'href'      => "javascript:void(0)",
                    'lay-event' => 'windChain',
                ];
            }
            return $returnArray;
        } else if ($product_types == 2) { // 空投
            $returnArray = [
                '空投记录' => [
                    'href'      => url('collection.airdrop/airdropRecord', ['goods_id' => $goods_id]),
                    'lay-event' => '',
                ],
                '编辑'     => [
                    'href'      => url('collection.airdrop/renew', ['goods_id' => $goods_id]),
                    'lay-event' => '',
                ],
                '删除'     => [
                    'href'      => "javascript:void(0)",
                    'lay-event' => 'remove',
                ],
            ];
            if ($blockchain['default'] == 'BD' || $blockchain['default'] == 'WC') {
                $returnArray['上链发行'] = [
                    'href'      => "javascript:void(0)",
                    'lay-event' => 'windChain',
                ];
            }
            return $returnArray;
        } elseif ($product_types == 3) { // 盲盒
            $returnArray = [
                '藏品管理' => [
                    'href'      => url('collection.blindbox/getGoodsList', ['goods_id' => $goods_id]),
                    'lay-event' => '',
                ],
                '编辑'     => [
                    'href'      => url('collection.blindbox/renew', ['goods_id' => $goods_id]),
                    'lay-event' => '',
                ],
                '删除'     => [
                    'href'      => "javascript:void(0)",
                    'lay-event' => 'remove',
                ],
            ];
            return $returnArray;
        } elseif ($product_types == 4) { // 申购
            $returnArray = [
                '预约记录' => [
                    'href'      => url('collection.purchase/getAppointmentList', ['goods_id' => $goods_id]),
                    'lay-event' => '',
                ],
                '编辑'     => [
                    'href'      => url('collection.purchase/renew', ['goods_id' => $goods_id]),
                    'lay-event' => '',
                ],
                '删除'     => [
                    'href'      => "javascript:void(0)",
                    'lay-event' => 'remove',
                ],
            ];
            if ($blockchain['default'] == 'BD' || $blockchain['default'] == 'WC') {
                $returnArray['上链发行'] = [
                    'href'      => "javascript:void(0)",
                    'lay-event' => 'windChain',
                ];
            }
            return $returnArray;
        } else {
            return [];
        }
    }

    /**
     * 添加
     * @param $data
     * @param $time
     * @return false|int
     * @Time      : 2022/6/13   10:10
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface add
     */
    public function add($data, $time = [])
    {
        if (!isset($data['goods_thumb']) || empty($data['goods_thumb'])) {
            $this->error = '请上传作品图片';
            return false;
        }
        if (!isset($data['d_images']) || empty($data['d_images'])) {
            $this->error = '请上传3D图片';
            return false;
        }
        if (mb_strlen($data['goods_name']) > 11) {
            $this->error = '名称不能超过12个字符';
            return false;
        }
        $data['content']    = isset($data['content']) ? $data['content'] : '';
        $data['app_id']     = self::$app_id;
        $data['start_time'] = isset($data['start_time']) ? strtotime($data['start_time']) : '';
        // 申购藏品
        if ($data['product_types'] == 4) {
            if (strtotime($time['appointment_start_time']) > strtotime($time['appointment_end_time'])) {
                $this->error = '预约开始时间必须小于预约结束时间';
                return false;
            }
            if (strtotime($time['appointment_end_time']) < strtotime($time['appointment_start_time'])) {
                $this->error = '预约结束时间必须大于预约开始时间';
                return false;
            }
            if (strtotime($data['draw_time']) < strtotime($time['appointment_end_time']) || strtotime($data['draw_time']) < strtotime($time['appointment_start_time'])) {
                $this->error = '抽签时间必须大于预约时间';
                return false;
            }

            if (strtotime($data['draw_time']) > $data['start_time']) {
                $this->error = '售卖时间需要大于抽签时间';
                return false;
            }
        }
        Db::startTrans();
        try {
            $this->allowField(true)->save($data);
            if ($data['product_types'] == 1 || $data['product_types'] == 3 || $data['product_types'] == 4 || $data['product_types'] == 5) {
                //设定 一个新的键值 作为库存处理 ||
                $this->auctionGoods($this['goods_id'], 1);
            }
            //将商品库存 写入商品库存中
            StockUtils::addStock($this['goods_id'], $data['stock_num']);
            // 添加申购预约时间
            if ($data['product_types'] == 4 && !empty($time)) {
                (new Purchase())->insert([
                    'goods_id'               => $this['goods_id'],
                    'appointment_start_time' => strtotime($time['appointment_start_time']),
                    'appointment_end_time'   => strtotime($time['appointment_end_time']),
                    'draw_time'              => strtotime($data['draw_time']),
                    'app_id'                 => self::$app_id,
                    'create_time'            => time(),
                    'update_time'            => time(),
					'init_booking_num' => $data['init_booking_num']
				]);
            }
            //判断如果是盲盒的商品则需要特殊处理下入库操作
            if ($data['product_types'] == 5) {
                $data['sort'] = $data['goods_sort'];
                $res = (new Blindbox())->add($data, $this['goods_id']);
            }
            Db::commit();
            return true;
        } catch (Exception $e) {
            pre($e);
            Db::rollback();
            return $this->error = $e->getMessage();
        }
    }


    /**
     * 编辑
     * @param $data
     * @param $time
     * @return false|int
     * @Time      : 2022/6/13   10:11
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface edit
     */
    public function edit($data, $time = [])
    {
        if (!isset($data['goods_thumb']) || empty($data['goods_thumb'])) {
            $this->error = '请上传作品图片';
            return false;
        }
        if (!isset($data['d_images']) || empty($data['d_images'])) {
            $this->error = '请上传3D图片';
            return false;
        }

        $data['content']    = isset($data['content']) ? $data['content'] : '';
        $data['start_time'] = isset($data['start_time']) ? strtotime($data['start_time']) : '';
        // 申购藏品
        if ($data['product_types'] == 4) {
            if (strtotime($time['appointment_start_time']) > strtotime($time['appointment_end_time'])) {
                $this->error = '预约开始时间必须小于预约结束时间';
                return false;
            }
            if (strtotime($time['appointment_end_time']) < strtotime($time['appointment_start_time'])) {
                $this->error = '预约结束时间必须大于预约开始时间';
                return false;
            }
            if (strtotime($data['draw_time']) < strtotime($time['appointment_end_time']) || strtotime($data['draw_time']) < strtotime($time['appointment_start_time'])) {
                $this->error = '抽签时间必须大于预约时间';
                return false;
            }
            if (strtotime($data['draw_time']) > $data['start_time']) {
                $this->error = '售卖时间需要大于抽签时间';
                return false;
            }
        }
        Db::startTrans();
        try {
            $this->allowField(true)->save($data, ['goods_id' => $this['goods_id']]);
            // 编辑商品
            if ($data['product_types'] == 1 || $data['product_types'] == 3 || $data['product_types'] == 4 || $data['product_types'] == 5) {
                $this->auctionGoods($this['goods_id'], 2);
            }
            //更新库存
            StockUtils::addStock($this['goods_id'], $data['stock_num']);
            // 编辑申购预约时间
            if ($data['product_types'] == 4 && !empty($time)) {
                (new Purchase())->where(['goods_id' => $this['goods_id']])->update([
                    'appointment_start_time' => strtotime($time['appointment_start_time']),
                    'appointment_end_time'   => strtotime($time['appointment_end_time']),
                    'draw_time'              => strtotime($data['draw_time']),
					'init_booking_num' => $data['init_booking_num']
                ]);
            }
            //判断如果是盲盒的商品则需要特殊处理下入库操作
            if ($this['product_types'] == 5) {
                $data['sort'] = $data['goods_sort'];
                $res = (new Blindbox())->edit($data, $this['goods_id']);
            }
            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 删除
     * @param $product_types
     * @return false|int
     * @throws \think\exception\DbException
     * @Time      : 2022/6/13   15:44
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface setDelete
     */
    public function setDelete($product_types)
    {
        $redis = initRedis();
        // 删除商品
        if ($product_types == 1 || $product_types == 3 || $product_types == 4) {
            $redis->del("collection:goods_id:{$this['goods_id']}");
        }
        //删除库存键
        StockUtils::delStock($this['goods_id']);
        return $this->save(['is_del' => self::$is_del], ['goods_id' => $this['goods_id']]);
    }

    /**
     * 修改产品上下架
     * @param $param
     * @return Goods
     * @throws \think\exception\DbException
     * @Time      : 2022/6/13   15:35
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface updateGoodsStatus
     */
    public function updateGoodsStatus($param)
    {
        $info = $this->where(['goods_id' => $param['goods_id']])->find();
        if ($param['goods_status'] == 10) {
            // 上架
            if ($info['product_types'] == 1 || $info['product_types'] == 3 || $info['product_types'] == 4) {
                //先修改下商品的数据
                $this->where(['goods_id' => $param['goods_id']])->update(['goods_status' => 10]);
                $this->auctionGoods($param['goods_id'], 2);
                return true;
            }
        } elseif ($param['goods_status'] == 20) {
            // 下架
            if ($info['product_types'] == 1 || $info['product_types'] == 3 || $info['product_types'] == 4) {
                $this->auctionGoods($param['goods_id'], 3);
            }
            // 判断此藏品是否添加为发售日历
            $CalendarModel = new CalendarModel();
            $goods_id      = $param['goods_id'];
            $where[]       = ['exp', Db::raw("FIND_IN_SET($goods_id,goods_id)")];
            $isHave        = $CalendarModel->where($where)->find();
            if (!empty($isHave)) {
                // 删除掉此商品
                $new_goods_ids = [];
                $goods_ids     = explode(',', $isHave['goods_id']);
                foreach ($goods_ids as $key => $value) {
                    if ($value == $goods_id) {
                        $new_goods_ids = $value;
                        break;
                    }
                }
                $CalendarModel->where(['id' => $isHave['id']])->update(['goods_id' => $new_goods_ids, 'update_time' => time()]);
            }
            return $this->where(['goods_id' => $param['goods_id']])->update(['goods_status' => $param['goods_status']]);
        }
    }


    /**
     * 将商品存入Redis
     * @param        $goods_id
     * @param        $type 1=添加商品  2=编辑商品（找到对应的商品ID 删除此条缓存 重新存入）
     * @param string $stock_num
     * @throws \think\exception\DbException
     * @Time      : 2022/6/13   15:35
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface auctionGoods
     */
    public function auctionGoods($goods_id, $type, $stock_num = '')
    {
        // 将商品id存入redis链表中
        $redis = initRedis();
        // 根据auctionID查询该ID的详细数据
        $goodsInfo = self::detail($goods_id);
        //初始化一个库存
        if ($goodsInfo['goods_status']['value'] == 20) {
            return true;
        }
        //  重新组装数据
        $newGoods['goods_id']   = $goodsInfo['goods_id'];
        $newGoods['goods_name'] = $goodsInfo['goods_name'];
        $newGoods['product_types'] = $goodsInfo['product_types'];
        $newGoods['content']         = $goodsInfo['content'];
        $newGoods['start_time']      = $goodsInfo['start_time'];
        $newGoods['goods_sales']     = $goodsInfo['sales_initial'];
        $newGoods['stock_num']       = $goodsInfo['stock_num'];
        $newGoods['goods_images']    = $goodsInfo['goods_thumb'];
        $newGoods['goods_price']     = $goodsInfo['goods_price'];
        $newGoods['category_name']   = $goodsInfo['category']['name'];
        $newGoods['writer_name']     = $goodsInfo['writer']['name'];
        $newGoods['writer_headimg']  = $goodsInfo['writer']['headimg'];
        $newGoods['d_images']        = $goodsInfo['d_images'];
        $newGoods['buy_num']         = $goodsInfo['buy_num'];
        $newGoods['issue_name']      = $goodsInfo['issue_name'];
        $newGoods['issue_tag']       = $goodsInfo['issue_tag'];
        $newGoods['goods_no']        = $goodsInfo['goods_no'];
        $newGoods['goods_sort']      = $goodsInfo['goods_sort'];
        $newGoods['goods_type']      = $goodsInfo['goods_type'];
        $newGoods['audio_link_url']  = $goodsInfo['audio_link_url'];
        $newGoods['video_link_url']  = $goodsInfo['video_link_url'];
        $newGoods['is_sold']         = $goodsInfo['is_sold']; //是否售罄
        $newGoods['original_number'] = $goodsInfo['original_number']; //发行量
        $newGoods['is_suffix']       = $goodsInfo['is_suffix']; //图片后缀
        $newGoods['is_pedestal']     = $goodsInfo['is_pedestal']; //图片后缀
        //  释放订单，修改商品库存
        if (!empty($stock_num)) {
            $newGoods['stock_num'] = $stock_num;
        }
        //  添加商
        if ($type == 1) {
            if ($goodsInfo['stock_num'] > 0) {
                $redis->Rpush("collectionList", $goods_id);
                //  向名称为key的hash中批量添加元素 ,$redis->hMset('user:1', array('name' => 'Joe', 'salary' => 2000));
                $redis->hMSet("collection:goods_id:$goods_id", $newGoods); //将商品详情数据存入到hash数据表中
            }
        } else if ($type == 2) {
            $goodsID = $redis->LRANGE("collectionList", "0", "-1");
            if (!in_array($goods_id, $goodsID)) {
                $redis->Rpush("collectionList", $goods_id);
            }
            $redis->hMSet("collection:goods_id:$goods_id", $newGoods); //将商品详情数据存入到hash数据表中
        } else if ($type == 3) {
            $data = $redis->hGetAll("collection:goods_id:$goods_id");
            if ($data) {
                $redis->del("collection:goods_id:$goods_id");
            }
        }
    }

    /**
     * 商品上架
     * @author    Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time      2022/6/22 10:57
     */
    public function putaway()
    {
        //判断下类型 然后处理下
        $blockchain = Setting::getItem('blockchain');
        switch ($blockchain['default']) {
            case "BD":
                $res = (new Task())->createAssets($this['goods_thumb'], $this['goods_name'], $this['stock_num'], $this['goods_price'], $this['goods_name'], 0, $this['goods_id']);
                if (1 == $res['code']) {
                    $this->save(['asset_id' => $res['data']['asset_id']]);
                    return true;
                } else {
                    return false;
                }
            case "WC":
                $result = (new Task())->wxCreateNfts($this['goods_thumb'], $this['goods_name'], $this['stock_num'], $this['goods_price'], $this['goods_name'], 0, $this['goods_id']);
                if (1 == $result['code']) {
                    $this->save(['asset_id' => $result['data']['asset_id']]);
                    return true;
                } else {
                    return false;
                }
        }
    }

    /**
     * 商品上架
     * @author    Mr.Liu
     * @copyright 河南八六互联信息技术有限公司
     * @Time      2022/6/22 10:57
     */
    public function soldstatus($param)
    {
        $goods_info = $this->where(['goods_id' => $param['goods_id']])->find();
        //获取藏品库存
        $stock_num = StockUtils::getStock($param['goods_id']);
        if ($param['is_sold'] == 20) { //下架
            //修改前端展示库存 不在进行售卖
            StockUtils::addStock($param['goods_id'], 0);
            return $this->where(['goods_id' => $param['goods_id']])->update(['stock_num' => 0, 'is_sold' => 20, 'is_sold_number' => $stock_num]);
        } else {  //上架
            $stock_values = bcadd($stock_num, $goods_info['is_sold_number']);
            StockUtils::addStock($param['goods_id'], $stock_values);
            return $this->where(['goods_id' => $param['goods_id']])->update(['stock_num' => $stock_values, 'is_sold' => 10, 'is_sold_number' => 0]);
        }
    }

    /**
     * 获取官方市场交易列表
     * @param $param
     * @Email    :sliyusheng@foxmail.com
     * @Company  河南八六互联信息技术有限公司
     * @DateTime 2022-08-20 11:49
     */
    public function secureOfficialDeal($param)
    {
        $where = [
            'goods_status' => 10,
            'recovery_num' => ['>', 0],
            //            'recovery_status' => 1
        ];
        if (!empty($param['goods_name'])) {
            $where['goods_name'] = ['like', '%' . $param['goods_name'] . '%'];
        }
        if (!empty($param['writer_id'])) {
            $where['writer_id'] = $param['writer_id'];
        }
        if (!empty($param['category_id'])) {
            $where['category_id'] = $param['category_id'];
        }
        if (array_key_exists('recovery_status', $param) && $param['recovery_status'] != '') {
            $where['recovery_status'] = $param['recovery_status'];
        }

        $page         = request()->param('page') ?: 1;//当前第几页
        $limit        = request()->param('limit') ?: 10;//每页显示几条
        $data         = GoodsModel::where($where)
            ->where('is_del', 0)
            ->paginate($limit, false, $config = ['page' => $page])
            ->toArray();
        $arr['data']  = $data['data'];
        $arr['code']  = '0';
        $arr['msg']   = 'OK';
        $arr['count'] = $data['total'];
        foreach ($arr['data'] as &$item) {
            $item['sale_status'] = $item['recovery_status'] == 1 ? 10 : 20;
            $item['id']          = $item['goods_id'];
        }
        return json($arr);
    }

    public function operation($param)
    {
        $goods           = GoodsModel::where('goods_id', $param['goods_id'])->find();
        $recovery_status = 1;
        if ($goods['recovery_status'] == 1) {
            $recovery_status = 0;
        }
        $goods->recovery_status = $recovery_status;
        if ($goods->save()) {
            return true;
        }
        return false;
    }
    /**
     * 注册赠送空投
     * @param int $member_id
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-09-16 10:59
     */
    public function registerFreeDrop(int $member_id)
    {
        $collection = Setting::getItem('collection');
        if ($collection['new_drop_status']==20){
            return false;
        }
        self::drop($collection['new_drop_goods_id'],$member_id);
    }

    /**
     * 邀请赠送空投
     * @param int $member_id
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-09-16 11:41
     */
    public function invitationDrop(int $member_id)
    {
        $collection = Setting::getItem('collection');
        if ($collection['new_drop_status']==20){
            return false;
        }
        $member_data = Member::where('member_id',$member_id)->find();
        if ($member_data['invitation_count']>=($collection['invitation_drop_count']-1)){
            //邀请人数已经满足
            $member_data->invitation_count = 0;
            $member_data->save();
            self::drop($collection['invitation_drop_goods_id'],$member_id);
        }else{
            //没有满足条件
            $member_data->setInc('invitation_count',1);
        }
    }

    /**
     * 藏品空投
     * @param int $goods_id
     * @param int $member_id
     * @return false
     * @Email:sliyusheng@foxmail.com
     * @Company 河南八六互联信息技术有限公司
     * @DateTime 2022-09-16 11:18
     */
    public static function drop(int $goods_id, int $member_id)
    {
        if (!StockUtils::deductStock($goods_id, 1)) {
            write_log('ID' . $member_id . '用户自动空投失败,空投ID'.$goods_id.'空投不足',RUNTIME_PATH.'drop_log');
            return false;
        }
        $member_data = Member::where('member_id', $member_id)->find();
        $goods_data = Goods::with('writer')->where('goods_id', $goods_id)->find();
        if (!$goods_data){
            write_log('ID' . $member_id . '用户自动空投失败,空投ID'.$goods_id.'空投不存在',RUNTIME_PATH.'drop_log');
        }
        $data['member_id'] = $member_id;
        $data['goods_id'] = $goods_id;
        $data['phone'] = $member_data->phone;
        $data['nickname'] = $member_data->name;
        $data['goods_no'] = $goods_data->goods_no;
        $data['goods_name'] = $goods_data->goods_name;
        $data['goods_thumb'] = $goods_data->goods_thumb;
        $data['goods_price'] = $goods_data->goods_price;
        $data['total_num'] = 1;
        $data['writer_id'] = $goods_data->writer_id;
        $data['writer_name'] = $goods_data->writer->name;
        $data['source_type'] = 4;
        $data['app_id'] = 10001;
        MemberGoods::create($data);
        $member_goods_insertId = (new MemberGoods())->getLastInsID();
        //加入铸造藏品的队列
        (new OrderNoticeModel())->castingQuestionList($member_goods_insertId, $goods_data['goods_id'], $member_id, "casting_question_list");

    }


    /**
     * 获取藏品列表
     * @param $param
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @Time      : 2022/6/13   10:10
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface getList
     */
    public function getRecommendList($param)
    {
        $limit                  = $param['limit'];
        $offset                 = ($param['page'] - 1) * $limit;
        $where                  = [];
        $where['is_del']        = 0;
        $where['is_recommend']        = 10;
        $where['product_types'] = isset($param['types']) ? $param['types'] : 2;
        if (!empty($param['goods_name'])) {
            $where['goods_name'] = ['like', '%' . $param['goods_name'] . '%'];
        }
        if (!empty($param['writer_id'])) {
            $where['writer_id'] = $param['writer_id'];
        }
        if (!empty($param['category_id'])) {
            $where['category_id'] = $param['category_id'];
        }
        if (!empty($param['product_types'])) {
            $where['product_types'] = $param['product_types'];
        }
        // 创建时间
        if (!empty($param['create_name'])) {
            $create_name          = explode('~', $param['create_name']);
            $where['create_time'] = ['between', [strtotime(trim($create_name[0])), strtotime(trim($create_name[1]))]];
        }
        // 开售时间
        if (!empty($param['start_time'])) {
            $start_time          = explode('~', $param['start_time']);
            $where['start_time'] = ['between', [strtotime(trim($start_time[0])), strtotime(trim($start_time[1]))]];
        }

        $list = $this->where($where)
            ->order(['goods_sort' => 'desc'])
            ->with(['category', 'writer'])
            ->limit($offset, $limit)
            ->select();
        $PurchaseModel = new Purchase();
        $new_list = [];
        foreach ($list as $k => $v) {
            $new_list[$k]['asset_status']        = empty($v['asset_id']) ? '<span class="layui-badge">未上链</span>' : '<span class="layui-badge layui-bg-blue">已上链</span>';
            $new_list[$k]['goods_id']            = $v['goods_id'];
            $new_list[$k]['goods_name']          = $v['goods_name'];
            $new_list[$k]['goods_thumb']         = $v['goods_thumb'];
            $new_list[$k]['goods_price']         = $v['goods_price'];
            $new_list[$k]['product_types']       = $v['product_types'];
            $new_list[$k]['goods_category_name'] = $v['category']['name'];
            $new_list[$k]['sales_actual']        = $v['sales_actual'];
            $new_list[$k]['goods_sort']          = $v['goods_sort'];
            $new_list[$k]['goods_status']        = $v['goods_status']['text'];
            $new_list[$k]['create_time']         = $v['create_time'];
            $new_list[$k]['writer_name']         = $v['writer']['name'];
            $new_list[$k]['buy_num']             = $v['buy_num'];
            $new_list[$k]['is_sold']             = $v['stock_num'];
            $new_list[$k]['is_sold_number']      = $v['stock_num'];
            $new_list[$k]['stock_num']           = $v['stock_num'];
            $new_list[$k]['sales_initial']       = $v['sales_initial'];
            $new_list[$k]['original_number']     = $v['original_number'];
            $new_list[$k]['start_time']          = $v['start_time'] ? date('Y-m-d H:i:s', $v['start_time']) : '';

            if ($v['product_types'] == 4) {
                // 获取申购藏品预约时间
                $purchase_info                          = $PurchaseModel->where(['goods_id' => $v['goods_id']])->find()->toArray();
                $new_list[$k]['appointment_start_time'] = date('Y-m-d H:i:s', $purchase_info['appointment_start_time']);
                $new_list[$k]['appointment_end_time']   = date('Y-m-d H:i:s', $purchase_info['appointment_end_time']);
                $new_list[$k]['draw_time']              = date('Y-m-d H:i:s', $purchase_info['draw_time']);
            }

            $new_list[$k]['operate'] = showNewOperate(self::makeRecommendButton($v['goods_id'], $v['product_types']));
        }
        $return['count'] = $this->getListTotal($where);
        $return['data']  = $new_list;
        $return['code']  = '0';
        $return['msg']   = 'OK';

        return $return;
    }


    /**
     * 操作按钮
     * @param $goods_id
     * @param $product_types
     * @return array
     * @Time      : 2022/6/16   11:40
     * @author    : [Mr.Ai] [18612421593@163.com]
     * @Interface makeButton
     */
    private static function makeRecommendButton($goods_id, $product_types)
    {
         return [
            '删除'   => [
                'href'      => "javascript:void(0)",
                'lay-event' => 'remove',
            ]
        ];

    }
}