<?php

namespace Soyhuce\DevTools\Debug\Warnings;

class Warning
{
    public function __construct(
        private string $source,
        private string $message,
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
