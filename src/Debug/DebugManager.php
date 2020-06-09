<?php

namespace Soyhuce\DevTools\Debug;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Soyhuce\DevTools\Debug\Collectors\CounterCollector;
use Soyhuce\DevTools\Debug\Collectors\DataCollector;
use Soyhuce\DevTools\Debug\Collectors\MemoryCollector;
use Soyhuce\DevTools\Debug\Collectors\MessageCollector;
use Soyhuce\DevTools\Debug\Collectors\ModelCollector;
use Soyhuce\DevTools\Debug\Collectors\NullCollector;
use Soyhuce\DevTools\Debug\Collectors\QueryCollector;
use Soyhuce\DevTools\Debug\Collectors\RequestCollector;
use Soyhuce\DevTools\Debug\Collectors\ResponseCollector;
use Soyhuce\DevTools\Debug\Collectors\TimeCollector;
use Soyhuce\DevTools\Debug\Entries\Entry;
use Soyhuce\DevTools\Debug\Warnings\Warning;

class DebugManager
{
    use ForwardsCallsToCollectors;

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

        $this->resolveCollectors();
        $this->app->terminating(function () {
            $this->log();
        });
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.enabled');
    }

    private function resolveCollectors(): void
    {
        $this->collectors = collect(static::$availableCollectors)
            ->map(function (string $collector): DataCollector {
                return $this->app->make($collector);
            })
            ->map(static function (DataCollector $collector): DataCollector {
                return $collector->isEnabled() ? $collector : new NullCollector($collector->getName());
            })
            ->each(static function (DataCollector $collector): void {
                $collector->boot();
            })
            ->mapWithKeys(static function (DataCollector $collector): array {
                return [$collector->getName() => $collector];
            })
            ->all();
    }

    public function log(): void
    {
        $report = $this->data()->implode(PHP_EOL);
        $warnings = $this->warnings()->implode(PHP_EOL);
        if ($warnings) {
            $warnings = PHP_EOL . implode(PHP_EOL, [str_repeat('!', 60), $warnings, str_repeat('!', 60)]);
        }
        Log::debug(PHP_EOL . $report . $warnings);
    }

    /**
     * @return \Illuminate\Support\Collection<Entry>
     */
    private function data(): Collection
    {
        return collect($this->collectors)
            ->flatMap(function (DataCollector $collector) {
                return $collector->collect();
            })
            ->sortBy(fn(Entry $entry) => $entry->getMicroTime())
            ->map(fn(Entry $entry) => (string) $entry);
    }

    /**
     * @return \Illuminate\Support\Collection<Warning>
     */
    private function warnings(): Collection
    {
        return collect($this->collectors)
            ->flatMap(function (DataCollector $collector) {
                return $collector->warnings();
            })
            ->map(fn(Warning $warning) => (string) $warning);
    }
}
