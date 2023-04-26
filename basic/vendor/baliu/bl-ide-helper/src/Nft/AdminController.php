<?php

namespace Bl\Nft;

use Bl\BaLiu;

class AdminController extends BaLiu
{


    /**
     * 验证登录状态
     *
     * @param mixed $store
     */
    public function checkLogin($store)
    {
    }

    /**
     * 返回加密封装后的 API 数据到客户端
     *
     * @param mixed $code
     * @param mixed $msg
     * @param mixed $url
     * @param mixed $data
     */
    public function renderJson($code = 1, $msg = '', $url = '', $data = [])
    {
    }
}
