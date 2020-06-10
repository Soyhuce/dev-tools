<?php

namespace Soyhuce\DevTools\Debug;

use Closure;

class DebugMiddleware
{
    protected DebugManager $debugManager;

    public function __construct(DebugManager $debugManager)
    {
        $this->debugManager = $debugManager;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app()->environment('testing')) {
            $this->debugManager->resetCollectors();
        }
        $this->debugManager->request($request);

        return $next($request);
    }

    public function terminate($request, $response): void
    {
        $this->debugManager->response($response);
    }
}
