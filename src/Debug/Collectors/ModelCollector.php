<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;

class ModelCollector extends DataCollector
{
    /** @var \Illuminate\Contracts\Events\Dispatcher */
    private $events;

    private $models;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
        $this->models = [];
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
        $this->events->listen('eloquent.*', function ($event, $models) {
            if (Str::contains($event, 'eloquent.retrieved')) {
                foreach (array_filter($models) as $model) {
                    $class = get_class($model);
                    $this->models[$class] = ($this->models[$class] ?? 0) + 1;
                }
            }
        });
    }

    public function collect(): array
    {
        $messages = [];
        $now = now()->toDateTimeString();
        foreach ($this->models as $model => $count) {
            $messages[] = [
                'pretty_time' => $now,
                'type' => 'models',
                'message' => "${model} : ${count}",
            ];
        }

        return $messages;
    }
}
