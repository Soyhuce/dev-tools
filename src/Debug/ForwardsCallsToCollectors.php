<?php

namespace Soyhuce\DevTools\Debug;

use Illuminate\Http\Request;
use Soyhuce\DevTools\Debug\Collectors\DataCollector;
use Soyhuce\DevTools\Debug\Collectors\NullCollector;
use Symfony\Component\HttpFoundation\Response;

trait ForwardsCallsToCollectors
{
    /** @var array<string, \Soyhuce\DevTools\Debug\Collectors\DataCollector> */
    private array $collectors = [];

    private function getCollector(string $name): DataCollector
    {
        $this->boot();

        return $this->collectors[$name] ??= new NullCollector();
    }

    public function message(string $message): void
    {
        $this->getCollector('message')->addMessage($message);
    }

    public function request(Request $request): void
    {
        $this->getCollector('request')->setRequest($request);
    }

    public function response(Response $response): void
    {
        $this->getCollector('response')->setResponse($response);
    }

    public function startMeasure(string $name): void
    {
        $this->getCollector('time')->startMeasure($name);
    }

    public function stopMeasure(string $name): void
    {
        $this->getCollector('time')->stopMeasure($name);
    }

    public function incrementCounter(string $name, int $value = 1): void
    {
        $this->getCollector('counter')->increment($name, $value);
    }
}
