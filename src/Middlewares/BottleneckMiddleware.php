<?php

namespace Soyhuce\DevTools\Middlewares;

use Closure;

class BottleneckMiddleware
{
    /**
     * @param \Illuminate\Http\Request $request
     */
    public function handle($request, Closure $next): mixed
    {
        $this->sleep($request);

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    private function sleep($request): void
    {
        if (config('dev-tools.bottleneck.only_ajax') && !$request->ajax()) {
            return;
        }

        usleep((int) config('dev-tools.bottleneck.duration') * 1000);
    }
}
