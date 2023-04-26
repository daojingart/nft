<?php

namespace app\queue\common;

use think\Queue;
use think\queue\Job;

class BaseJob
{
    /** @var array|string $allowQueue 允许的队列名称 */
    public $allowQueue = "*";

    /** @var string 队列名称 */
    public const QUEUE_NAME = 'default';

    /** @var string  失败重试次数 */
    public $attempted = 3;

    protected static $instance;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    public function fire(Job $job, array $data)
    {

        if (is_array($this->allowQueue) && !in_array($data['current_use_queue_name'], $this->allowQueue, true)) {

            return true;
        }

        try {
            $result = $this->run($data, $job->getName());
        } catch (\Exception $exception) {
            trace([
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'msg'  => $exception->getMessage(),
            ], 'queue');
            $result = false;
        }

        if ($result) {
            $job->delete();
            trace('job has been down and deleted', 'queue');
        } elseif ($job->attempts() > $this->attempted) {
            $job->delete();
            trace("job has been retried more that {$this->attempted} times", 'queue');
        }
    }

    public function run(array $data, string $queue_name): bool
    {
        return true;
    }

    public function failed($data)
    {
        trace($data);
    }

    /**
     * 生产
     * @param array  $data
     * @param string $queue_name 默认为 QUEUE_NAME
     * @return void
     */
    public static function push(array $data, string $queue_name = '')
    {
        $queue_name                     = $queue_name ?: static::QUEUE_NAME;
        $data['current_use_queue_name'] = $queue_name;
        Queue::push(static::class, $data, static::QUEUE_NAME);

    }

    /**
     * 延迟生产
     * @param int    $delay      延迟秒
     * @param array  $data
     * @param string $queue_name 默认为 QUEUE_NAME
     * @return void
     */
    public static function later(int $delay, array $data, string $queue_name = '')
    {
        $queue_name                     = $queue_name ?: static::QUEUE_NAME;
        $data['current_use_queue_name'] = $queue_name;
        Queue::later($delay, static::class, $data, static::QUEUE_NAME);

    }


}