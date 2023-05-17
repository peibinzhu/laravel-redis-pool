<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool\Traits;

use Redis;
use RedisCluster;

trait MultiExec
{
    /**
     * Execute commands in a pipeline.
     *
     * @return array|Redis
     */
    public function pipeline(callable $callback = null)
    {
        $pipeline = $this->__call('pipeline', []);

        return is_null($callback) ? $pipeline : tap($pipeline, $callback)->exec();
    }

    /**
     * Execute commands in a transaction.
     *
     * @return array|Redis|RedisCluster
     */
    public function transaction(callable $callback = null)
    {
        $transaction = $this->__call('multi', []);

        return is_null($callback) ? $transaction : tap($transaction, $callback)->exec();
    }
}
