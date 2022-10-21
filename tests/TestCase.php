<?php

namespace Soyhuce\DevTools\Test;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    use InteractsWithDeprecationHandling;

    protected function getPackageProviders($app): array
    {
        return [\Soyhuce\DevTools\ServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set([
            'dev-tools' => [
                'bottleneck' => [
                    'duration' => 1000,
                    'only_ajax' => false,
                ],
                'debugger' => [
                    'enabled' => false,
                    'datetime_format' => 'Y-m-d H:i:s.u',
                    'artisan' => ['enabled' => false],
                    'counter' => ['enabled' => false],
                    'database' => [
                        'enabled' => false,
                        'max_queries' => null,
                    ],
                    'memory' => [
                        'enabled' => false,
                        'max' => null,
                    ],
                    'message' => ['enabled' => false],
                    'model' => ['enabled' => false],
                    'request' => ['enabled' => false],
                    'response' => ['enabled' => false],
                    'time' => [
                        'enabled' => false,
                        'max_app_duration' => null,
                    ],
                ],
            ],
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutDeprecationHandling();

        $this->loadLaravelMigrations();

        User::unguard();
    }
}
