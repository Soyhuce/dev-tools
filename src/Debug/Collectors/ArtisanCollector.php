<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Contracts\Events\Dispatcher;
use Soyhuce\DevTools\Debug\Entries\Entry;

class ArtisanCollector extends DataCollector
{
    private ?Entry $entry = null;

    public function __construct(
        private Dispatcher $events,
    ) {
    }

    public function getName(): string
    {
        return 'artisan';
    }

    public function boot(): void
    {
        $this->events->listen(CommandStarting::class, function (CommandStarting $commandStarting): void {
            $this->entry = new Entry($this->getName(), (string) $commandStarting->input);
        });
    }

    public function reset(): void
    {
        $this->entry = null;
    }

    public function collect(): array
    {
        if ($this->entry === null) {
            return [];
        }

        return [$this->entry];
    }
}
