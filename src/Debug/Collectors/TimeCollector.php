<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Foundation\Application;
use Soyhuce\DevTools\Debug\Entries\Measure;
use Soyhuce\DevTools\Debug\Warnings\ApplicationDurationExceeded;

/**
 * Class TimeCollector
 */
class TimeCollector extends DataCollector
{
    private Application $app;

    /** @var array<string, \Soyhuce\DevTools\Debug\Entries\Measure> */
    private array $measures = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getName(): string
    {
        return 'time';
    }

    public function boot(): void
    {
        $this->app->booted(function () {
            $startTime = $this->app['request']->server('REQUEST_TIME_FLOAT');

            if ($startTime) {
                $this->measures['Booting'] = new Measure($this->getName(), 'Booting', [microtime(true) - $startTime]);
            }
        });
        $this->startMeasure('Application');
    }

    public function startMeasure(string $name): void
    {
        $this->getMeasure($name)->start();
    }

    public function stopMeasure(string $name): void
    {
        $this->getMeasure($name)->stop();
    }

    public function getMeasure(string $name): Measure
    {
        return $this->measures[$name] ??= new Measure($this->getName(), $name);
    }

    public function collect(): array
    {
        $this->stopRunningMeasures();

        return array_values($this->measures);
    }

    public function warnings(): array
    {
        $this->stopRunningMeasures();

        $max = config('dev-tools.debugger.time.max_app_duration');

        if ($max === null) {
            return [];
        }

        $max = $max / 1000;
        $appDuration = $this->measures['Application']->lastMeasure();
        if ($appDuration <= $max) {
            return [];
        }

        return [
            new ApplicationDurationExceeded($this->getName(), $max, $appDuration),
        ];
    }

    private function stopRunningMeasures(): void
    {
        foreach ($this->measures as $measure) {
            if ($measure->isRunning()) {
                $measure->stop();
            }
        }
    }
}
