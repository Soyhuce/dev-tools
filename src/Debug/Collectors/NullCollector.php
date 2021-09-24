<?php

namespace Soyhuce\DevTools\Debug\Collectors;

class NullCollector extends DataCollector
{
    public function getName(): string
    {
        return 'null';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function reset(): void
    {
    }

    public function collect(): array
    {
        return [];
    }

    public function __call($name, $arguments): void
    {
    }
}
