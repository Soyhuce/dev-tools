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

    /**
     * @param array<mixed> $arguments
     */
    public function __call(string $name, array $arguments): void
    {
    }
}
