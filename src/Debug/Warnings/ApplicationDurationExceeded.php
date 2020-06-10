<?php

namespace Soyhuce\DevTools\Debug\Warnings;

use Soyhuce\DevTools\Tools\Time;

class ApplicationDurationExceeded extends Warning
{
    public function __construct(string $source, float $max, float $actual)
    {
        parent::__construct(
            $source,
            sprintf(
                "Application duration exceeded max %s allowed : %s",
                Time::humanizeSeconds($max),
                Time::humanizeSeconds($actual)
            )
        );
    }
}
