<?php

namespace Bl\Tools;

use Bl\BaLiu;

/**
 *
 * 海报工具
 * CacheTool
 * Bl\Tools
 */
class PostersTool extends BaLiu
{


    /**
     * 自定义海报生成(图片水印必须为oss地址)
     *
     * @param string $main_img 海报主图
     * @param array|string $config 自定义配置
     * @param array $user_info 会员信息必须包含 nickname,invite_code,avatar 三个字段
     * @return string
     * @throws \JsonException
     */
    public function diyGenerate($main_img, $config, $user_info = [])
    {
    }
}
