<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use function Orchestra\Testbench\Pest\defineEnvironment;

defineEnvironment(function (Application $app): void {
    $app['config']->set([
        'dev-tools.debugger.enabled' => true,
        'dev-tools.debugger.model.enabled' => true,
    ]);
});

test('models are collected', function (): void {
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
        ->withArgs(static function (string $message) {
            return Str::contains($message, 'model : Illuminate\Foundation\Auth\User -> 2');
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});
