<?php

namespace Soyhuce\DevTools\Test\Feature\Debug;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use Soyhuce\DevTools\Test\TestCase;

class MemoryCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set([
            'dev-tools.debugger.enabled' => true,
            'dev-tools.debugger.memory.enabled' => true,
        ]);
    }

    /**
     * @test
     */
    public function memoryIsCollected(): void
    {
        Log::shouldReceive('debug')
            ->withArgs(static function (string $message) {
                return Str::contains($message, 'memory : ');
            });

        Debug::log();
    }
}
