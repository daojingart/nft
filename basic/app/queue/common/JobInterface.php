<?php

namespace app\queue\common;

interface  JobInterface
{

    /**
     *
     * @return mixed
     */
    public function run(array $data, string $queue_name): bool;

}