<?php

namespace Soyhuce\DevTools\Test\Feature\Debug;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LogicException;
use Soyhuce\DevTools\Debug\Debug;
use Soyhuce\DevTools\Test\TestCase;

/**
 * @coversNothing
 */
class TimeCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set([
            'dev-tools.debugger.enabled' => true,
            'dev-tools.debugger.time.enabled' => true,
        ]);
    }

    /**
     * @test
     */
    public function timingsAreCollected(): void
    {
        Debug::startMeasure('foo');
        Debug::stopMeasure('foo');

        Log::shouldReceive('debug')
            ->withArgs(static function (string $message) {
                return Str::containsAll($message, [
                    'time : Booting -> ',
                    'time : foo -> ',
                    'time : Application -> ',
                ]);
            });

        Debug::log();
    }

    /**
     * @test
     */
    public function timerCanBeUsedToHaveStatistics(): void
    {
        Collection::times(3, static function (): void {
            Debug::startMeasure('foo');
            Debug::stopMeasure('foo');
        });

        Log::shouldReceive('debug')
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
    }

    /**
     * @test
     */
    public function timerCannotBeStartedTwice(): void
    {
        $this->expectException(LogicException::class);
        $this->expectErrorMessage('A measure foo is already started');

        Debug::startMeasure('foo');
        Debug::startMeasure('foo');
    }

    /**
     * @test
     */
    public function timerMustBeStartedBeforeStopped(): void
    {
        $this->expectException(LogicException::class);
        $this->expectErrorMessage('A measure foo is not started');

        Debug::stopMeasure('foo');
    }
}
