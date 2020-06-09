<?php

namespace Soyhuce\DevTools\Debug\Collectors;

/**
 * Class Collector
 */
abstract class DataCollector
{
    abstract public function getName(): string;

    abstract public function isEnabled(): bool;

    public function boot()
    {
    }

    /**
     * @return array<\Soyhuce\DevTools\Debug\Entries\Entry>
     */
    abstract public function collect(): array;

    /**
     * @return array<\Soyhuce\DevTools\Debug\Entries\Warning>
     */
    public function warnings(): array
    {
        return [];
    }
}
