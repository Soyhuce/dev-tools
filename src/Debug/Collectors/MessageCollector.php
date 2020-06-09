<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Support\Carbon;

/**
 * Class MessageCollector
 */
class MessageCollector extends DataCollector
{
    /** @var array */
    private $messages;

    public function __construct()
    {
        $this->messages = [];
    }

    public function getName(): string
    {
        return 'message';
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.message.enabled');
    }

    public function collect(): array
    {
        return collect($this->messages)->map(
            function ($message) {
                return [
                    'time' => $message['time'],
                    'pretty_time' => Carbon::createFromTimestamp((int) $message['time'])->toDateTimeString(),
                    'message' => $message['message'],
                    'type' => $this->getName(),
                ];
            }
        )->toArray();
    }

    public function addMessage(string $message)
    {
        $this->messages[] = [
            'time' => $this->time(),
            'message' => $message,
        ];
    }
}
