<?php

namespace Soyhuce\DevTools\Middleware;

use Closure;

class BottleneckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if config onlyAjax is set at true
        if (config('bottleneck.only_ajax')) {
            //simulate a bottleneck with a usleep only on ajax requests
            if ($request->ajax()) {
                usleep(config('dev-tools.bottleneck.duration') * 1000);
            }
        } else {
            //simulate a bottleneck for all requests
            usleep(config('dev-tools.bottleneck.duration') * 1000);
        }

        return $next($request);
    }
}
