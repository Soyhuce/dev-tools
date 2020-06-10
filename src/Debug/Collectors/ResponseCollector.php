<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Soyhuce\DevTools\Debug\Entries\Entry;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseCollector extends DataCollector
{
    private ?Entry $entry = null;

    public function getName(): string
    {
        return 'response';
    }

    public function reset(): void
    {
        $this->entry = null;
    }

    public function collect(): array
    {
        if ($this->entry === null) {
            return [];
        }

        return [$this->entry];
    }

    public function setResponse(Response $response): void
    {
        $this->entry = new Entry(
            $this->getName(),
            "{$response->getStatusCode()} -> {$this->getContent($response)}"
        );
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

        return PHP_EOL . $response->getContent();
    }
}
