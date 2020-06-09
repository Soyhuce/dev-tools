<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Soyhuce\DevTools\Debug\Entries\Counter;

class CounterCollector extends DataCollector
{
    /** @var array<\Soyhuce\DevTools\Debug\Entries\Counter> */
    private array $counters = [];

    public function getName(): string
    {
        return 'counter';
    }

    public function collect(): array
    {
        return array_values($this->counters);
    }

    public function increment(string $counter, int $count = 1): void
    {
        $this->getCounter($counter)->increment($count);
    }

    private function getCounter(string $counterName): Counter
    {
        return $this->counters[$counterName] ??= new Counter($this->getName(), $counterName);
    }
}
