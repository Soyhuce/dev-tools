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

    abstract public function collect(): array;

    public function warnings(): array
    {
        return [];
    }

    protected function time(): float
    {
        return microtime(true);
    }
}
