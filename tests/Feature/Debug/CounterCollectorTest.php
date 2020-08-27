<?php

namespace Soyhuce\DevTools\Test\Feature\Debug;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use Soyhuce\DevTools\Test\TestCase;

class CounterCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set([
            'dev-tools.debugger.enabled' => true,
            'dev-tools.debugger.counter.enabled' => true,
        ]);
    }

    /**
     * @test
     */
    public function counterIsCollected(): void
    {
        Debug::incrementCounter('foo');

        Log::shouldReceive('debug')
            ->withArgs(static function (string $message) {
                return Str::contains($message, 'counter : foo -> 1');
            });

        Debug::log();
    }

    /**
     * @test
     */
    public function counterIsCanBeIncrementedMultipleTimes(): void
    {
        Collection::times(15, static fn () => Debug::incrementCounter('foo', 2));

        Log::shouldReceive('debug')
            ->withArgs(static function (string $message) {
                return Str::contains($message, 'counter : foo -> 30');
            });

        Debug::log();
    }
}
