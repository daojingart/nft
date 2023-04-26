<?php

namespace app\common\helpers;

use app\common\library\SdPay;
use app\common\model\PaySetting;
use app\common\model\WxSetting as WxSettingModel;
use think\Config;
use think\Url;
use Yansongda\Pay\Exception\ContainerException;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Provider\Alipay;
use Yansongda\Pay\Provider\Wechat;

/**
 * 支付工具类
 * Tools
 * app\common\components\helpers
 */
class PayTools
{
	public static $success_text = "333";

	/**
	 * 获取用户支付的权限
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 */
	public static function getPayTools($member_id)
	{
		//获取目前支付权限的开关
		$paySettingList = (new PaySetting())->getPayGetWay(3);
		foreach ($paySettingList as $value){
			switch ($value['id'])
			{
				case 3: //连连支付
					break;
				case 4: //杉德支付
					//判断当前后台是否开启杉德支付 开启需要开启钱包后才可以使用
					if(isset($paySettingList[3]['open_purse_status']) && $paySettingList[3]['open_purse_status'] == 10){
						//查询用户是否开启
						if((new SdPay())->getUserStatus($member_id)){
							return true;
						}
						return false;
					}
					return false;
					break;
				case 5: //汇付支付
					break;
				case 6: //汇元支付
					break;
				case 7: //汇付通
					break;
				case 8: //易宝支付
					break;
				case 9: //首信易支付
					break;
			}
		}

	}

	/**
	 * 获取提示
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 */
	public  function getSuccessText()
	{
		echo (new self)->success_text;die;
	}
}