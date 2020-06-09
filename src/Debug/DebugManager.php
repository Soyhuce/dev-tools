<?php

namespace Soyhuce\DevTools\Debug;

use Illuminate\Contracts\Foundation\Application;
use Log;
use Soyhuce\DevTools\Debug\Collectors\DataCollector;
use Soyhuce\DevTools\Debug\Collectors\MemoryCollector;
use Soyhuce\DevTools\Debug\Collectors\MessageCollector;
use Soyhuce\DevTools\Debug\Collectors\ModelCollector;
use Soyhuce\DevTools\Debug\Collectors\NullCollector;
use Soyhuce\DevTools\Debug\Collectors\QueryCollector;
use Soyhuce\DevTools\Debug\Collectors\RequestCollector;
use Soyhuce\DevTools\Debug\Collectors\ResponseCollector;
use Soyhuce\DevTools\Debug\Collectors\TimeCollector;

class DebugManager
{
    use ForwardsCallsToCollectors;

    private static array $availableCollectors = [
        MessageCollector::class,
        RequestCollector::class,
        MemoryCollector::class,
        ModelCollector::class,
        TimeCollector::class,
        QueryCollector::class,
        ResponseCollector::class,
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
        $report = implode(PHP_EOL, $this->data());
        $warnings = implode(PHP_EOL, $this->warnings());
        if ($warnings) {
            $warnings = PHP_EOL . implode(PHP_EOL, [str_repeat('!', 60), $warnings, str_repeat('!', 60)]);
        }
        \Log::debug(PHP_EOL . $report . $warnings);
    }

    private function data()
    {
        return collect($this->collectors)
            ->flatMap
            ->collect()
            ->sortBy('time')
            ->map(
                static function ($datum) {
                    return sprintf('=> [%s] %s : %s', $datum['pretty_time'], $datum['type'], $datum['message']);
                }
            )
            ->toArray();
    }

    private function warnings()
    {
        return collect($this->collectors)
            ->flatMap
            ->warnings()
            ->map(
                static function ($warning) {
                    return sprintf('!! %s : %s', $warning['type'], $warning['message']);
                }
            )->toArray();
    }
}
