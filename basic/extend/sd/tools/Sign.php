<?php

namespace sd\tools;

use exception\BaseException;

/**
 * 支付签名类
 */
class Sign
{

	/**
	 * 获取签名的字符串
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 */
	public static function getSignContent($params) {
		ksort($params);
		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === self::checkEmpty($v) && "@" != substr($v, 0, 1)) {

				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . "$v";
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . "$v";
				}
				$i++;
			}
		}

		unset ($k, $v);
		return $stringToBeSigned;
	}


	/**
	 * 检查为空的字段
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 * @ApiRoute  ()
	 * @ApiParams   (name="name", type="string", required=true, description="用户名")
	 * @ApiReturnParams   (name="sale_number", type="string", description="1==实名审核中 2==已实名")
	 */
	public static function checkEmpty($value)
	{
		if (!isset($value))
			return true;
		if ($value === null)
			return true;
		if (trim($value) === "")
			return true;

		return false;
	}

	/**
	 * 云账户的签名
	 * @ApiAuthor [Mr.Zhang]
	 * @ApiMethod (POST)
	 */
	public static function createSign($str,$privateKeyPath_pro,$privateKeyPath_pwd) {
		$file = file_get_contents($privateKeyPath_pro);
		if (!$file) {
			throw new BaseException(['msg' => 'loadPk12Cert::file_get_contents', 'code' => -10]);
		}
		$cert = [];
		if (!openssl_pkcs12_read($file, $cert,$privateKeyPath_pwd)) {
			throw new BaseException(['msg' => 'loadPk12Cert::openssl_pkcs12_read ERROR', 'code' => -10]);
		}
		$pem = $cert['pkey'];
		openssl_sign($str, $sign, $pem);
		$sign = base64_encode($sign);
		return $sign;
	}

	/**
	 * 回到验签
	 * @param $plainText
	 * @param $sign
	 * @throws \Exception
	 * @Time: 2022/8/6   14:21
	 * @author: [Mr.Zhang] [1040657944@qq.com]
	 * @Interface verify
	 */
	public static function verify($plainText, $sign,$publicKey)
	{
		$publicKey = self::publicKey($publicKey);
		$resource = openssl_pkey_get_public($publicKey);
		$result   = openssl_verify($plainText, base64_decode($sign), $resource);
		openssl_free_key($resource);
		return $result;
	}

	/*********************************账户侧开放平台****************************************/
	/**
	 * 生成AESKey
	 * @param $size
	 * @return string
	 */
	public static function aesGenerate($size)
	{
		$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$arr = array();
		for ($i = 0; $i < $size; $i++) {
			$arr[] = $str[mt_rand(0, 61)];
		}

		return implode('', $arr);
	}


	/**
	 * AES加密
	 * @param $plainText
	 * @param $key
	 * @return string
	 * @throws \Exception
	 */
	public static function AESEncrypt($plainText, $key)
	{
		ksort($plainText);
		$plainText = json_encode($plainText);
		$result = openssl_encrypt($plainText, 'AES-128-ECB', $key,OPENSSL_RAW_DATA,$iv);
		//var_dump($iv);
		if (!$result) {
			throw new BaseException(['msg' => '报文加密错误', 'code' => -10]);
		}
		return base64_encode($result);
	}

	/**
	 * 公钥加密AESKey
	 * @param $plainText
	 * @param $puk
	 * @return string
	 * @throws Exception
	 */
	public static function RSAEncryptByPub($plainText, $puk)
	{
		$publicKey = self::publicKey($puk);
		if (!openssl_public_encrypt($plainText, $cipherText, $publicKey, OPENSSL_PKCS1_PADDING)) {
			throw new BaseException(['msg' => 'AESKey 加密错误', 'code' => -10]);
		}
		return base64_encode($cipherText);
	}

	/**
	 * 私钥签名
	 * @param $plainText
	 * @param $path
	 * @return string
	 * @throws Exception
	 */
	public static function sign($plainText, $path)
	{
		try {
			$resource = openssl_pkey_get_private($path);
			$result = openssl_sign($plainText, $sign, $resource);
			openssl_free_key($resource);
			if (!$result) {
				throw new BaseException(['msg' => '签名出错'.$plainText, 'code' => -10]);
			}
			return base64_encode($sign);
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * 获取私钥
	 * @param $path
	 * @param $pwd
	 * @return mixed
	 * @throws Exception
	 */
	public static function loadPk12Cert($privateKeyPath_pro,$privateKeyPath_pwd)
	{
		try {
			$file = file_get_contents($privateKeyPath_pro);
			if (!$file) {
				throw new BaseException(['msg' => "loadPk12Cert::file_get_contents", 'code' => -10]);
			}
			if (!openssl_pkcs12_read($file, $cert, $privateKeyPath_pwd)) {
				throw new BaseException(['msg' => "loadPk12Cert::openssl_pkcs12_read ERROR", 'code' => -10]);
			}
			return $cert['pkey'];
		} catch (\Exception $e) {
			throw $e;
		}
	}


	/**
	 * 私钥解密AESKey
	 * @param $cipherText
	 * @param $prk
	 * @return string
	 * @throws Exception
	 */
	public static function RSADecryptByPri($cipherText, $prk)
	{
		if (!openssl_private_decrypt(base64_decode($cipherText), $plainText, $prk, OPENSSL_PKCS1_PADDING)) {
			throw new \Exception('AESKey 解密错误');
		}
		return (string)$plainText;
	}

	/**
	 * AES解密
	 * @param $cipherText
	 * @param $key
	 * @return string
	 * @throws \Exception
	 */
	public static function AESDecrypt($cipherText, $key)
	{
		$result = openssl_decrypt(base64_decode($cipherText), 'AES-128-ECB', $key, 1);
		if (!$result) {
			throw new \Exception('报文解密错误', 2003);
		}

		return $result;
	}


	/**
	 * 获取公钥信息KEY
	 * @param $cipherText
	 * @param $key
	 * @return string
	 * @throws \Exception
	 */
	public static function publicKey($publicKey_path)
	{
		try {
			$file = file_get_contents($publicKey_path);
			if (!$file) {
				throw new \Exception('getPublicKey::file_get_contents ERROR');
			}
			$cert   = chunk_split(base64_encode($file), 64, "\n");
			$cert   = "-----BEGIN CERTIFICATE-----\n" . $cert . "-----END CERTIFICATE-----\n";
			$res    = openssl_pkey_get_public($cert);
			$detail = openssl_pkey_get_details($res);
			openssl_free_key($res);
			if (!$detail) {
				throw new \Exception('getPublicKey::openssl_pkey_get_details ERROR');
			}
			return $detail['key'];
		} catch (\Exception $e) {
			throw $e;
		}
	}


}