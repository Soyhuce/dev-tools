<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use function Orchestra\Testbench\Pest\defineEnvironment;

defineEnvironment(function (Application $app): void {
    $app['config']->set([
        'dev-tools.debugger.enabled' => true,
        'dev-tools.debugger.memory.enabled' => true,
    ]);
});

test('memory is collected', function (): void {
    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::contains($message, 'memory : ');
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});
