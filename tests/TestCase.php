<?php

namespace Soyhuce\DevTools\Test;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\Pest\WithPest;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    use InteractsWithDeprecationHandling;
    use WithPest;

    protected function getPackageProviders($app): array
    {
        return [\Soyhuce\DevTools\ServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutDeprecationHandling();

        $this->loadLaravelMigrations();

        User::unguard();
    }
}
