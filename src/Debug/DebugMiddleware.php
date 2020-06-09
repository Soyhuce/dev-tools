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
        return $next($request);
    }

    public function terminate($request, $response): void
    {
        $this->debugManager->request($request);
        $this->debugManager->response($response);
    }
}
