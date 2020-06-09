<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Support\Carbon;

/**
 * Class MemoryCollector
 */
class MemoryCollector extends DataCollector
{
    /** @var int|null */
    private $memoryPeak;

    /**
     * MemoryCollector constructor.
     */
    public function __construct()
    {
        $this->memoryPeak = null;
    }

    public function getName(): string
    {
        return 'memory';
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.memory.enabled');
    }

    public function collect(): array
    {
        $time = $this->time();

        return [
            [
                'time' => $time,
                'pretty_time' => Carbon::createFromTimestamp((int) $time)->toDateTimeString(),
                'message' => $this->formatBytes($this->getMemoryPeak()),
                'type' => $this->getName(),
            ],
        ];
    }

    public function warnings(): array
    {
        $max = $this->toBytes(config('dev-tools.debugger.memory.max'));
        if (!$max || $this->getMemoryPeak() <= $max) {
            return [];
        }

        return [
            [
                'message' => sprintf(
                    'Memory exceeded max %s allowed : %s',
                    $this->formatBytes($max),
                    $this->formatBytes($this->getMemoryPeak())
                ),
                'type' => $this->getName(),
            ],
        ];
    }

    private function getMemoryPeak(): int
    {
        if ($this->memoryPeak === null) {
            $this->memoryPeak = memory_get_peak_usage(false);
        }

        return $this->memoryPeak;
    }

    private function formatBytes($size, $precision = 2)
    {
        if ($size === 0 || $size === null) {
            return '0o';
        }

        $sign = $size < 0 ? '-' : '';
        $size = abs($size);

        $base = log($size) / log(1024);
        $suffixes = ['o', 'ko', 'Mo', 'Go', 'To'];

        return $sign . round(1024 ** ($base - floor($base)), $precision) . $suffixes[(int) floor($base)];
    }

    private function toBytes($value)
    {
        if (!$value) {
            return 0;
        }
        $number = mb_substr($value, 0, -2);
        switch (mb_strtoupper(mb_substr($value, -2))) {
            case 'KB':
            case 'KO':
                return $number * 1024;
            case 'MB':
            case 'MO':
                return $number * 1024 ** 2;
            case 'GB':
            case 'GO':
                return $number * 1024 ** 3;
            default:
                return $value;
        }
    }
}
