<?php

namespace Soyhuce\DevTools\Debug\Collectors;

/**
 * Class Collector
 */
abstract class DataCollector
{
    abstract public function getName(): string;

    public function isEnabled(): bool
    {
        return (bool) config("dev-tools.debugger.{$this->getName()}.enabled");
    }

    public function boot(): void
    {
    }

    /**
     * @return array<\Soyhuce\DevTools\Debug\Entries\Entry>
     */
    abstract public function collect(): array;

    /**
     * @return array<\Soyhuce\DevTools\Debug\Warnings\Warning>
     */
    public function warnings(): array
    {
        return [];
    }
}
