<?php

namespace Soyhuce\DevTools\Test\Feature\Debug;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Debug;
use Soyhuce\DevTools\Test\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

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
        Event::dispatch(new CommandStarting(
            'list',
            new ArrayInput(['command' => 'list']),
            new NullOutput()
        ));

        $log = Log::shouldReceive('debug')
            ->withArgs(static fn (string $message) => Str::contains($message, 'artisan : list'));

        Debug::log();

        $log->verify();
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function artisanArgumentsAreCollected(): void
    {
        Event::dispatch(new CommandStarting(
            'list',
            new ArrayInput(['command' => 'list', '--format' => 'md', 'namespace' => 'make']),
            new NullOutput()
        ));

        $log = Log::shouldReceive('debug')
            ->withArgs(static fn (string $message) => Str::contains($message, 'artisan : list --format=md make'));

        Debug::log();

        $log->verify();
        $this->addToAssertionCount(1);
    }
}
