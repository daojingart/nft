<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/27   11:14 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 获取相关信息类
 * +----------------------------------------------------------------------
 */

namespace app\api\controller;

use app\common\extend\wx\Qrcode;
use app\common\extend\wx\Subscribe;
use app\common\extend\wx\Wechat;
use app\common\extend\wx\WxShare;
use app\common\model\Member;
use app\common\model\WxSetting;
use app\common\controller\Controller as ApiController;

/**
 * 微信支付登录
 */
class Wx extends ApiController
{
	protected $noNeedLogin = [
		'getPayWxLogin','getCodeToken'
	];

	/**
	 * 获取静默授权的URL
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/Wx/getPayWxLogin)
	 * @ApiParams   (name="current_url", type="string", required=true, description="用户名")
	 * @ApiReturnParams   (name="data", type="string", description="跳转的授权URL")
	 */
	public function getPayWxLogin()
	{
		$current_url = $this->request->param('current_url');
		if(!$current_url){
			$this->error("参数错误");
		}
		$callBack_url =  Wechat::getWxPayLoginUrl($current_url);
		$this->success('',$callBack_url);
	}

	/**
	 * 通过code 获取openid
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/Wx/getCodeToken)
	 * @ApiParams   (name="code", type="string", required=true, description="授权成功后URL获取的code")
	 * @ApiReturnParams   (name="data", type="string", description="openID")
	 */
	public function getCodeToken()
	{
		$code = $this->request->param('code');
		if(!$code){
			$this->error("参数错误");
		}
		//通过Code  获取用户信息OPENID
		$wechatInfo = Wechat::wxPayLogin($code);
		$openid = '';
		if(isset($wechatInfo['openid'])){
			$openid = $wechatInfo['openid'];
		}
		$this->success('',$wechatInfo['openid']);
	}

	/**
	 * 获取是否判断openID
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/Wx/getIsOpenid)
	 * @ApiReturnParams   (name="data", type="string", description="1=可以直接支付  2=直接跳转到获取openID")
	 */
	public function getIsOpenid()
	{
		if ($this->auth->open_id) {
			$this->success("ok", 1);
		}
		$this->success("需要获取OPENID",2);
	}

	/**
	 * 绑定公众号的OPENID
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/Wx/bindOpenId)
	 * @ApiParams   (name="open_id", type="string", required=true, description="用户名")
	 * @ApiReturnParams   (name="data", type="string", description="1=绑定成功")
	 */
	public function bindOpenId()
	{
		$open_id = $this->request->param('open_id');
		if (!$open_id) {
			$this->error("参数错误");
		}
		if (!$this->auth->open_id) {
			(new Member())->where(['member_id' => $this->auth->member_id])->update([
				'open_id' => $open_id,
			]);
		}
		$this->success("绑定成功", "1");
	}
}