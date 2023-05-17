<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool;

use Illuminate\Contracts\Container\Container;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Arr;
use PeibinLaravel\Contracts\StdoutLoggerInterface;
use PeibinLaravel\Pool\Connection as BaseConnection;
use PeibinLaravel\Pool\Contracts\ConnectionInterface;
use PeibinLaravel\Pool\Contracts\PoolInterface;
use PeibinLaravel\Pool\Exceptions\ConnectionException;
use Throwable;

/**
 * @method bool select(int $db)
 */
class RedisConnection extends BaseConnection implements ConnectionInterface
{
    use Traits\ScanCaller;
    use Traits\MultiExec;

    protected Connection | null $connection = null;

    /**
     * Current redis database.
     */
    protected ?int $database = null;

    public function __construct(
        Container $container,
        PoolInterface $pool,
        protected array $config,
        protected string $name
    ) {
        parent::__construct($container, $pool);

        $this->reconnect();
    }

    public function __call($name, $arguments)
    {
        try {
            $result = $this->connection->{$name}(...$arguments);
        } catch (Throwable $exception) {
            $result = $this->retry($name, $arguments, $exception);
        }

        return $result;
    }

    public function getActiveConnection()
    {
        if ($this->check()) {
            return $this;
        }

        if (!$this->reconnect()) {
            throw new ConnectionException('Connection reconnect failed.');
        }

        return $this;
    }

    public function reconnect(): bool
    {
        $manager = new RedisManager(
            $this->container,
            Arr::pull($this->config, 'client', 'phpredis'),
            $this->config
        );
        $redis = $manager->connection($this->name);

        $options = $this->config[$this->name]['options'] ?? [];

        foreach ($options as $name => $value) {
            // The name is int, value is string.
            $redis->client()->setOption($name, $value);
        }

        $this->connection = $redis;
        $this->lastUseTime = microtime(true);

        return true;
    }

    public function close(): bool
    {
        $this->connection->client()->close();
        unset($this->connection);

        return true;
    }

    public function release(): void
    {
        $database = $this->config[$this->name]['database'] ?? null;
        if ($this->database && $this->database != $database) {
            // Select the origin db after execute select.
            $this->select($database);
            $this->database = null;
        }
        parent::release();
    }

    public function setDatabase(?int $database): void
    {
        $this->database = $database;
    }

    protected function retry($name, $arguments, Throwable $exception)
    {
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $logger->warning('Redis::__call failed, because ' . $exception->getMessage());

        try {
            $this->reconnect();
            $result = $this->connection->{$name}(...$arguments);
        } catch (Throwable $exception) {
            $this->lastUseTime = 0.0;
            throw $exception;
        }

        return $result;
    }
}
