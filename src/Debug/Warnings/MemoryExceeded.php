<?php

namespace Soyhuce\DevTools\Debug\Warnings;

use Soyhuce\DevTools\Tools\Memory;

class MemoryExceeded extends Warning
{
    public function __construct(string $source, int $max, int $used)
    {
        parent::__construct(
            $source,
            sprintf(
                'Memory exceeded max %s allowed : %s',
                Memory::humanize($max),
                Memory::humanize($used)
            )
        );
    }
}
