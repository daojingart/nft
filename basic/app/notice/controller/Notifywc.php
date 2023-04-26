<?php

/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/3/23   3:35 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 文昌异步回调 创建类别
 * +----------------------------------------------------------------------
 */

namespace app\notice\controller;

use app\common\model\Goods;

class Notifywc
{

	/**
	 * 异步回调
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * notice/Notifywc/classesCallback
	 */
	public function classesCallback()
	{
		$param = file_get_contents('php://input');
		$param_array = json_decode($param, true);
		write_log($param_array, __DIR__);
		if($param_array['message'] == 'SUCCESS') {
			$goods_info = (new Goods())->where(['asset_id'=>$param_array['operation_id']])->find();
			if (!empty($goods_info) && $goods_info['hash'] == 0){
				(new Goods())->where(['goods_id'=>$goods_info['goods_id']])->update(['hash'=>$param_array['tx_hash'],'asset_id'=>$param_array['nft']['class_id']]);
				die('SUCCESS');
			}
		}
	}

}