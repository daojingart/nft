<?php

namespace app\admin\controller\goods;

use app\admin\controller\Controller;
use app\admin\model\Setting as SettingModel;

class Setting extends Controller
{
	/**
	 * @Notes: 商城配置
	 * @Interface store
	 * @return array|mixed
	 * @throws \think\exception\DbException
	 * @author: Mr.Zhang
	 * @copyright: 河南八六互联信息技术有限公司
	 * @Time: 2021/3/27   11:33 上午
	 */
	public function index()
	{
		return $this->updateEvent('index');
	}



	/**
	 * 更新配置
	 * @param $key
	 * @return array|mixed
	 * @throws \think\exception\DbException
	 */
	private function updateEvent($key)
	{
		if (!$this->request->isAjax()) {
			$values = SettingModel::getItem($key);
			return $this->fetch($key, compact('values'));
		}
		$model = new SettingModel;
		$param = $this->postData($key);
		if ($model->edit($key, $param)) {
			return $this->renderSuccess('更新成功');
		}
		return $this->renderError('更新失败');
	}

}