<?php

namespace Soyhuce\DevTools\Debug;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Soyhuce\DevTools\Debug\Collectors\CounterCollector;
use Soyhuce\DevTools\Debug\Collectors\DataCollector;
use Soyhuce\DevTools\Debug\Collectors\MemoryCollector;
use Soyhuce\DevTools\Debug\Collectors\MessageCollector;
use Soyhuce\DevTools\Debug\Collectors\ModelCollector;
use Soyhuce\DevTools\Debug\Collectors\QueryCollector;
use Soyhuce\DevTools\Debug\Collectors\RequestCollector;
use Soyhuce\DevTools\Debug\Collectors\ResponseCollector;
use Soyhuce\DevTools\Debug\Collectors\TimeCollector;
use Soyhuce\DevTools\Debug\Entries\Entry;
use Soyhuce\DevTools\Debug\Warnings\Warning;

class DebugManager
{
    use ForwardsCallsToCollectors, DefinesHelpers;

    private static array $availableCollectors = [
        CounterCollector::class,
        MemoryCollector::class,
        MessageCollector::class,
        ModelCollector::class,
        QueryCollector::class,
        RequestCollector::class,
        ResponseCollector::class,
        TimeCollector::class,
    ];

    private Application $app;

    private bool $booted = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;
        if (!$this->isEnabled()) {
            return;
        }

        $this->registerDebugMiddleware();
        $this->resolveCollectors();
        $this->app->terminating(function () {
            $this->log();
        });
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.enabled');
    }

    private function registerDebugMiddleware(): void
    {
        $httpKernel = app(HttpKernel::class);

        if (!method_exists($httpKernel, 'pushMiddleware')) {
            return;
        }

        $httpKernel->pushMiddleware(DebugMiddleware::class);
    }

    private function resolveCollectors(): void
    {
        $this->collectors = collect(static::$availableCollectors)
            ->map(function (string $collector): DataCollector {
                return $this->app->make($collector);
            })
            ->filter(static function (DataCollector $collector): bool {
                return $collector->isEnabled();
            })
            ->each(static function (DataCollector $collector): void {
                $collector->boot();
            })
            ->mapWithKeys(static function (DataCollector $collector): array {
                return [$collector->getName() => $collector];
            })
            ->all();
    }

    public function resetCollectors(): void
    {
        collect($this->collectors)
            ->each(function (DataCollector $collector) {
                $collector->reset();
            });
    }

    public function log(): void
    {
        $entries = $this->entries();
        $warnings = $this->warnings();

        if (!$warnings) {
            Log::debug(PHP_EOL . $entries->implode(PHP_EOL));
        }

        Log::debug(PHP_EOL . $entries->implode(PHP_EOL) . PHP_EOL . $warnings->implode(PHP_EOL));
    }

    /**
     * @return \Illuminate\Support\Collection<Entry>
     */
    private function entries(): Collection
    {
        return collect($this->collectors)
            ->flatMap(static function (DataCollector $collector) {
                return $collector->collect();
            })
            ->sortBy(static fn(Entry $entry) => $entry->getMicroTime())
            ->map(static fn(Entry $entry) => (string) $entry);
    }

    /**
     * @return \Illuminate\Support\Collection<Warning>
     */
    private function warnings(): Collection
    {
        $warnings = collect($this->collectors)
            ->flatMap(static function (DataCollector $collector) {
                return $collector->warnings();
            })
            ->map(static fn(Warning $warning) => (string) $warning);

        if ($warnings->isEmpty()) {
            return $warnings;
        }

        $maxLength = $warnings->max(static fn(string $warning) => Str::length($warning));

        return $warnings
            ->map(static function (string $warning) use ($maxLength) {
                return sprintf(
                    '!! %s%s !!',
                    $warning,
                    str_repeat(' ', $maxLength - Str::length($warning))
                );
            })
            ->prepend(str_repeat('!', $maxLength + 6))
            ->push(str_repeat('!', $maxLength + 6));
    }
}
