<?php

namespace Soyhuce\DevTools\Middlewares;

use Closure;

class BottleneckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->sleep($request);

        return $next($request);
    }

    private function sleep($request): void
    {
        if (config('dev-tools.bottleneck.only_ajax') && !$request->ajax()) {
            return;
        }

        usleep(config('dev-tools.bottleneck.duration') * 1000);
    }
}
