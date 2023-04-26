<?php

namespace Bl\Tools;

use Bl\BaLiu;

/**
 *
 * 随机数工具
 * RandomTool
 * Bl\Tools
 */
class RandomTool extends BaLiu
{


    /**
     * 生成数字和字母
     *
     * @param int $len 长度
     * @return string
     */
    public function alnum($len = 6)
    {
    }

    /**
     * 仅生成字符
     *
     * @param int $len 长度
     * @return string
     */
    public function alpha($len = 6)
    {
    }

    /**
     * 生成指定长度的随机数字
     *
     * @param int $len 长度
     * @return string
     */
    public function numeric($len = 4)
    {
    }

    /**
     * 生成指定长度的无0随机数字
     *
     * @param int $len 长度
     * @return string
     */
    public function nozero($len = 4)
    {
    }

    /**
     * 能用的随机数生成
     *
     * @param string $type 类型 alpha/alnum/numeric/nozero/unique/md5/encrypt/sha1
     * @param int    $len  长度
     * @return string
     */
    public function build($type = 'alnum', $len = 8)
    {
    }

    /**
     * 根据数组元素的概率获得键名
     *
     * @param array $ps     array("p1"=>20, "p2"=>30, "p3"=>50);
     * @param int   $num    默认为1,即随机出来的数量
     * @param bool  $unique 默认为true,即当num>1时,随机出的数量是否唯一
     * @return mixed 当num为1时返回键名,反之返回一维数组
     */
    public function lottery($ps, $num = 1, $unique = true)
    {
    }

    /**
     * 获取全球唯一标识
     *
     * @return string
     */
    public function uuid()
    {
    }
}
