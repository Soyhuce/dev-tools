<?php

namespace Soyhuce\DevTools\Debug\Entries;

class Counter extends Entry
{
    private int $count = 0;

    public function increment(int $value = 1): void
    {
        $this->count += $value;
        $this->updateMicroTime();
    }

    protected function message(): string
    {
        return sprintf('%s -> %s', $this->message, $this->count);
    }
}
