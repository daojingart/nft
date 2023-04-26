<?php

namespace app\queue\controller;

use app\common\helpers\Tools;
use app\queue\common\BaseJob;
use app\queue\common\JobInterface;

/**
 *
 * 默认多任务队列适用于执行时间短的任务
 *
 * php think queue:listen -h  帮助
 *
 * php think queue:listen --queue default
 *
 * DefaultJob
 * app\queue\controller
 */
class DefaultJob extends BaseJob implements JobInterface
{

    /** @var array $allowQueue 允许的队列名称 */
    public $allowQueue = [
        self::QUEUE_NAME,
        self::QUEUE_NAME_TEST,
    ];

    /**
     *
     * @string 默认队列名
     */
    public const QUEUE_NAME = 'default';

    /**
     * @string 测试队列
     */
    public const QUEUE_NAME_TEST = 'test';

    public function run(array $data, string $queue_name): bool
    {
        try {
            $this->{$data['current_use_queue_name']}($data);
        } catch (\Exception $exception) {
            trace($exception->getMessage(), 'queue');
            return false;
        }

        return true;
    }

    /**
     * 默认队列
     * @param $data
     * @return bool
     */
    public function default($data): bool
    {
        Tools::show_msg(99999);
        return true;
    }

    /**
     * 测试队列
     * @param $data
     * @return bool
     */
    public function test($data): bool
    {
        Tools::show_msg(77777);

        return true;
    }

}