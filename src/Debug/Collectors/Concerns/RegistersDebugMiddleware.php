<?php

namespace Soyhuce\DevTools\Debug\Collectors\Concerns;

use Soyhuce\DevTools\Debug\DebugMiddleware;

trait RegistersDebugMiddleware
{
    public function boot(): void
    {
        $httpKernel = app(\Illuminate\Contracts\Http\Kernel::class);

        if (!method_exists($httpKernel, 'pushMiddleware')) {
            return;
        }

        $httpKernel->pushMiddleware(DebugMiddleware::class);
    }
}
