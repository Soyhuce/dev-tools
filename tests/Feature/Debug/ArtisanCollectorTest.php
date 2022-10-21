<?php

namespace Soyhuce\DevTools\Test\Feature\Debug;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use Soyhuce\DevTools\Test\TestCase;

/**
 * @coversNothing
 */
class ArtisanCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set([
            'dev-tools.debugger.enabled' => true,
            'dev-tools.debugger.artisan.enabled' => true,
        ]);
    }

    /**
     * @test
     */
    public function artisanCommandIsCollected(): void
    {
        $this->artisan('list');

        $log = Log::shouldReceive('debug')
            ->withArgs(static function (string $message) {
                return Str::contains($message, 'artisan : list');
            });

        Debug::log();

        $log->verify();
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function artisanArgumentsAreCollected(): void
    {
        $this->artisan('list --format=md make');

        $log = Log::shouldReceive('debug')
            ->withArgs(static function (string $message) {
                return Str::contains($message, 'artisan : list --format=md make');
            });

        Debug::log();

        $log->verify();
        $this->addToAssertionCount(1);
    }
}
