<?php

namespace Soyhuce\DevTools\Debug\Entries;

class Counter extends Entry
{
    private int $counter = 0;

    public function increment(int $count = 1): void
    {
        $this->counter += $count;
        $this->updateMicroTime();
    }

    protected function message(): string
    {
        return sprintf('%s -> %s', $this->message, $this->counter);
    }
}
