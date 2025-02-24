<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use function Orchestra\Testbench\Pest\defineEnvironment;

defineEnvironment(function (Application $app): void {
    $app['config']->set([
        'dev-tools.debugger.enabled' => true,
        'dev-tools.debugger.counter.enabled' => true,
    ]);
});

test('counter is collected', function (): void {
    Debug::incrementCounter('foo');

    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::contains($message, 'counter : foo -> 1');
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});

test('counter is can be incremented multiple times', function (): void {
    Collection::times(15, static fn () => Debug::incrementCounter('foo', 2));

    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::contains($message, 'counter : foo -> 30');
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});
