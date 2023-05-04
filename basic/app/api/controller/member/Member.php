<?php
/**
 * +----------------------------------------------------------------------
 * | 河南八六互联信息技术有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 河南八六互联信息技术有限公司 http://www.86itn.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: Mr.zhang
 * +----------------------------------------------------------------------
 * | Time:  2021/4/12   5:47 下午
 * +----------------------------------------------------------------------
 * | Email: 18519179004@163.com
 * +----------------------------------------------------------------------
 * | className: 用户逻辑操作API
 * +----------------------------------------------------------------------
 */

namespace app\api\controller\member;

use app\admin\controller\Common;
use app\admin\model\Setting as SettingModel;
use app\api\controller\Sms;
use app\api\model\task\AwardSetting;
use app\api\validate\UserValidate;
use app\common\components\helpers\RedisUtils;
use app\common\controller\Task;
use app\common\model\Appointment;
use app\common\model\Glory;
use app\common\model\Member as MemberModel;
use app\common\controller\Controller;
use app\common\model\MemberBox;
use app\common\model\MemberChain;
use app\common\model\MemberGoods;
use app\common\model\MemberReal;
use app\common\model\Setting;
use card\CheckCard;
use exception\BaseException;
use think\Cache;
use think\Env;

/**
 * 会员接口
 */
class Member extends Controller
{
	protected $noNeedLogin = [
		'login','mobilelogin','register','forgotPassword','getIsRealNameAuthentication','realAbc'
	];

	/**
	 * 会员登录
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @param string $phone  账号
	 * @param string $password 密码
	 * @ApiRoute  (/api/member.member/login)
	 * @ApiReturnParams   (name="token", type="string", required=true, description="登录凭证")
	 * @ApiReturnParams   (name="expiretime", type="string", required=true, description="过期时间")
	 */
	public function login()
	{
		$phone = $this->request->post('phone');
		$password = $this->request->post('password');
		if (!$phone || !$password) {
			$this->error('请输入账号密码');
		}
		if(!checkMobile($phone)){
			$this->error('手机号格式错误');
		}
		$ret = $this->auth->login($phone, $password);
		($ret === false) && $this->error($this->auth->getError());
		$data = ['userinfo' => $this->auth->getUserinfo()];
		$this->success("登录成功", $data);
	}

	/**
	 * 验证码登录
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @param string $phone  账号
	 * @param string $sms_code 验证码
	 * @ApiRoute  (/api/member.member/mobilelogin)
	 */
	public function mobilelogin()
	{
		$redis = initRedis();
		$phone = $this->request->post('phone');
		$sms_code = $this->request->post('sms_code');
		if (!$phone || !$sms_code) {
			$this->error('请输入手机号或者验证码');
		}
		if(!checkMobile($phone)){
			$this->error('手机号格式错误');
		}
		//验证码效验
		if(Env::get('app_debug') == false){
			$this->checkSmsCode($phone, $sms_code);
		}
		$member = \app\common\model\Member::where(['phone' => $phone, 'is_del' => 1])->find();
		if (!$member) {
			$this->error('用户不存在,请先注册');
		}
		if ($member['status']['value'] == 0) {
			$this->error('用户禁用无法登录');
		}
		$ret = $this->auth->direct($member['member_id']);
		if ($ret) {
			$data = ['userinfo' => $this->auth->getUserinfo()];
			$this->success('登录成功', $data);
		} else {
			$this->error($this->auth->getError());
		}
	}


	/**
	 * 会员注册
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiParams   (name="phone", type="string", required=true, description="手机号")
	 * @ApiParams   (name="sms_code", type="string", required=true, description="验证码")
	 * @ApiParams   (name="password", type="string", required=true, description="密码")
	 * @ApiParams   (name="repeat_password", type="string", required=true, description="重复密码")
	 * @ApiParams   (name="invitation_code", type="string", required=false, description="邀请码")
	 * @ApiRoute  (/api/member.member/register)
	 */
	public function register()
	{
		$password        = $this->request->post('password');
		$repeat_password = $this->request->post('repeat_password');
		$phone          = $this->request->post('phone');
		$sms_code            = $this->request->post('sms_code');
		$invitation_code = $this->request->post('invitation_code');
		if (!$phone || !$sms_code || !$password || !$repeat_password) {
			$this->error('请先完成手机号、验证码、密码、重复密码、邀请码的填写');
		}
		if(!checkMobile($phone)){
			$this->error('手机号格式错误');
		}
		if ($repeat_password !== $password) {
			$this->error("两次密码不一致");
		}
		if(Env::get('app_debug') == false){
			$this->checkSmsCode($phone, $sms_code);
		}
		//判断下这个是否开启强制邀请码注册
		$storeSetting = SettingModel::getItem('store');
		if ($storeSetting['register'] == 10) {
			if (!$invitation_code) {
				$this->error('请先填写邀请码');
			}
			$invite_code = \app\common\model\Member::where('code', $invitation_code)->find();
			if (!$invite_code) {
				$this->error('邀请码错误');
			}
		}
		$ret = $this->auth->register($phone, $password, '', $phone, [], $invitation_code);
		if ($ret) {
			$data = ['userinfo' => $this->auth->getUserinfo()];
			$this->success('注册成功', $data);
		} else {
			$this->error($this->auth->getError());
		}
	}

	/**
	 * 忘记登录密码
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiParams   (name="phone", type="string", required=true, description="手机号")
	 * @ApiParams   (name="sms_code", type="string", required=true, description="验证码")
	 * @ApiParams   (name="password", type="string", required=true, description="密码")
	 * @ApiParams   (name="repeat_password", type="string", required=true, description="重复密码")
	 * @ApiRoute  (/api/member.member/forgotPassword)
	 */
	public function forgotPassword()
	{
		$password        = $this->request->post('password');
		$repeat_password = $this->request->post('repeat_password');
		$phone          = $this->request->post('phone');
		$sms_code            = $this->request->post('sms_code');
		if (!$phone || !$sms_code || !$password || !$repeat_password) {
			$this->error('请先完成手机号、验证码、密码、重复密码的填写');
		}
		if(!checkMobile($phone)){
			$this->error('手机号格式错误');
		}
		if ($repeat_password !== $password) {
			$this->error("两次密码不一致");
		}
		if(Env::get('app_debug') == false){
			$this->checkSmsCode($phone, $sms_code);
		}
		if(!\app\common\model\Member::where('phone', $phone)->find()){
			$this->error("用户不存在");
		}
		$ret = $this->auth->changepwd($password, $phone);
		if ($ret) {
			$this->success('密码修改成功', []);
		} else {
			$this->error($this->auth->getError());
		}
	}


	/**
	 * 修改登录密码
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiParams   (name="sms_code", type="string", required=true, description="验证码")
	 * @ApiParams   (name="password", type="string", required=true, description="密码")
	 * @ApiParams   (name="repeat_password", type="string", required=true, description="重复密码")
	 * @ApiRoute  (/api/member.member/editLoginPwd)
	 */
	public function editLoginPwd()
	{
		$user = $this->auth->getUser();
		$password        = $this->request->post('password');
		$repeat_password = $this->request->post('repeat_password');
		$sms_code            = $this->request->post('sms_code');
		if (!$sms_code || !$password || !$repeat_password) {
			$this->error('请先完成验证码、新密码、重复密码的填写');
		}
		if ($repeat_password !== $password) {
			$this->error("两次密码不一致");
		}
		if(Env::get('app_debug') == false){
			$this->checkSmsCode($user['phone'], $sms_code);
		}
		if(!\app\common\model\Member::where('member_id', $user['member_id'])->find()){
			$this->error("用户不存在");
		}
		$ret = $this->auth->changepwd($password, '','',true);
		if ($ret) {
			$this->success('密码修改成功', []);
		} else {
			$this->error($this->auth->getError());
		}
	}


    /**
     * 设置操作密码
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     * @ApiParams   (name="operation_pwd", type="string", required=true, description="操作密码")
     * @ApiParams   (name="re_operation_pwd", type="string", required=true, description="重复操作密码")
     * @ApiRoute  (/api/member.member/setOperationPwd)
     */
    public function setOperationPwd()
    {
        $operation_pwd        = $this->request->post('operation_pwd');
        $re_operation_pwd = $this->request->post('re_operation_pwd');
        if(strlen($operation_pwd) != 6 || strlen($re_operation_pwd) !=6){
            $this->error('操作密码为6位纯数字');
        }
        if (!$operation_pwd || !$re_operation_pwd) {
            $this->error('操作密码不能为空');
        }
        if ($operation_pwd !== $re_operation_pwd) {
            $this->error("两次密码不一致");
        }
        $member_data = MemberModel::where('member_id', $this->auth->member_id)->find();
        if (!$member_data) {
            $this->error("当前用户不存在");
        }
        if($member_data['operation_pwd']){
            $this->error("操作密码已经设置过了");
        }
        $member_salt   = self::getMemberSalt((int)$this->auth->member_id);
        $operation_pwd = self::encryptPassword($operation_pwd, $member_salt);
        //重新设定操作密码
        $member_data->operation_pwd = $operation_pwd;
        if ($member_data->save()) {
            $this->success("设置成功");
        }
        $this->error("设置失败");
    }


	/**
	 * 修改操作密码
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiParams   (name="phone", type="string", required=true, description="手机号默认当前用户手机号")
	 * @ApiParams   (name="sms_code", type="string", required=true, description="验证码")
	 * @ApiParams   (name="operation_pwd", type="string", required=true, description="操作密码")
	 * @ApiParams   (name="re_operation_pwd", type="string", required=true, description="重复操作密码")
	 * @ApiRoute  (/api/member.member/editOperationPwd)
	 */
	public function editOperationPwd()
	{
		$phone          = $this->request->post('phone');
		$sms_code            = $this->request->post('sms_code');
		$operation_pwd        = $this->request->post('operation_pwd');
		$re_operation_pwd = $this->request->post('re_operation_pwd');
		if(!$phone || !$sms_code || !$operation_pwd || !$re_operation_pwd){
			$this->error('请先完成手机号、验证码、操作密码、重复操作密码的填写');
		}
		if(strlen($operation_pwd) != 6 || strlen($re_operation_pwd) !=6){
			$this->error('操作密码为6位纯数字');
		}
		if ($operation_pwd !== $re_operation_pwd) {
			$this->error("两次密码不一致");
		}
		if($phone != $this->auth->getUser()['phone']){
			$this->error("手机号不正确");
		}
		if(Env::get('app_debug') == false){
			$this->checkSmsCode($phone, $sms_code);
		}
		$member_data = MemberModel::where('member_id', $this->auth->member_id)->find();
		if (!$member_data) {
			$this->error("当前用户不存在");
		}
		$member_salt   = self::getMemberSalt((int)$this->auth->member_id);
		$operation_pwd = self::encryptPassword($operation_pwd, $member_salt);
		$member_data->operation_pwd = $operation_pwd;
		if ($member_data->save()) {
			$this->success("修改成功");
		}
		$this->error("修改失败");
	}

	/**
	 * 加密密码
	 * @param string $password
	 * @param string $salt
	 * @return string
	 */
	public static function encryptPassword(string $password, string $salt = ''): string
	{
		return md5(md5($password . $salt) . base64_encode($salt));
	}

	/**
	 * 获取会员salt
	 * @param int $member_id
	 * @return string
	 */
	public static function getMemberSalt(int $member_id): string
	{
		return substr(md5(md5($member_id) . base64_encode($member_id)), 0, 8);
	}


	/**
	 * 修改会员个人信息
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiParams   (name="avatarUrl", type="string", required=true, description="头像链接")
	 * @ApiParams   (name="name", type="string", required=true, description="昵称")
	 * @ApiRoute  (/api/member.member/profile)
	 */
	public function profile()
	{
		$user     = $this->auth->getUser();
		$name          = $this->request->post('name');
		$avatar   = $this->request->post('avatarUrl', '', 'trim,strip_tags,htmlspecialchars');
		if($name){
			$user->name = $name;
		}
		if($avatar){
			$user->avatarUrl = $avatar;
		}
		if($avatar || $name){
			$user->save();
			$this->success('修改成功');
		}
		$this->error('请先填写昵称或者头像');
	}


	/**
	 * 会员注销
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiParams   (name="sms_code", type="string", required=true, description="验证码")
	 * @ApiRoute  (/api/member.member/cancellation)
	 */
	public function cancellation()
	{
		$user = $this->auth->getUser();
		$sms_code          = $this->request->post('sms_code');
		if(!$sms_code){
			$this->error('请先填写验证码');
		}
		if(Env::get('app_debug') == false){
			$this->checkSmsCode($user['phone'], $sms_code);
		}
		if ($user['real_status']['value'] == 2) {
			(new MemberReal())->where(['member_id' => $user['member_id']])->delete();
		}
		$user->is_del = 0;
		$user->save();
		$this->success('注销成功');
	}


	/**
	 * 修改手机号验证验证码是否正确
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiParams   (name="sms_code", type="string", required=true, description="验证码")
	 * @ApiRoute  (/api/member.member/replaceVerifyPhone)
	 */
	public function replaceVerifyPhone()
	{
		$user = $this->auth->getUser();
		$sms_code          = $this->request->post('sms_code');
		if(!$sms_code){
			$this->error('请先填写验证码');
		}
		if(Env::get('app_debug') == false){
			$this->checkSmsCode($user['phone'], $sms_code);
		}
		Cache::set($user['phone'], 1, 600);
		$this->success('验证成功');
	}

	/**
	 * 修改手机号
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiParams   (name="phone", type="string", required=true, description="修改的新手机号")
	 * @ApiParams   (name="sms_code", type="string", required=true, description="验证码")
	 * @ApiRoute  (/api/member.member/replacePhone)
	 */
	public function replacePhone()
	{
		$user = $this->auth->getUser();
		$sms_code          = $this->request->post('sms_code');
		$phone          = $this->request->post('phone');
		if(!$sms_code || !$phone){
			$this->error('请先填写验证码或者手机号');
		}
		if(!checkMobile($phone)){
			$this->error('请填写正确的手机号');
		}
		if($phone == $user['phone']){
			$this->error('新手机不能与老手机号相同');
		}
		if(Env::get('app_debug') == false){
			$this->checkSmsCode($phone, $sms_code);
		}
		if(!Cache::get($user['phone'])){
			$this->error('请先完成老手机号码验证');
		}
		MemberModel::get(['phone'=>$phone]) && $this->error('该手机号已被注册');
		$user->phone = $phone;
		$user->save();
		$this->auth->logout();
		Cache::clear($user['phone']);
		return $this->success('修改成功');
	}

    /**
     * 实名认证纠错
     * @ApiAuthor [Mr.Wei]
     * @ApiMethod (POST)
     * @ApiRoute  (/api/member.member/realAbc)
     */
    public function realAbc(){
        $member_list = (new MemberReal())->select();
        $card = new CheckCard();
        foreach ($member_list as $k => $v){
            $param['card'] = '15212220000223122X';
            $param['phone'] = '15049500117';
            $param['name'] = '赵蒙';
//            $param['card'] = $v->card;
//            $param['phone'] = $v->phone;
//            $param['name'] = $v->name;
            $result = $card->threeElementsDemo($param);
            echo "<pre/>";
            print_r($result);die;
            if($result == 1){
                $v->result_status = 1;
                $v->result_msg = '成功';
            }else{
                $v->result_status = 0;
                $v->result_msg = $result;
            }
            $v->save();
        }
        echo "<pre/>";
        print_r('完成2');die;
    }

	/**
	 * 实名认证
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiParams   (name="phone", type="string", required=true, description="手机号")
	 * @ApiParams   (name="code", type="string", required=true, description="验证码")
	 * @ApiParams   (name="name", type="string", required=true, description="真实姓名")
	 * @ApiParams   (name="card", type="string", required=true, description="身份证号码")
	 * @ApiParams   (name="acc_no", type="string", description="银行卡号")
	 * @ApiRoute  (/api/member.member/realNameAuthentication)
	 */
	public function realNameAuthentication()
	{
		$redis = initRedis();
		$param                = $this->request->param();
		$member_info            = $this->auth->getUser();
		$certification_status = SettingModel::getItem('certification')['status'];
		$lockKey = "checkCardinfo:member_id:{$member_info['member_id']}";
		//判断是否开启实名认证 选择合适的实名认证 进去效验
		(new UserValidate())->goCheck('card');
		if($member_info['real_status']['value'] == 2){
			$this->error('已经实名认证过了');
		}
		
		$length = strlen($param['card']);
        if($id_num = '' || !in_array($length,[15,18])){
            $this->error('身份证输入错误');
        }
        $rule15 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/";
        $rule18 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/";
        // if($length === 15){
        //     $match_result = preg_match($rule15,$id_num);
        //     if(!$match_result){
        //         $this->error('身份证输入错误');
        //     }
        // }
        // if($length === 18){
        //     $match_result = preg_match($rule18,$id_num);
        //     if(!$match_result){
        //         $this->error('身份证输入错误');
        //     }
        // }
		
//		判断输入的身份证号码是否已经使用过
		$MemberCard = MemberReal::getDetails(['card'=>$param['card']]);
		if (!empty($MemberCard)) {
			//判断这个会员账号是否存在 如果不存在 则修改下信息  兼容原始版本的错误
			$member_info_model = (new MemberModel())->where(['member_id' => $MemberCard['member_id'], 'is_del' => 1])->find();
			if (!empty($member_info_model)) {
				$this->error('身份证号码已被使用');
			}
		}
		if(Env::get('app_debug') == false){
			$this->checkSmsCode($member_info['phone'],$param['code']);
		}
		if (RedisUtils::lock($lockKey, 60)) {
			$this->error('提交频繁,请一分钟后重试');
		}
		$real_status = 1;
		$card = new CheckCard();
		$result = false;
		switch ($certification_status)
		{
			case "10": //开启人工审核
				//判断是否提交过实名认证 提交过则判断
				if($member_info['real_status']['value'] == 1){
					$this->error('实名认证审核中,请不要重复提交认证！！！');
				}
				$result = (new MemberReal())->humanReviewReal(array_merge($param,[
					'member_id' => $member_info['member_id'],
					'real_status' => $member_info['real_status']
				]));
				break;
			case "20": //二要素
				$result = $card->Txcheck($param['name'], $param['card']);
				$real_status = 2;
				break;
			case "40": //三要素
				$result = $card->threeElements($param);
				$real_status = 2;
				break;
			case "30": //四要素
				$result = $card->fourElements($param);
				$real_status = 2;
				break;
		}
		if(!$result){
			RedisUtils::unlock($lockKey);
			$this->error($card->getError());
		}
		try {
			(new MemberReal())->insertData(array_merge($param,[
				'member_id' => $member_info['member_id'],
			]));
			MemberModel::update(['real_status' => $real_status], ['member_id' => $member_info['member_id']]);
		}catch (\Exception $e){
			RedisUtils::unlock($lockKey);
			$this->error($e->getMessage());
		}
		if($real_status===2){
			//判断是否关闭 积分奖励 关闭后则不再奖励积分
			$values = Setting::getItem("collection");
			if($values['repo'] == 10){
				(new AwardSetting())->realtask($member_info['member_id']);
				if ($member_info['p_id'] != 0) {
					(new AwardSetting())->getTaskList($member_info['p_id']);
				}
			}
			$value = Setting::getItem("todaytask");
			if ($member_info['p_id'] != 0 && isset($value['condition_task']) && $value['condition_task']==2) {
				(new MemberModel())->where(['member_id'=>$member_info['p_id']])->setInc("invitations_number",1);
			}
			(new Task())->createAccount($member_info['member_id']);
            Common::givePrizeCount($member_info['member_id']);

            if($member_info['p_id'] != 0){
                (new MemberModel())->where(['member_id'=>$member_info['p_id']])->setInc("invitations_num",1);
                $invitations_num = (new MemberModel())->where(['member_id'=>$member_info['p_id']])->value('invitations_num');
                if($invitations_num == 3){
                    //增加2个限购次数
                    (new MemberModel())->where(['member_id'=>$member_info['p_id']])->setInc('purchase_limit',2);
                }elseif ($invitations_num == 5){
                    //增加3个限购次数
                    (new MemberModel())->where(['member_id'=>$member_info['p_id']])->setInc('purchase_limit',3);
                }elseif ($invitations_num == 10){
                    //增加5个限购次数
                    (new MemberModel())->where(['member_id'=>$member_info['p_id']])->setInc('purchase_limit',5);
                }elseif ($invitations_num == 30){
                    //增加20个限购次数
                    (new MemberModel())->where(['member_id'=>$member_info['p_id']])->setInc('purchase_limit',20);
                }elseif ($invitations_num == 50){
                    //增加30个限购次数
                    (new MemberModel())->where(['member_id'=>$member_info['p_id']])->setInc('purchase_limit',30);
                }elseif ($invitations_num == 100){
                    //增加50个限购次数
                    (new MemberModel())->where(['member_id'=>$member_info['p_id']])->setInc('purchase_limit',50);
                }
            }





			$this->success('实名成功');
		}else{
			$this->success('提交成功~等待平台审核！');
		}
	}

	/**
	 * 获取个人中心信息
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/member.member/getUserDetails)
	 * @ApiReturnParams   (name="name", type="string", required=true, description="用户昵称")
	 * @ApiReturnParams   (name="phone", type="string", required=true, description="手机号")
	 * @ApiReturnParams   (name="re_phone", type="string", required=true, description="星号替换的手机号")
	 * @ApiReturnParams   (name="avatarUrl", type="string", required=true, description="头像")
	 * @ApiReturnParams   (name="account_address", type="string", required=true, description="区块链地址")
	 * @ApiReturnParams   (name="glory", type="string", required=true, description="积分")
	 * @ApiReturnParams   (name="volume_drop", type="string", required=true, description="空投卷")
	 * @ApiReturnParams   (name="account", type="string", required=true, description="账户余额")
	 * @ApiReturnParams   (name="goods_count", type="string", required=true, description="藏品数量")
	 * @ApiReturnParams   (name="invite_name", type="string", required=true, description="邀请人昵称")
	 */
	public function getUserDetails()
	{
		$user = $this->auth->getUser();
		$MemberChain                    = (new MemberChain())->where(['member_id' => $user['member_id']])->find();
		$account_address = '';
		if (!empty($MemberChain)) {
			$info = SettingModel::getItem('blockchain');
			if ($info['default'] == 'BD') {
				$account_address = $MemberChain['b_address'];
			} else if ($info['default'] == 'WC') {
				$account_address = $MemberChain['w_account'];
			} else if ($info['default'] == 'TH') {
				$account_address = $MemberChain['t_address'];
			} else {
				if (empty($MemberChain['w_account'])) {
					$account_address = $MemberChain['w_account'];
				} else {
					$account_address = $MemberChain['s_address'];
				}
			}
		}
		$box_count = (new MemberBox())->where(['member_id'=>$user['member_id'],'is_open'=>10,'box_status'=>['<>',40]])->count();
		$member_goods_count = (new MemberGoods())->where(['member_id'=>$user['member_id'],'goods_status'=>['in',['0','1','2']],'is_synthesis'=>0,'is_donation'=>0])->count();
		$invite_name = "暂无";
		if ($user['p_id']) {
			$member_info = MemberModel::detail(['member_id'=>$user['p_id']]);
			$invite_name = $member_info['name'];
		}
		$member_real_info = (new MemberReal())->where(['member_id'=>$user['member_id']])->find();
		$real_name = "暂无实名信息";
		if (!empty($member_real_info)){
			$real_name = $member_real_info['name'];
		}

		$this->success('ok',[
			'name' => $user['name'],
			'phone' => $user['phone'],
			're_phone' => phone_substr_replace($user['phone']),
			'avatarUrl' => $user['avatarUrl'],
			'account_address' => $account_address,
			'glory'          => intval($user['glory']),
			'volume_drop' => $user['volume_drop'],
			'account' => $user['account'],
			'goods_count' =>binaryCalculator($box_count,'+',$member_goods_count,0),
			'invite_name' => $invite_name,
			'real_name' => $real_name,
		]);
	}

	/**
	 * 获取是否实名认证
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/member.member/getIsRealNameAuthentication)
	 * @ApiReturnParams   (name="data", type="string", required=true, description="0==未实名认证1==实名审核中 2==已实名")
	 */
	public function getIsRealNameAuthentication()
	{
		$status = 0;
		if($this->auth->isLogin()){
			$user = $this->auth->getUser();
			$status = $user['real_status']['value'];
		}
		$this->success('获取数据成功',$status);
	}

	/**
	 * 是否设置操作密码
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/member.member/getIsOperatePwd)
	 * @ApiReturnParams   (name="data", type="string", required=true, description="true=已设置  false=未设置")
	 */
	public function getIsOperatePwd()
	{
		$operation_pwd = $this->auth->operation_pwd;

		$this->error('获取数据成功',$operation_pwd?true:false);
	}

	/**
	 * 获取个人中心挂售/申购订单数量角标
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/member.member/getOrderNumber)
	 * @ApiReturnParams   (name="sale_number", type="string", description="挂售中的数量")
	 * @ApiReturnParams   (name="sold_number", type="string", description="已卖出的数量")
	 * @ApiReturnParams   (name="purchase_order", type="string", description="申购的订单数量")
	 */
	public function getOrderNumber()
	{
		$user = $this->auth->getUser();
		$memberGoods = new MemberGoods();
		$memberBox = new MemberBox();
		//挂售中
		$sale_number = $memberGoods->where(['member_id'=>$user['member_id'],'goods_status'=>1,'is_synthesis'=>0,'is_donation'=>0])->count();
		$sale_number +=$memberBox->where(['member_id'=>$user['member_id'],'box_status'=>20,'is_open'=>10])->count();
		//已卖出
		$sold_number = $memberGoods->where(['member_id'=>$user['member_id'],'goods_status'=>3,'is_synthesis'=>0,'is_donation'=>0])->count();
		$sold_number +=$memberBox->where(['member_id'=>$user['member_id'],'box_status'=>40,'is_open'=>10])->count();

		//申购中
		$purchase_order = (new Appointment())->where(['member_id'=>$user['member_id'],'status'=>['in',['0','1','2']]])->count();
		$this->success('ok',[
			'sale_number' => $sale_number,
			'sold_number' => $sold_number,
			'purchase_order' => $purchase_order
		]);
	}


	/**
	 * 积分记录
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/member.member/getScoreList)
	 * @ApiParams   (name="page", type="string", required=true, description="分页")
	 */
	public function getScoreList()
	{
		$memberInfo     = $this->auth->getUser();
		$params         = $this->request->param();
		$glory          = new Glory();
		$list           = $glory->gloryRecode($memberInfo);
		$params['page'] = $params['page'] ?? 1;
		$page           = intval($params['page']);
		$listRows       = $params['listRows'] ?? 10;
		$total          = count($list, 0);
		$offset         = ($params['page'] - 1) * $listRows;
		$data           = array_slice($list, $offset, $listRows);
		$total_page     = ceil($total / $listRows);
		$this->success('ok',compact('total', 'page', 'listRows', 'total_page', 'data'));
	}


	/**
	 * 推广海报
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  (/api/member.member/getPoster)
	 * @ApiReturnParams   (name="invitation", type="string", required=true, description="邀请码")
	 * @ApiReturnParams   (name="code", type="string", required=true, description="邀请码")
	 * @ApiReturnParams   (name="avatarUrl", type="string", required=true, description="头像")
	 */
	public function getPoster()
	{
		$values = SettingModel::getItem("store");
		$invitation = $values['invitation'];
		$system_name = $values['system_name'];
		$code = $this->auth->code;
		$avatarUrl = $this->auth->avatarUrl;
		$code_url = HOST."/h5/h5.html#/pages/main/personal?member_id=".$this->auth->code;
		$this->success("获取成功",compact('invitation','code','avatarUrl','code_url','system_name'));
	}

	/**
	 * 短信的效验
	 * @param $phone
	 * @param $code
	 * @Time: 2023/2/22   18:52
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface checkSmsCode
	 */
	private function checkSmsCode($phone,$code)
	{
		$redis = initRedis();
		$chche_code = $redis->get($phone . 'code');
		if (empty($chche_code)) {
			$this->error('验证码过期');
		}
		if ($chche_code != $code) {
			$this->error('验证码错误');
		}
	}
}
