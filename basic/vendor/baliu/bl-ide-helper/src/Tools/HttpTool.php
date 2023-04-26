<?php

namespace Bl\Tools;

use Bl\BaLiu;

/**
 *
 * http请求工具
 * HttpTool
 * Bl\Tools
 */
class HttpTool extends BaLiu
{


    /**
     * 发送一个GET请求
     *
     * @param string $url     请求URL
     * @param array  $params  请求参数
     * @param array  $options 扩展参数
     * @return mixed|string
     */
    public function get($url, $params = [], $options = [])
    {
    }

    /**
     * 发送一个POST请求
     *
     * @param string $url     请求URL
     * @param array  $params  请求参数
     * @param array  $options 扩展参数
     * @return mixed|string
     */
    public function post($url, $params = [], $options = [])
    {
    }

    /**
     * CURL发送Request请求,含POST和REQUEST
     *
     * @param string $url     请求的链接
     * @param mixed  $params  传递的参数
     * @param string $method  请求的方法
     * @param mixed  $options CURL的参数
     * @return array
     */
    private function sendRequest($url, $params = [], $method = 'POST', $options = [])
    {
    }

    /**
     * 异步发送一个请求
     *
     * @param string $url    请求的链接
     * @param mixed  $params 请求的参数
     * @param string $method 请求的方法
     * @return boolean TRUE
     */
    public function sendAsyncRequest($url, $params = [], $method = 'POST')
    {
    }

    /**
     * 发送文件到客户端
     *
     * @param string $file
     * @param bool   $delaftersend
     * @param bool   $exitaftersend
     */
    public function sendToBrowser($file, $delaftersend = true, $exitaftersend = true)
    {
    }
}
