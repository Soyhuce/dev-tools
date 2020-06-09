<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Http\Request;
use Soyhuce\DevTools\Debug\Collectors\Concerns\RegistersDebugMiddleware;
use Soyhuce\DevTools\Debug\Entries\Entry;

class RequestCollector extends DataCollector
{
    use RegistersDebugMiddleware;

    private ?Entry $entry = null;

    public function getName(): string
    {
        return 'request';
    }

    public function collect(): array
    {
        if ($this->entry === null) {
            return [];
        }

        return [$this->entry];
    }

    public function setRequest(Request $request)
    {
        $this->entry = new Entry(
            $this->getName(),
            $this->formatRequest($request)
        );
    }

    private function formatRequest(Request $request): string
    {
        $result = "{$request->method()} {$request->url()}";

        $parameters = $request->all();
        if ($parameters) {
            $result .= PHP_EOL . json_encode($parameters, JSON_PRETTY_PRINT);
        }

        return $result;
    }
}
