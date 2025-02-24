<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use function Orchestra\Testbench\Pest\defineEnvironment;

defineEnvironment(function (Application $app): void {
    $app['config']->set([
        'dev-tools.debugger.enabled' => true,
        'dev-tools.debugger.database.enabled' => true,
    ]);
});

test('queries are collected', function (): void {
    User::query()->where('email', 'taylor@laravel.com')->first();

    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::containsAll($message, [
                'database : select * from "users" where "email" = \'taylor@laravel.com\' limit 1 -> ',
                'database : query executed :',
                'select :',
            ]);
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});

test('queries are collected with updates', function (): void {
    Date::setTestNow('2023-08-11 11:17:52');

    User::query()->where('email', 'taylor@laravel.com')->update(['name' => 'Taylor']);

    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::containsAll($message, [
                'database : update "users" set "name" = \'Taylor\', "updated_at" = \'2023-08-11 11:17:52\' where "email" = \'taylor@laravel.com\' -> ',
                'database : query executed :',
            ]);
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});

test('queries are collected with different kinds of bindings', function (): void {
    User::query()
        ->whereRaw('"string" = ?', ['taylor@laravel.com'])
        ->whereRaw('"nullable" = ?', [null])
        ->whereRaw('"bool" = ?', [true])
        ->whereRaw('"float" = ?', [1.2])
        ->whereRaw('"integer" = ?', [1])
        ->first();

    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::containsAll($message, [
                'database : select * from "users" where "string" = \'taylor@laravel.com\' and "nullable" = null and "bool" = 1 and "float" = 1.2 and "integer" = 1 limit 1 -> ',
                'database : query executed :',
            ]);
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});
