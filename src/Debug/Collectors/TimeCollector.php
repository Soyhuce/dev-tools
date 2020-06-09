<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;

/**
 * Class TimeCollector
 */
class TimeCollector extends DataCollector
{
    /** @var Application */
    private $app;

    /** @var array */
    private $startedMeasures;

    /** @var array */
    private $measures;

    /**
     * TimeCollector constructor.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->startedMeasures = [];
        $this->measures = [];
    }

    public function getName(): string
    {
        return 'time';
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.time.enabled');
    }

    public function boot()
    {
        $this->app->booted(
            function () {
                $startTime = $this->app['request']->server('REQUEST_TIME_FLOAT');
                if ($startTime) {
                    $this->addMeasure('Booting', $startTime, $this->time());
                }
            }
        );
        $this->startMeasure('Application');
    }

    public function collect(): array
    {
        foreach (array_keys($this->startedMeasures) as $name) {
            $this->stopMeasure($name);
        }

        return collect($this->measures)
            ->map(
                function ($measure) {
                    return [
                        'time' => $measure['stop'],
                        'pretty_time' => Carbon::createFromTimestamp((int) $measure['stop'])->toDateTimeString(),
                        'message' => $measure['name'] . ' -> ' . $this->format($measure['duration']),
                        'type' => $this->getName(),
                    ];
                }
            )->toArray();
    }

    public function warnings(): array
    {
        foreach (array_keys($this->startedMeasures) as $name) {
            $this->stopMeasure($name);
        }
        $max = config('dev-tools.debugger.time.max_app_duration');
        if (!$max || $duration = data_get($this->measures, 'Application.duration') <= $max / 1000) {
            return [];
        }

        return [
            [
                'message' => sprintf(
                    'Application duration exceeded max %s allowed : %s',
                    $this->format($max / 1000),
                    $this->format($duration)
                ),
                'type' => $this->getName(),
            ],
        ];
    }

    public function startMeasure(string $name)
    {
        if ($this->hasStartedMeasure($name)) {
            throw new \LogicException("A measure ${name} is already started");
        }
        $this->startedMeasures[$name] = $this->time();
    }

    public function stopMeasure(string $name)
    {
        if (!$this->hasStartedMeasure($name)) {
            throw new \LogicException("A measure ${name} is not started");
        }
        $this->addMeasure($name, $this->startedMeasures[$name], $this->time());

        unset($this->startedMeasures[$name]);
    }

    public function addMeasure($name, $start, $stop)
    {
        $this->measures[$name] = [
            'name' => $name,
            'start' => $start,
            'stop' => $stop,
            'duration' => $stop - $start,
        ];
    }

    private function hasStartedMeasure(string $name)
    {
        return isset($this->startedMeasures[$name]);
    }

    private function format($seconds)
    {
        if ($seconds < 0.001) {
            return round($seconds * 1000000) . 'μs';
        }
        if ($seconds < 1) {
            return round($seconds * 1000, 2) . 'ms';
        }

        return round($seconds, 2) . 's';
    }
}
