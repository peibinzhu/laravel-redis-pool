<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool;

use Illuminate\Support\ServiceProvider;
use PeibinLaravel\ProviderConfig\Contracts\ProviderConfigInterface;
use PeibinLaravel\RedisPool\Listeners\BootApplicationListener;
use PeibinLaravel\RedisPool\Pool\PoolFactory;
use PeibinLaravel\SwooleEvent\Events\BootApplication;

class RedisPoolServiceProvider extends ServiceProvider implements ProviderConfigInterface
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                RedisFactory::class => RedisFactory::class,
                PoolFactory::class  => PoolFactory::class,
            ],
            'listeners'    => [
                BootApplication::class => BootApplicationListener::class,
            ],
        ];
    }
}
