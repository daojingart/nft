<?php

namespace Bl\Nft;

use Bl\BaLiu;

class ApiController extends BaLiu
{


    /**
     * 返回加密封装后的 API 数据到客户端
     *
     * @param mixed $code
     * @param mixed $msg
     * @param mixed $url
     * @param mixed $data
     * @param mixed $used_time
     * @param mixed $reqs
     * @param mixed $time
     */
    public function renderJson($code = 1, $msg = '', $url = '', $data = [], $used_time, $reqs, $time)
    {
    }
}
