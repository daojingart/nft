<?php

namespace sd;

use sd\tools\Http;
use sd\tools\Sign;
use think\Config;

/**
 * 云账户
 */
class Acctmgr extends SdPay
{
    /**
     * 杉德云钱包开户
     * @ApiAuthor [Mr.Zhang]
     */
    public function openAccount($params)
    {
        $data = [
            'version' => 10,
            'mer_no' =>  $this->config['merchant_id'],
            'mer_order_no' => $this->orderNo(), //商户唯一订单号
            'create_time' => date('YmdHis'),
            'expire_time' => date('YmdHis', time()+30*60),
            'order_amt' => '0.3', //订单支付金额
            'notify_url' => Config::get('payConfig')['sdPay']['openacct_notify_url'],  // 异步通知地址
            'return_url' => Config::get('payConfig')['sdPay']['return_url'], //订单前端页面跳转地址
            'create_ip' => str_replace(".","_",\request()->ip()),
            'goods_name' => '开户',
            'store_id' => '000000',
            'product_code' => '00000001',
            'clear_cycle' => '3',
            //pay_extra参考语雀文档4.3
            'pay_extra' => json_encode(["userId"=> $this->prefix.$params['member_id'] ,"nickName"=>$params['name']],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
            'accsplit_flag' => 'NO',
            'jump_scheme' => 'sandcash://scpay',
            'meta_option' => json_encode([["s" => "Android","n" => "wxDemo","id" => "com.pay.paytypetest","sc" => "com.pay.paytypetest"]]),
            'sign_type' => 'RSA'
        ];
        $temp = $data;
        unset($temp['goods_name']);
        unset($temp['jump_scheme']);
        unset($temp['expire_time']);
        unset($temp['product_code']);
        unset($temp['clear_cycle']);
        unset($temp['meta_option']);
        $str = Sign::getSignContent($temp);
        $sign = Sign::createSign($str,$this->config['privatePfx_path'],$this->config['privatePfxPwd']);
        $data['sign'] = $sign;
        $query = http_build_query($data);
        $request_url = $this->baseUrl.$query;
        return [
            'jump_url' => $request_url
        ];

    }


    /**
     * 账户侧账户状态查询
     * @ApiAuthor [Mr.Zhang]
     * @ApiMethod (POST)
     */
    public function getMemberStatusQuery($member_ids)
    {
        $header_public = [
            'mid' => $this->config['merchant_id'],
            'timestamp' => $this->getTimestamp(),
            'version' => '1.0',
            'signType' => 'SHA1WithRSA',
            'customerOrderNo' => 'acctmgr'.mt_rand(111111111100000,999999999999999),
            'encryptType' => 'AES',
        ];
        $data = array_merge($header_public,[
            'bizUserNo' => $this->prefix.$member_ids
        ]);
        $AESKey = Sign::aesGenerate(16);
        $data['data'] = Sign::AESEncrypt($data, $AESKey);
        $data['encryptKey'] = Sign::RSAEncryptByPub($AESKey, $this->config['publicKeyPro_path']);
        $data['sign'] = Sign::sign($data['data'], Sign::loadPk12Cert($this->config['privatePfx_path'],$this->config['privatePfxPwd']));
        $url = "https://cap.sandpay.com.cn/v4/elecaccount/ceas.elec.member.status.query";
        $result = Http::http_post_json($url, $data);
        $arr= json_decode($result,true);
        if(isset($arr['response'])){
            $verify = Sign::verify($arr['data'], $arr['sign'], $this->config['publicKeyPro_path']);
            // step9: 使用私钥解密AESKey
            $decryptAESKey = Sign::RSADecryptByPri($arr['encryptKey'], Sign::loadPk12Cert($this->config['privatePfx_path'],$this->config['privatePfxPwd']));
            // step10: 使用解密后的AESKey解密报文
            $decryptPlainText = Sign::AESDecrypt($arr['data'], $decryptAESKey);
            $decryptPlainText = json_decode($decryptPlainText,true);
            if(isset($decryptPlainText['memberStatus']) && $decryptPlainText['memberStatus']=='00'){
                return true;
            }
            return false;
        }
        return false;
    }

	/**
	 * 可以通过该接口进行会员销户、冻结、解冻操作。
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 */
	public function setAccountStatus($member_ids)
	{
		$header_public = [
			'mid' => $this->config['merchant_id'],
			'timestamp' => $this->getTimestamp(),
			'version' => '1.0',
			'signType' => 'SHA1WithRSA',
			'customerOrderNo' => 'acctmgr'.mt_rand(111111111100000,999999999999999),
			'encryptType' => 'AES',
			'notifyUrl' => '',
		];
		$data = array_merge($header_public,[
			'bizUserNo' => $this->prefix.$member_ids,
			'bizType' => 'FREEZE'
		]);
		$AESKey = Sign::aesGenerate(16);
		$data['data'] = Sign::AESEncrypt($data, $AESKey);
		$data['encryptKey'] = Sign::RSAEncryptByPub($AESKey, $this->config['publicKeyPro_path']);
		$data['sign'] = Sign::sign($data['data'], Sign::loadPk12Cert($this->config['privatePfx_path'],$this->config['privatePfxPwd']));
		$url = "https://cap.sandpay.com.cn/v4/elecaccount/ceas.elec.account.member.status.modify";
		$result = Http::http_post_json($url, $data);
		$arr= json_decode($result,true);
		if(isset($arr['response'])){
			$verify = Sign::verify($arr['data'], $arr['sign'], $this->config['publicKeyPro_path']);
			// step9: 使用私钥解密AESKey
			$decryptAESKey = Sign::RSADecryptByPri($arr['encryptKey'], Sign::loadPk12Cert($this->config['privatePfx_path'],$this->config['privatePfxPwd']));
			// step10: 使用解密后的AESKey解密报文
			$decryptPlainText = Sign::AESDecrypt($arr['data'], $decryptAESKey);
			$decryptPlainText = json_decode($decryptPlainText,true);
			pre($decryptPlainText);
			if(isset($decryptPlainText['memberStatus']) && $decryptPlainText['memberStatus']=='00'){
				return true;
			}
			return false;
		}
		return false;
	}






	/**
	 * 开户费调用支付
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface paymentBalance
	 * @Time: 2022/8/5   17:00
	 */
	public function paymentQuick($order_sn,$total_fee,$member_id,$name,$card)
	{
		//获取订单过期时间
		$data = [
			'version' => 10,
			'mer_no' =>  $this->config['merchant_id'],
			'mer_order_no' => $order_sn, //商户唯一订单号
			'create_time' => $this->getTimestamp(),
			'expire_time' => date('YmdHis', time() +10 * 60),
			'order_amt' => $total_fee, //订单支付金额
			'notify_url' => Config::get('payConfig')['sdPay']['account_notify_url'],  // 异步通知地址
			'return_url' => Config::get('payConfig')['sdPay']['return_url'], //订单前端页面跳转地址
			'create_ip' => str_replace(".","_",\request()->ip()),
			'goods_name' => "账户开户费",
			'store_id' => '000000',
			'product_code' => '06030003',
			'clear_cycle' => '3',
			//pay_extra参考语雀文档4.3
			'accsplit_flag' => 'NO',
			'jump_scheme' => 'sandcash://scpay',
			'meta_option' => json_encode([["s" => "Android","n" => "wxDemo","id" => "com.pay.paytypetest","sc" => "com.pay.paytypetest"]]),
			'sign_type' => 'RSA',
			'pay_extra' => json_encode([
				'userId' => $this->prefix.$member_id,
				'userName' => $name,
				'idCard' => $card,
			]),
		];
		$temp = $data;
		unset($temp['goods_name']);
		unset($temp['jump_scheme']);
		unset($temp['expire_time']);
		unset($temp['product_code']);
		unset($temp['clear_cycle']);
		unset($temp['meta_option']);
		$str = Sign::getSignContent($temp);
		$sign = Sign::createSign($str,$this->config['privatePfx_path'],$this->config['privatePfxPwd']);
		$data['sign'] = $sign;
		$query = http_build_query($data);
		$request_url = $this->quicktopupUrl.$query;
		return [
			'jump_url' => $request_url
		];
	}


}