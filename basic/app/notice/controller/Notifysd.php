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
 * | className: 杉德支付回调
 * +----------------------------------------------------------------------
 */

namespace app\notice\controller;


use app\common\model\PaySetting;
use sd\Callback;
use think\Controller;

/**
 * 杉德异步回调通知
 */
class Notifysd extends Controller
{
	protected $callback;

	public function __construct()
	{
		$this->callback = new Callback(PaySetting::getItem('sdPay'));
		parent::__construct();
	}

	/**
	 * 云账户开户异步
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface Cloudnotify
	 * @Time: 2022/9/24   22:32
	 */
	public function Cloudnotify()
	{
		$this->callback->cloudAcctmgrCallback(file_get_contents('php://input'));
	}

	/**
	 * 杉德支付回调
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface sdCard
	 * @Time: 2022/8/5   17:04
	 */
	public function quickNotifyUrl()
	{
	   
		$this->callback->quickPaymentCallback(file_get_contents('php://input'));
	}

	/**
	 * 杉德开户费支付回调
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface sdCard
	 * @Time: 2022/8/5   17:04
	 */
	public function accountNotifyUrl()
	{
		$this->callback->accountPaymentCallback(file_get_contents('php://input'));
	}

	/**
	 *  支付异步通知 C2B
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface notify
	 * @Time: 2022/9/24   22:32
	 */
	public function synchronizeNotifyC2B()
	{
		$this->callback->cloudPaymentC2bCallback(file_get_contents('php://input'));
	}

	/**
	 *  支付异步通知 C2C
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface notify
	 * @Time: 2022/9/24   22:32
	 */
	public function synchronizeNotifyC2C()
	{
		$this->callback->cloudPaymentC2cCallback(file_get_contents('php://input'));
	}


	/**
	 * 注销账户的回调
	 * @ApiAuthor [Mr.Zhang]
	 */
	public function setAccountStatus()
	{
		write_log(__DIR__,file_get_contents('php://input'));
	}

}
