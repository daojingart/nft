<?php

namespace app\api\controller;

use app\admin\model\Setting as SettingModel;
use app\api\model\Banner;
use app\common\controller\Controller as ApiController;
use app\common\model\Nav;
use app\common\model\Navigation;
use app\common\model\ProductGuarantee;
use app\common\model\Setting;

/**
 * 公共接口
 */
class Index extends ApiController
{
    protected $noNeedLogin = [
        '*'
    ];

    /**
     * 获取轮播图
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getBannerList)
     * @ApiParams   (name="type", type="string", required=true, description="1=首页 2=商城")
     * @ApiReturnParams   (name="banner_id", type="string", required=true, description="轮播图的ID")
     * @ApiReturnParams   (name="thumb", type="string", required=true, description="图片的URL")
     * @ApiReturnParams   (name="link_url", type="string", required=true, description="点击轮播图跳转的链接")
     */
    public function getBannerList()
    {
        $type = $this->request->param('type');
        if(!$type){
            $this->error("参数错误");
        }
        $this->success('获取成功',(new Banner())->getBannerList($type));
    }

    /**
     * 获取客服信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getCustomerInfo)
     * @ApiReturnParams   (name="is_open", type="string", description="10关闭客服按钮  link==跳转链接   code==二维码弹窗")
     * @ApiReturnParams   (name="link_url", type="string", description="资源链接")
     */
    public function getCustomerInfo()
    {
        $values = Setting::getItem("customer");
        $this->success('获取成功',['is_open'=>$values['default'],'link_url'=>$values['engine'][$values['default']]['link_url']]);
    }

    /**
     * 商城保障信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getProductGuarantee)
     * @ApiReturnParams   (name="title", type="string", description="标题")
     * @ApiReturnParams   (name="content", type="string", description="内容")
     */
    public function getProductGuarantee()
    {
        $list = (new ProductGuarantee())->field("id,title,content")->select();
        $this->success('获取成功',$list);
    }

    /**
     * 获取行为验证码的信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getCaptchaAppId)
     * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
     */
    public function getCaptchaAppId()
    {
        $this->success("获取成功",['appid' => Setting::getItem("behaviorcode")['appid']]);
    }

    /**
     * 获取登录页面的配置
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getLoginSetting)
     * @ApiReturnParams   (name="logo_img", type="string", description="LOGO 图片")
     * @ApiReturnParams   (name="login_title", type="string", description="登录页面副标题")
     * @ApiReturnParams   (name="login_left_logo_img", type="string", description="首页左上角的logo")
     */
    public function getLoginSetting()
    {
        $values = Setting::getItem("store");
        $this->success("获取成功",['logo_img' => $values['login_logo_img'],'login_title'=>$values['login_logo_title_img'],'login_left_logo_img'=>$values['login_left_logo_img']]);
    }


    /**
     * 获取底部版权信息
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getBottomInfo)
     * @ApiReturnParams   (name="copyright", type="string", description="名称")
     * @ApiReturnParams   (name="icp_number", type="string", description="备案号 需要点击可以跳转到https://beian.miit.gov.cn/")
     */
    public function getBottomInfo()
    {
        $values = Setting::getItem("store");
        $this->success("获取成功",['copyright' => $values['copyright'],'icp_number'=>$values['icp_number']]);
    }

    /**
     * 邀请码是否必填
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getIsInvitationCode)
     * @ApiReturnParams   (name="data", type="string", description="10 需要邀请码  20不需要邀请码")
     */
    public function getIsInvitationCode()
    {
        $this->success("获取成功",Setting::getItem("store")['register']);
    }

    /**
     * 获取荣誉值名称
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getStoreScoreName)
     * @ApiReturnParams   (name="data", type="string", description="积分名称")
     */
    public function getStoreScoreName()
    {
        $this->success("获取成功",Setting::getItem("store")['honor']);
    }

    /**
     * 获取首页中间广告图
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getAdBanner)
     * @ApiReturnParams   (name="data", type="string", description="轮播图链接")
     */
    public function getAdBanner()
    {
        $this->success("获取成功",Setting::getItem("store")['login_logo_ad_img']);
    }

    /**
     * 获取广告开关状态
     * @ApiAuthor 2023/4/21-liyusheng
     * @ApiRoute  (/api/index/getAdvertisementStatus)
     * @ApiReturnParams (name="advertisement", type="integer", required=true, description="广告图开关:10=开启,20=关闭")
     */
    public function getAdvertisementStatus()
    {
        $this->success('获取成功',Setting::getItem('store')['advertisement']);
    }
    /**
     * 关于我们
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getAboutInfo)
     * @ApiReturnParams   (name="data", type="string", description="关于我们的内容")
     */
    public function getAboutInfo()
    {
        $this->success("获取成功",htmlspecialchars_decode(Setting::getItem("agreement")['aboutUs']));
    }

    /**
     * 获取隐私协议
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getAgreement)
     * @ApiReturnParams   (name="data", type="string", description="协议配置")
     */
    public function getAgreement()
    {
        $this->success("获取成功",htmlspecialchars_decode(Setting::getItem("agreement")['privacy'],1));
    }

    /**
     * 获取用户协议
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getUserAgreement)
     * @ApiReturnParams   (name="data", type="string", description="协议配置")
     */
    public function getUserAgreement()
    {
        $this->success("获取成功",htmlspecialchars_decode(Setting::getItem("agreement")['user'],1));
    }

    /**
     * 提现须知
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getWithdrawalInfo)
     * @ApiReturnParams   (name="status", type="string", description="提现状态 10=开启提现 20关闭提现；点击提现按钮提示后台设置的文字")
     * @ApiReturnParams   (name="close_text", type="string", description="提示文字")
     * @ApiReturnParams   (name="rules", type="string", description="提现规则")
     */
    public function getWithdrawalInfo()
    {
        $values = Setting::getItem("withdrawal");
        unset($values['handling_fee'],$values['minimum_withdrawal']);
        $this->success("获取成功",$values);
    }

    /**
     * 获取模块权限
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getAuthInfo)
     * @ApiReturnParams   (name="permissions", type="string", description="10=开启寄售模块 20=关闭寄售模块")
     * @ApiReturnParams   (name="repo", type="string", description="10=开启签到页面 20=关闭签到页面")
     * @ApiReturnParams   (name="shop_open", type="string", description="10=开启商城 20=关闭商城")
     */
    public function getAuthInfo()
    {
        $values = Setting::getItem("collection");
        $valuesShop = Setting::getItem("index");
        $this->success("获取成功",['permissions'=>$values['permissions'],'repo'=>$values['repo'],'shop_open'=>$valuesShop['register']]);
    }

    /**
     * 获取实名认证是几要素
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/index/getRealNameType)
     * @ApiReturnParams   (name="status", type="string", description="10=二要素  20=二要素  30=四要素")
     */
    public function getRealNameType()
    {
		if(!$this->auth->isLogin()){
			$this->error("请先登录", null, 401);
		}
		$values = Setting::getItem("certification")['status'];
        $this->success("获取成功",['status'=>$values]);
    }


    /**
     * 获取按钮状态(10显示20隐藏)
     * @ApiAuthor 2023/4/13-liyusheng
     * @ApiRoute  (/api/index/getBtnStatus)
     * @ApiReturnParams (name="shop_status", type="integer", required=true, description="商城订单")
     * @ApiReturnParams (name="synthesis_status", type="integer", required=true, description="合成藏品")
     * @ApiReturnParams (name="exchange_status", type="integer", required=true, description="藏品兑换")
     * @ApiReturnParams (name="log_status", type="integer", required=true, description="藏品记录")
     * @ApiReturnParams (name="ranking_status", type="integer", required=true, description="排行榜")
     * @ApiReturnParams (name="team_status", type="integer", required=true, description="我的团队")
     * @ApiReturnParams (name="sign_status", type="integer", required=true, description="每日签到")
     * @ApiReturnParams (name="invitation_status", type="integer", required=true, description="邀请有礼")
     * @ApiReturnParams (name="pwd_status", type="integer", required=true, description="操作密码")
     * @ApiReturnParams (name="we_status", type="integer", required=true, description="关于我们")
     * @ApiReturnParams (name="conduct_status", type="integer", required=true, description="正在挂售")
     * @ApiReturnParams (name="sold_status", type="integer", required=true, description="已售出")
     * @ApiReturnParams (name="shen_status", type="integer", required=true, description="申购订单")
     * @ApiReturnParams (name="invitation_ranking_status", type="integer", required=true, description="邀请排行")
     * @ApiReturnParams (name="consumption_ranking_status", type="integer", required=true, description="消费排行")
     * @ApiReturnParams (name="warehouse_ranking_status", type="integer", required=true, description="持仓排行")
     */
    public function getBtnStatus()
    {
        $this->success(SettingModel::getItem('my_personal'));
    }


	/**
	 * 获取金刚区导航图标
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/index/getIconList)
	 * @ApiReturnParams   (name="title", type="string", description="标题")
	 * @ApiReturnParams   (name="icon_image", type="string", description="图片")
	 * @ApiReturnParams   (name="link_url", type="string", description="链接地址,前台根据是否含有http或者httPS 判断是内链还是外链")
	 */
	public function getIconList()
	{
		$this->success("获取成功",(new Navigation())->field("title,icon_image,link_url")->where(['disabled'=>10])->select());
	}

    /**
     * 获取底部导航列表
     * @ApiAuthor 2023/4/21-liyusheng
     * @ApiRoute  (/api/index/getBottomNavList)
     * @ApiReturnParams (name="code", type="integer", required=true, description="0")
     */
    public function getBottomNavList()
    {
        $this->success('获取成功',Nav::where(['status'=>10])->field('id,name,url,icon,selected_icon')->select());
    }


	/**
	 * 获取浏览器icon 小图标
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/index/getIcon)
	 * @ApiReturnParams   (name="ico_images", type="string", description="浏览器小图标")
	 */
	public function getIcon()
	{
		$this->success("获取成功",['ico_images'=>Setting::getItem("store")['ico_images']]);
	}

}