<?php

namespace adapay\tools;

/**
 * 支付日志类
 */
class Logs
{
    /**
     * 写入日志
     * @param string|array $values
     * @param string       $dir
     * @return bool|int
     */
    public static function write_log($values, $dir)
    {
        if (is_array($values))
            $values = print_r($values, true);
        // 日志内容
        $content = '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $values . PHP_EOL . PHP_EOL;
        try {
            // 文件路径
            $filePath = RUNTIME_PATH.$dir . '/logs/';
            // 路径不存在则创建
            !is_dir($filePath) && mkdir($filePath, 0755, true);
            // 写入文件
            return file_put_contents($filePath . date('Ymd') . '.log', $content, FILE_APPEND);
        } catch (\Exception $e) {
            return false;
        }
    }


}