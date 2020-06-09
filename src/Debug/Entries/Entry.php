<?php

namespace Soyhuce\DevTools\Debug\Entries;

use Illuminate\Support\Carbon;

class Entry
{
    private float $microTime;

    private string $source;

    private string $message;

    public function __construct(string $source, string $content)
    {
        $this->microTime = microtime(true) * 1000;
        $this->source = $source;
        $this->message = $content;
    }

    public function getMicroTime(): float
    {
        return $this->microTime;
    }

    public function __toString(): string
    {
        return $this->header() . $this->message();
    }

    protected function header(): string
    {
        return sprintf(
            '=> [%s] %s : ',
            Carbon::createFromTimestampMs($this->microTime)->format(config('dev-tools.debugger.datetime_format')),
            $this->source
        );
    }

    protected function message(): string
    {
        return $this->message;
    }
}
