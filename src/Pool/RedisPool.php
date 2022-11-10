<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool\Pool;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use PeibinLaravel\Pool\Pool;
use PeibinLaravel\RedisPool\Frequency;
use PeibinLaravel\RedisPool\RedisConnection;

class RedisPool extends Pool
{
    protected ?array $config = null;

    public function __construct(Container $container, protected string $name)
    {
        $config = $container->get(Repository::class);
        $key = sprintf('database.redis.%s', $this->name);
        if (!$config->has($key)) {
            throw new InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }

        $this->config = $config->get('database.redis');
        $options = Arr::get($config->get($key), 'pool', []);

        $this->frequency = $container->make(Frequency::class);
        parent::__construct($container, $options);
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function createConnection(): RedisConnection
    {
        return new RedisConnection($this->container, $this, $this->config, $this->name);
    }
}
