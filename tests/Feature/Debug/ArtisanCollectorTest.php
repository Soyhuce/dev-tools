<?php

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use function Orchestra\Testbench\Pest\defineEnvironment;

defineEnvironment(function (Application $app): void {
    $app['config']->set([
        'dev-tools.debugger.enabled' => true,
        'dev-tools.debugger.artisan.enabled' => true,
    ]);
});

test('artisan command is collected', function (): void {
    Event::dispatch(new CommandStarting(
        'list',
        new ArrayInput(['command' => 'list']),
        new NullOutput()
    ));

    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::contains($message, 'artisan : list');
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});

test('artisan arguments are collected', function (): void {
    Event::dispatch(new CommandStarting(
        'list',
        new ArrayInput(['command' => 'list', '--format' => 'md', 'namespace' => 'make']),
        new NullOutput()
    ));

    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::contains($message, 'artisan : list --format=md make');
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});
