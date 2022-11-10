<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool;

use Swoole\Coroutine;

class RedisManager extends \Illuminate\Redis\RedisManager
{
    /**
     * Get a Redis connection by name.
     *
     * @param string|null $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function connection($name = null)
    {
        $name = $name ?: 'default';

        if (!$this->isPoolConnection($name)) {
            return parent::connection($name);
        }

        return $this->app->get(RedisFactory::class)->get($name);
    }

    /**
     * Determine if the connection is a pool connection.
     *
     * @param string $name
     * @return bool
     */
    protected function isPoolConnection(string $name): bool
    {
        return Coroutine::getCid() > 0 && isset($this->config[$name]['pool']);
    }
}
