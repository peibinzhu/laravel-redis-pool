<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool;

trait ScanCaller
{
    public function scan(&$cursor, ...$arguments)
    {
        return $this->__call('scan', array_merge([&$cursor], $arguments));
    }

    public function hScan($key, &$cursor, ...$arguments)
    {
        return $this->__call('hScan', array_merge([$key, &$cursor], $arguments));
    }

    public function zScan($key, &$cursor, ...$arguments)
    {
        return $this->__call('zScan', array_merge([$key, &$cursor], $arguments));
    }

    public function sScan($key, &$cursor, ...$arguments)
    {
        return $this->__call('sScan', array_merge([$key, &$cursor], $arguments));
    }
}
