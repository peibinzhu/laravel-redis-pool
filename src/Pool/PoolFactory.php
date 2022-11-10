<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool\Pool;

use Illuminate\Contracts\Container\Container;

class PoolFactory
{
    /**
     * @var array<string, RedisPool>
     */
    protected array $pools = [];

    public function __construct(protected Container $container)
    {
    }

    public function getPool(string $name): RedisPool
    {
        if (isset($this->pools[$name])) {
            return $this->pools[$name];
        }

        if ($this->container instanceof Container) {
            $pool = $this->container->make(RedisPool::class, ['name' => $name]);
        } else {
            $pool = new RedisPool($this->container, $name);
        }
        return $this->pools[$name] = $pool;
    }
}
