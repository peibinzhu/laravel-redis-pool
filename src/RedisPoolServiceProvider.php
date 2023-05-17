<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use PeibinLaravel\RedisPool\Listeners\BootApplicationListener;
use PeibinLaravel\RedisPool\Pool\PoolFactory;
use PeibinLaravel\SwooleEvent\Events\BootApplication;

class RedisPoolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $dependencies = [
            RedisFactory::class => RedisFactory::class,
            PoolFactory::class  => PoolFactory::class,
        ];
        $this->registerDependencies($dependencies);

        $listeners = [
            BootApplication::class => BootApplicationListener::class,
        ];
        $this->registerListeners($listeners);
    }

    private function registerDependencies(array $dependencies)
    {
        $config = $this->app->get(Repository::class);
        foreach ($dependencies as $abstract => $concrete) {
            $concreteStr = is_string($concrete) ? $concrete : gettype($concrete);
            if (is_string($concrete) && method_exists($concrete, '__invoke')) {
                $concrete = function () use ($concrete) {
                    return $this->app->call($concrete . '@__invoke');
                };
            }
            $this->app->singleton($abstract, $concrete);
            $config->set(sprintf('dependencies.%s', $abstract), $concreteStr);
        }
    }

    private function registerListeners(array $listeners)
    {
        $dispatcher = $this->app->get(Dispatcher::class);
        foreach ($listeners as $event => $_listeners) {
            foreach ((array)$_listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }
}
