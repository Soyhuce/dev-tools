<?php

namespace Soyhuce\DevTools\Debug\Warnings;

class QueriesExceeded extends Warning
{
    public function __construct(string $source, int $max, int $actual)
    {
        parent::__construct(
            $source,
            "Number of queries exceeded max {$max} allowed : {$actual}"
        );
    }
}
