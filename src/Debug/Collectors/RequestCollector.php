<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Soyhuce\DevTools\Debug\Collectors\Concerns\RegistersDebugMiddleware;

/**
 * Class RequestCollector
 */
class RequestCollector extends DataCollector
{
    use RegistersDebugMiddleware;

    private $data;

    public function getName(): string
    {
        return 'request';
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.request.enabled');
    }

    public function collect(): array
    {
        return $this->data
            ? [
                [
                    'time' => $this->data['time'],
                    'pretty_time' => Carbon::createFromTimestamp((int) $this->data['time'])->toDateTimeString(),
                    'message' => $this->getMessage(),
                    'type' => $this->getName(),
                ],
            ]
            : [];
    }

    public function setRequest(Request $request)
    {
        $this->data = [
            'time' => $this->time(),
            'url' => $request->url(),
            'method' => $request->method(),
            'params' => $request->all(),
        ];
    }

    private function getMessage()
    {
        $message = sprintf('%s %s ', $this->data['method'], $this->data['url']);
        if ($this->data['params']) {
            $message .= PHP_EOL . json_encode($this->data['params']);
        }

        return $message;
    }
}
