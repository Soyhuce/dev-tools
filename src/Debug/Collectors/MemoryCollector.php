<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Soyhuce\DevTools\Debug\Entries\Entry;
use Soyhuce\DevTools\Debug\Warnings\MemoryExceeded;
use Soyhuce\DevTools\Tools\Memory;

class MemoryCollector extends DataCollector
{
    private ?int $memoryPeak = null;

    public function getName(): string
    {
        return 'memory';
    }

    public function reset(): void
    {
    }

    public function collect(): array
    {
        return [
            new Entry($this->getName(), Memory::humanize($this->getMemoryPeak())),
        ];
    }

    public function warnings(): array
    {
        $max = config('dev-tools.debugger.memory.max');

        if ($max === null) {
            return [];
        }

        $max = Memory::toBytes($max);
        if ($this->getMemoryPeak() <= $max) {
            return [];
        }

        return [
            new MemoryExceeded($this->getName(), $max, $this->getMemoryPeak()),
        ];
    }

    private function getMemoryPeak(): int
    {
        if ($this->memoryPeak === null) {
            $this->memoryPeak = memory_get_peak_usage(false);
        }

        return $this->memoryPeak;
    }
}
