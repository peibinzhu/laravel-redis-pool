<?php

declare(strict_types=1);

namespace PeibinLaravel\RedisPool;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Support\ServiceProvider;
use PeibinLaravel\RedisPool\Listeners\BootstrapListener;
use PeibinLaravel\SwooleEvent\Events\BeforeMainServerStart;
use PeibinLaravel\Utils\Providers\RegisterProviderConfig;

class RedisPoolServiceProvider extends ServiceProvider
{
    use RegisterProviderConfig;

    public function __invoke(): array
    {
        return [
            'listeners' => [
                ArtisanStarting::class       => BootstrapListener::class,
                BeforeMainServerStart::class => BootstrapListener::class,
            ],
        ];
    }
}
