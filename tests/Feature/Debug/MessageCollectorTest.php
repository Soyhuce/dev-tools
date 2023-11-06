<?php

namespace Soyhuce\DevTools\Test\Feature\Debug;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use Soyhuce\DevTools\Test\TestCase;

/**
 * @coversNothing
 */
class MessageCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set([
            'dev-tools.debugger.enabled' => true,
            'dev-tools.debugger.message.enabled' => true,
        ]);
    }

    /**
     * @test
     */
    public function messageAreCollected(): void
    {
        Debug::message('Foo');

        $log = Log::shouldReceive('debug')
            ->withArgs(static fn (string $message) => Str::contains($message, 'message : Foo'));

        Debug::log();

        $log->verify();
        $this->addToAssertionCount(1);
    }
}
