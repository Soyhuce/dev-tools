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
        'dev-tools.debugger.time.enabled' => true,
    ]);
});

test('timings are collected', function (): void {
    Debug::startMeasure('foo');
    Debug::stopMeasure('foo');

    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::containsAll($message, [
                'time : Booting -> ',
                'time : foo -> ',
                'time : Application -> ',
            ]);
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});

test('timer can be used to have statistics', function (): void {
    Collection::times(3, static function (): void {
        Debug::startMeasure('foo');
        Debug::stopMeasure('foo');
    });

    $log = Log::shouldReceive('debug')
        ->withArgs(static function (string $message) {
            return Str::containsAll($message, [
                'time : foo -> ',
                'cumulated on 3 entries',
                'avg',
                'min',
                'max',
                'std',
            ]);
        });

    Debug::log();

    $log->verify();
    $this->addToAssertionCount(1);
});

test('timer cannot be started twice', function (): void {
    $this->expectException(LogicException::class);
    $this->expectExceptionMessage('A measure foo is already started');

    Debug::startMeasure('foo');
    Debug::startMeasure('foo');
});

test('timer must be started before stopped', function (): void {
    $this->expectException(LogicException::class);
    $this->expectExceptionMessage('A measure foo is not started');

    Debug::stopMeasure('foo');
});
