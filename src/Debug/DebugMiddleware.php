<?php

namespace Soyhuce\DevTools\Debug;

use Closure;
use Illuminate\Testing\TestResponse;

class DebugMiddleware
{
    public function __construct(protected DebugManager $debugManager)
    {
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function handle($request, Closure $next): mixed
    {
        if (app()->environment('testing')) {
            $this->debugManager->resetCollectors();
        }
        $this->debugManager->request($request);

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Testing\TestResponse|\Symfony\Component\HttpFoundation\Response $response
     */
    public function terminate($request, $response): void
    {
        if ($response instanceof TestResponse) {
            $response = $response->baseResponse;
        }

        $this->debugManager->response($response);
    }
}
