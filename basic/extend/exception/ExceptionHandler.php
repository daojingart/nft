<?php

namespace exception;

use think\exception\Handle;
use Exception;
use think\Log;

/**
 * 重写Handle的render方法，实现自定义异常消息
 * Class ExceptionHandler
 * @package app\common\library\exception
 */
class ExceptionHandler extends Handle
{
    private $code;
    private $message;

    /**
     * @Notes: 输出异常信息
     * @Interface render
     * @param Exception $e
     * @return \think\Response|\think\response\Json
     * @author: Mr.Zhang
     * @copyright: 河南八六互联信息技术有限公司
     * @Time: 2021/4/13   5:21 下午
     */
    public function render(Exception $e)
    {
        if ($e instanceof BaseException) {
            $this->code = $e->code;
            $this->message = $e->message;
        } else {
            if (config('app_debug')) {
                return parent::render($e);
            }
            $this->code = 0;
            $this->message = $e->getMessage() ?: '很抱歉，服务器内部错误';
            $this->recordErrorLog($e);
        }
        return json(['msg' => $this->message, 'code' => $this->code]);
    }

    /**
     * 将异常写入日志
     * @param Exception $e
     */
    private function recordErrorLog(Exception $e)
    {
        Log::record($e->getMessage(), 'error');
        Log::record($e->getTraceAsString(), 'error');
    }
}
