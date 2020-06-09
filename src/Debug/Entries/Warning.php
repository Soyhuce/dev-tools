<?php

namespace Soyhuce\DevTools\Debug\Entries;

class Warning
{
    private string $source;

    private string $message;

    public function __construct(string $source, string $content)
    {
        $this->source = $source;
        $this->message = $content;
    }

    public function __toString(): string
    {
        return $this->header() . $this->message();
    }

    protected function header(): string
    {
        return sprintf(
            '!! %s : ',
            $this->source
        );
    }

    protected function message(): string
    {
        return $this->message;
    }
}
