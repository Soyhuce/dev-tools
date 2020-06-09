<?php

namespace Soyhuce\DevTools\Debug;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * Class LogDebugger
 */
class DebuggerMiddleware
{
    /**
     * The App container
     *
     * @var Container
     */
    protected $container;

    /**
     * The DebugBar instance
     *
     * @var DebugManager
     */
    protected $debugManager;

    /**
     * Create a new middleware instance.
     *
     * @param Container $container
     * @param DebugManager $debugManager
     */
    public function __construct(Container $container, DebugManager $debugManager)
    {
        $this->container = $container;
        $this->debugManager = $debugManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        if (!$this->debugManager->isEnabled()) {
            return $next($request);
        }

        $this->debugManager->boot();
        $this->debugManager->request($request);

        try {
            /** @var \Illuminate\Http\Response $response */
            $response = $next($request);
        } catch (\Exception $e) {
            $response = $this->handleException($request, $e);
        }

        $this->debugManager->response($response);
        $this->debugManager->log();

        return $response;
    }

    /**
     * Handle the given exception.
     *
     * (Copy from Illuminate\Routing\Pipeline by Taylor Otwell)
     *
     * @param $passable
     * @param Exception $e
     * @return mixed
     * @throws Exception
     */
    protected function handleException($passable, \Exception $e)
    {
        if (!$this->container->bound(ExceptionHandler::class) || !$passable instanceof \Illuminate\Http\Request) {
            throw $e;
        }

        $handler = $this->container->make(ExceptionHandler::class);

        $handler->report($e);

        return $handler->render($passable, $e);
    }
}
