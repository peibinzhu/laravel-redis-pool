<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool\Listeners;

use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Support\Arr;
use PeibinLaravel\RedisPool\Pool\PoolFactory;
use PeibinLaravel\RedisPool\RedisFactory;
use PeibinLaravel\RedisPool\RedisManager;
use PeibinLaravel\Utils\ApplicationContext;

class BootstrapListener
{
    private array $warmServices = [
        'redis',
        'cache',
        'cache.store',
        'session',
        RedisFactory::class,
        PoolFactory::class,
    ];

    private array $providers = [
        CacheServiceProvider::class,
        SessionServiceProvider::class,
    ];

    public function __construct(protected Container $container)
    {
    }

    public function handle(object $event): void
    {
        $this->container->singleton('redis', function ($app) {
            $config = $this->container->get('config')->get('database.redis', []);
            return new RedisManager($app, Arr::pull($config, 'client', 'phpredis'), $config);
        });

        $this->container->singleton(RedisFactory::class);
        $this->container->singleton(PoolFactory::class);

        // Re-register the service provider with the application.
        foreach ($this->providers as $provider) {
            $this->container->register($provider, true);
        }

        // The bindings listed below will be preloaded, avoiding repeated instantiation.
        foreach ($this->warmServices as $service) {
            if (is_string($service) && $this->container->bound($service)) {
                $this->container->get($service);
            }
        }

        ApplicationContext::setContainer($this->container);
    }
}
