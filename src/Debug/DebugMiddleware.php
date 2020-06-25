<?php

namespace Soyhuce\DevTools\Debug;

use Closure;
use Illuminate\Testing\TestResponse;

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
        if ($response instanceof TestResponse) {
            $response = $response->baseResponse;
        }

        $this->debugManager->response($response);
    }
}
