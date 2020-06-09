<?php

namespace Soyhuce\DevTools;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Soyhuce\DevTools\Debug\Debug;
use Soyhuce\DevTools\Debug\DebugManager;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../assets/config.php' => config_path('dev-tools.php'),
        ], 'config');

        Debug::boot();
    }

    public function register(): void
    {
        $this->app->singleton(DebugManager::class);
        $this->app->alias(DebugManager::class, 'debug');

        $this->mergeConfigFrom(__DIR__ . '/../assets/config.php', 'dev-tools');
    }
}
