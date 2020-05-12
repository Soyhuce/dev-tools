<?php

namespace Soyhuce\DevTools;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Soyhuce\DevTools\Middleware\DebuggerMiddleware;

class ServiceProvider extends BaseServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMiddleware(DebuggerMiddleware::class);

        $this->publishes([
            __DIR__ . '/../assets/config.php' => config_path('dev-tools.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            DebugManager::class,
            function () {
                return new DebugManager($this->app);
            }
        );

        $this->app->alias(DebugManager::class, 'debug');

        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'dev-tools');
    }

    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }
}
