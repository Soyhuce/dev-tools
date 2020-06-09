<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ResponseCollector
 */
class ResponseCollector extends DataCollector
{
    /** @var array */
    private $data;

    /**
     * ResponseCollector constructor.
     */
    public function __construct()
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'response';
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.response.enabled');
    }

    public function collect(): array
    {
        return $this->data
            ? [
                [
                    'time' => $this->data['time'],
                    'pretty_time' => Carbon::createFromTimestamp((int) $this->data['time'])->toDateTimeString(),
                    'message' => $this->data['status'] . ' -> ' . $this->data['content'],
                    'type' => $this->getName(),
                ],
            ]
            : [];
    }

    public function setResponse(Response $response)
    {
        $this->data = [
            'time' => $this->time(),
            'status' => $response->getStatusCode(),
            'content' => $this->getContent($response),
        ];
    }

    private function getContent(Response $response)
    {
        if ($response instanceof BinaryFileResponse) {
            return '[Binary data]';
        }
        if ($response instanceof StreamedResponse) {
            return '[Streamed data]';
        }
        if ($response instanceof RedirectResponse) {
            return 'redirect to ' . $response->getTargetUrl();
        }

        return $response->getContent();
    }
}
