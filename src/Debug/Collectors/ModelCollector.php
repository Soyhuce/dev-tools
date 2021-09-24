<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;
use function count;

class ModelCollector extends CounterCollector
{
    public function __construct(
        private Dispatcher $events,
    ) {
    }

    public function getName(): string
    {
        return 'model';
    }

    public function boot(): void
    {
        $this->events->listen('eloquent.retrieved: *', function (string $event, array $models): void {
            $this->increment(
                Str::after($event, 'eloquent.retrieved: '),
                count($models)
            );
        });
    }
}
