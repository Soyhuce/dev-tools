<?php

namespace Soyhuce\DevTools\Debug\Warnings;

use Stringable;

class Warning implements Stringable
{
    public function __construct(
        private readonly string $source,
        private readonly string $message,
    ) {
    }

    public function __toString(): string
    {
        return $this->header() . $this->message();
    }

    protected function header(): string
    {
        return "{$this->source} : ";
    }

    protected function message(): string
    {
        return $this->message;
    }
}
