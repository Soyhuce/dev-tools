<?php

namespace Soyhuce\DevTools\Test\Feature\Debug;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Date;
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

        $log = Log::shouldReceive('debug')
            ->withArgs(static fn (string $message) => Str::containsAll($message, [
                'database : select * from "users" where "email" = \'taylor@laravel.com\' limit 1 -> ',
                'database : query executed :',
                'select :',
            ]));

        Debug::log();

        $log->verify();
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function queriesAreCollectedWithUpdates(): void
    {
        Date::setTestNow('2023-08-11 11:17:52');

        User::query()->where('email', 'taylor@laravel.com')->update(['name' => 'Taylor']);

        $log = Log::shouldReceive('debug')
            ->withArgs(static fn (string $message) => Str::containsAll($message, [
                'database : update "users" set "name" = \'Taylor\', "updated_at" = \'2023-08-11 11:17:52\' where "email" = \'taylor@laravel.com\' -> ',
                'database : query executed :',
            ]));

        Debug::log();

        $log->verify();
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function queriesAreCollectedWithDifferentKindsOfBindings(): void
    {
        User::query()
            ->whereRaw('"string" = ?', ['taylor@laravel.com'])
            ->whereRaw('"nullable" = ?', [null])
            ->whereRaw('"bool" = ?', [true])
            ->whereRaw('"float" = ?', [1.2])
            ->whereRaw('"integer" = ?', [1])
            ->first();

        $log = Log::shouldReceive('debug')
            ->withArgs(static fn (string $message) => Str::containsAll($message, [
                'database : select * from "users" where "string" = \'taylor@laravel.com\' and "nullable" = null and "bool" = 1 and "float" = 1.2 and "integer" = 1 limit 1 -> ',
                'database : query executed :',
            ]));

        Debug::log();

        $log->verify();
        $this->addToAssertionCount(1);
    }
}
