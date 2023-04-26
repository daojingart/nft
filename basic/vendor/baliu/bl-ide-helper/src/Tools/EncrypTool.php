<?php

namespace Bl\Tools;

use Bl\BaLiu;

/**
 * 加密工具
 * EncrypTool
 * Bl\Tools
 */
class EncrypTool extends BaLiu
{


    /**
     * 生成签名
     *
     * @return array
     */
    public function genSign()
    {
    }

    /**
     * 加密
     *
     * @param $str
     * @param $key
     * @param $iv
     * @return string
     * @param mixed $str
     * @param mixed $key
     * @param mixed $iv
     */
    public function aes_encrypt($str, $key, $iv = '')
    {
    }

    /**
     * 解密
     *
     * @param $str
     * @param $key
     * @param $iv
     * @return string
     * @param mixed $str
     * @param mixed $key
     * @param mixed $iv
     */
    public function aes_decrypt($str, $key, $iv = '')
    {
    }

    /**
     * 加密字符串
     *
     * @param $str
     * @return string
     * @param mixed $str
     */
    public function str_encrypt($str)
    {
    }

    /**
     * 解密字符串
     *
     * @param $str
     * @return string
     * @param mixed $str
     */
    public function str_decrypt($str)
    {
    }

    /**
     * 解密字符串遮罩
     *
     * @param        $str
     * @param string $mask
     * @return string
     * @param mixed $str
     */
    public function str_decrypt_mask($str, $mask = '*')
    {
    }

    /**
     * oss 水印编码
     *
     * @param $str
     * @return string|string[]
     * @param mixed $str
     */
    public function oss_watermark_coding($str)
    {
    }
}
