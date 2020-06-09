<?php

namespace Soyhuce\DevTools\Debug;

use Illuminate\Http\Request;
use Soyhuce\DevTools\Debug\Collectors\DataCollector;
use Symfony\Component\HttpFoundation\Response;

trait ForwardsCallsToCollectors
{
    private array $collectors = [];

    private function getCollector($name): DataCollector
    {
        return $this->collectors[$name];
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

    public function increment(string $name, int $count = 1): void
    {
        $this->getCollector('counter')->increment($name, $count);
    }
}
