<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool;

use PeibinLaravel\RedisPool\Pool\PoolFactory;

class RedisProxy extends Redis
{
    public function __construct(PoolFactory $factory, string $pool)
    {
        parent::__construct($factory);

        $this->poolName = $pool;
    }

    /**
     * WARN: Can't remove this function, because AOP need it.
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        return parent::__call($name, $arguments);
    }
}
