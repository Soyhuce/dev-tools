<?php

namespace Soyhuce\DevTools\Test\Feature\Debug;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use Soyhuce\DevTools\Test\TestCase;

/**
 * @coversNothing
 */
class ModelCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set([
            'dev-tools.debugger.enabled' => true,
            'dev-tools.debugger.model.enabled' => true,
        ]);
    }

    /**
     * @test
     */
    public function modelsAreCollected(): void
    {
        User::query()
            ->create([
                'name' => 'Taylor Otwell',
                'email' => 'taylor@laravel.com',
                'password' => bcrypt('password'),
            ]);

        User::query()->where('email', 'taylor@laravel.com')->first();
        User::query()->where('email', 'john.doe@email.com')->first();
        User::all();

        $log = Log::shouldReceive('debug')
            ->withArgs(static fn (string $message) => Str::contains($message, 'model : Illuminate\Foundation\Auth\User -> 2'));

        Debug::log();

        $log->verify();
        $this->addToAssertionCount(1);
    }
}
