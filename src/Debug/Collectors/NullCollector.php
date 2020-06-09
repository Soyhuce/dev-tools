<?php

namespace Soyhuce\DevTools\Debug\Collectors;

class NullCollector extends DataCollector
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function collect(): array
    {
        return [];
    }

    public function __call($name, $arguments)
    {
    }
}
