<?php

namespace Bl;

/**
 *
 * 公共方法
 * Bl
 */
class Common extends \Bl\BaLiu
{


    /**
     * 打印调试函数
     *
     * @param mixed $content 打印内容
     * @param bool $is_die 是否终止
     * @return void
     */
    public function pre($content, $is_die = true)
    {
    }

    /**
     * 驼峰命名转下划线命名
     *
     * @param string $str 命名字符串
     * @return string
     */
    public function toUnderScore($str)
    {
    }

    /**
     * 生成密码hash值
     *
     * @param string $password 密码
     * @return string
     */
    public function blhlhash($password)
    {
    }

    /**
     * 对象转换成数组
     *
     * @param object $obj 对象
     * @return array
     */
    public function objToArray($obj)
    {
    }

    /**
     * 多维数组合并
     *
     * @param array $array1 数组1
     * @param array $array2 数组2
     * @return array
     */
    public function array_merge_multiple($array1, $array2)
    {
    }

    /**
     *  生成唯一HASH
     *
     * @return string
     */
    public function get_hash()
    {
    }

    /**
     * 加密手机号中间4位
     *
     * @param string $phone 手机号
     * @return string
     */
    public function phone_substr_replace($phone)
    {
    }

    /**
     * 经典的概率算法
     *
     * @param array $proArr 数组
     * @return string
     */
    public function get_rand($proArr)
    {
    }

    /**
     * 格式化数字
     *
     * @param int $number 数字
     * @return string
     */
    public function float_number($number)
    {
    }
}
