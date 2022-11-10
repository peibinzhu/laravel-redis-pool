<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use PeibinLaravel\RedisPool\Exceptions\InvalidRedisProxyException;

class RedisFactory
{
    /**
     * @var RedisProxy[]
     */
    protected array $proxies = [];

    public function __construct(Container $container, Repository $config)
    {
        $redisConfig = $config->get('database.redis', []);
        $redisConfig = Arr::except($redisConfig, ['client', 'options']);
        foreach ($redisConfig as $poolName => $item) {
            $this->proxies[$poolName] = $container->make(RedisProxy::class, ['pool' => $poolName]);
        }
    }

    public function get(string $poolName)
    {
        $proxy = $this->proxies[$poolName] ?? null;
        if (!$proxy instanceof RedisProxy) {
            throw new InvalidRedisProxyException('Invalid Redis connection.');
        }

        return $proxy;
    }
}
