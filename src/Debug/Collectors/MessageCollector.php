<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Soyhuce\DevTools\Debug\Entries\Entry;

class MessageCollector extends DataCollector
{
    /** @var array<Entry> */
    private array $messages = [];

    public function getName(): string
    {
        return 'message';
    }

    public function reset(): void
    {
        $this->messages = [];
    }

    public function collect(): array
    {
        return $this->messages;
    }

    public function addMessage(string $message): void
    {
        $this->messages[] = new Entry($this->getName(), $message);
    }
}
