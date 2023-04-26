<?php

namespace app\admin\model\application;

use app\common\model\BaseModel;
use app\common\model\Member;

class GoodsSyntheticWhitelist extends BaseModel
{
	protected $name = 'goods_synthetic_whitelist';


	/**
	 * 添加白名单
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  ()
	 * @ApiParams   (name="name", type="string", required=true, description="用户名")
	 * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
	 */
	public function addAll($data)
	{
		return $this->insertAll($data);
	}

	/**
	 * 获取列表
	 * @Email:sliyusheng@foxmail.com
	 * @Company 河南八六互联信息技术有限公司
	 * @DateTime 2022-06-15 11:06
	 */
	public static function getList()
	{
		$where = [];
		$param = request()->param();
		if (array_key_exists('name',$param)&&!empty($param['name'])){
			$where['name'] = ['like','%'.$param['name'].'%'];
		}
		$page = request()->param('page')?:1;//当前第几页
		$list = request()->param('limit')?:10;//每页显示几条
		$data = self::where($where)->order('id desc')->paginate($list,false,$config = ['page'=>$page])->toArray();
		foreach ($data['data'] as &$item){
			$member_info = Member::detail(['member_id'=>$item['member_id']]);
			$item['phone'] = $member_info['phone'];
			$item['name'] = $member_info['name'];
			$item['operate'] = showNewOperate(self::makeButton($item['id']));

		}
		$arr['data'] = $data['data'];
		$arr['code'] = '0';
		$arr['msg'] = 'OK';
		$arr['count'] = $data['total'];
		return  json($arr);
	}


	private static  function makeButton($id)
	{
		return [
			'删除' => [
				'href' => "javascript:void(0)",
				'lay-event' => 'remove',
			],
		];
	}

}