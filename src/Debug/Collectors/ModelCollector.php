<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;

class ModelCollector extends CounterCollector
{
    private Dispatcher $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function getName(): string
    {
        return 'model';
    }

    public function boot(): void
    {
        $this->events->listen('eloquent.retrieved: *', function (string $event, array $models) {
            $this->increment(
                Str::after($event, 'eloquent.retrieved: '),
                count($models)
            );
        });
    }
}
