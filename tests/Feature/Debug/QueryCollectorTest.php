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
class QueryCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set([
            'dev-tools.debugger.enabled' => true,
            'dev-tools.debugger.database.enabled' => true,
        ]);
    }

    /**
     * @test
     */
    public function queriesAreCollected(): void
    {
        User::query()->where('email', 'taylor@laravel.com')->first();

        Log::shouldReceive('debug')
            ->withArgs(static function (string $message) {
                return Str::containsAll($message, [
                    'database : select * from "users" where "email" = \'taylor@laravel.com\' limit 1 -> ',
                    'database : query executed :',
                ]);
            });

        Debug::log();
    }
}
