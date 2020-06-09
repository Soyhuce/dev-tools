<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Contracts\Events\Dispatcher;
use Soyhuce\DevTools\Debug\Entries\Counter;

class ModelCollector extends DataCollector
{
    private Dispatcher $events;

    /** @var array<\Soyhuce\DevTools\Debug\Entries\Counter> */
    private array $counters = [];

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function getName(): string
    {
        return 'model';
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.model.enabled');
    }

    public function boot()
    {
        $this->events->listen('eloquent.retrieved: *', function (string $event, array $models) {
            $this->incrementCounters(array_filter($models));
        });
    }

    private function incrementCounters(array $models): void
    {
        foreach ($models as $model) {
            $this->getCounter(get_class($model))->increment();
        }
    }

    private function getCounter(string $modelClass): Counter
    {
        return $this->counters[$modelClass] ??= new Counter($this->getName(), $modelClass);
    }

    public function collect(): array
    {
        return $this->counters;
    }
}
