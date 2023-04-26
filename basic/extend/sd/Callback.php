<?php

namespace sd;

use app\common\model\Member;
use app\common\model\Order;
use sd\tools\Logs;
use sd\tools\Sign;

/**
 * 杉德支付回调
 */
class Callback extends SdPay
{
	/**
	 * 云账户开户异步
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 */
	public function cloudAcctmgrCallback($callback)
	{
		Logs::write_log(['cloudAcctmgrCallback'=>$callback], "sdPay");
		$paramDecode = urldecode($callback);
		$param_array = explode('&', $paramDecode);
		//处理字符串  获取DATA
		$param_array_data_json = substr($param_array[2], 5);
		$param_array_data_array = json_decode($param_array_data_json, true);
		if ($param_array_data_array['respCode'] == '00000') {
			die(json_encode(['respCode'=>'000000']));
		}
		die(json_encode(['respCode'=>'000000']));
	}

	/**
	 * 杉德快捷支付回调通知
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 */
	public function quickPaymentCallback($callback)
	{
		Logs::write_log(['quickPaymentCallback'=>$callback], "sdPay");
		$paramDecode = urldecode($callback);
		$param_array = explode('&', $paramDecode);
		$param_array_data_json = substr($param_array[2], 5);
		$param_array_data_array = json_decode($param_array_data_json, true);
		$param_array_sign = substr($param_array[3], 5);
		if ($param_array_data_array['head']['respCode'] == '000000') {
			if (Sign::verify($param_array_data_json, $param_array_sign, $this->config['publicKey_path'])) {
				$Order_info = (new Order())->where(['order_no' => $param_array_data_array['body']['tradeNo']])->find();
				if (empty($Order_info)) {
					die(json_encode(['respCode'=>'000000']));
				}
				if ($Order_info['pay_status']['value'] == 2) {
					die(json_encode(['respCode'=>'000000']));
				}
				$arr = [
					'transaction_id' => $param_array_data_array['body']['tradeNo'],
					'out_trade_no' => $param_array_data_array['body']['orderCode'],
				];
			
				if((new \app\notice\model\Order())->callBack($arr)){
					die(json_encode(['respCode'=>'000000']));
				}
			}else{
				echo "验签失败";
			}
		}else{
			echo "验签失败";
		}
	}


	/**
	 * 开户费支付回调
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 */
	public function accountPaymentCallback($callback)
	{
		$json_string = $callback;
		Logs::write_log(['accountPaymentCallback'=>$callback], "sdPay");
		$json_array = json_decode($json_string['data'],true);
		if(isset($json_array['head']) && $json_array['head']['respCode']=='000000'){
			//验签成功 支付成功
			if(Sign::verify($json_string['data'], $json_string['sign'],$this->config['publicKey_path'])){
				$member_id =  substr($json_array['body']['tradeNo'],strripos($json_array['body']['tradeNo'],"_")+1);
				//变更会员的缴纳开户费的状态
				$memberModel = new Member();
				$member_info = $memberModel->where(['member_id'=>$member_id])->find();
				if($member_id && $member_info){
					$memberModel->where(['member_id'=>$member_id])->update(['sd_open_free'=>20]);
				}
				return json_encode(['respCode'=>'000000']);
			}
		}
	}

	/**
	 * 钱包支付一级市场回调
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  ()
	 * @ApiParams   (name="name", type="string", required=true, description="用户名")
	 * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
	 */
	public function cloudPaymentC2bCallback($callback)
	{
		Logs::write_log(['cloudPaymentC2bCallback'=>$callback], "sdPay");
		$paramDecode = urldecode($callback);
		$param_array = explode('&', $paramDecode);
		$param_array_data_json = substr($param_array[2], 5);
		$param_array_data_array = json_decode($param_array_data_json, true);
		$param_array_sign = substr($param_array[3], 5);
		if ($param_array_data_array['head']['respCode'] == '000000') {
			if (Sign::verify($param_array_data_json, $param_array_sign, $this->config['publicKey_path'])) {
				$Order_info = (new Order())->where(['order_no' => $param_array_data_array['body']['tradeNo']])->find();
				if (empty($Order_info)) {
					die(json_encode(['respCode' => '000000']));
				}
				if ($Order_info['pay_status']['value'] == 2) {
					die(json_encode(['respCode' => '000000']));
				}
				$arr = [
					'transaction_id' => $param_array_data_array['body']['tradeNo'],
					'out_trade_no' => $param_array_data_array['body']['orderCode'],
				];
				if ((new \app\notice\model\Order())->callBack($arr)) {
					die(json_encode(['respCode' => '000000']));
				}
				die(json_encode(['respCode' => '000000']));
			}
		}
	}

	/**
	 * 钱包支付二级市场回调
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  ()
	 * @ApiParams   (name="name", type="string", required=true, description="用户名")
	 * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
	 */
	public function cloudPaymentC2cCallback($callback)
	{
		Logs::write_log(['cloudPaymentC2cCallback'=>$callback], "sdPay");
		$paramDecode = urldecode($callback);
		$param_array = explode('&', $paramDecode);
		$param_array_data_json = substr($param_array[2], 5);
		$param_array_data_array = json_decode($param_array_data_json, true);
		$param_array_sign = substr($param_array[3], 5);
		if ($param_array_data_array['respCode'] == '00000') {
			if (Sign::verify($param_array_data_json, $param_array_sign, $this->config['publicKeyPro_path'])) {
				$Order_info = (new Order())->where(['order_no' => $param_array_data_array['orderNo']])->find();
				if (empty($Order_info)) {
					die(json_encode(['respCode' => '000000']));
				}
				if ($Order_info['pay_status']['value'] == 2) {
					die(json_encode(['respCode' => '000000']));
				}
				$arr = [
					'transaction_id' => $param_array_data_array['sandSerialNo'],
					'out_trade_no' => $param_array_data_array['orderNo'],
				];

				if ((new \app\notice\model\Order())->callBack($arr)) {
					die(json_encode(['respCode' => '000000']));
				}
				die(json_encode(['respCode' => '000000']));
			}
		}
	}
}