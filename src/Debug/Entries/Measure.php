<?php

namespace Soyhuce\DevTools\Debug\Entries;

use LogicException;
use Soyhuce\DevTools\Tools\Stats;
use Soyhuce\DevTools\Tools\Time;
use function count;

class Measure extends Entry
{
    private ?float $startedAt = null;

    /**
     * @param array<float> $measures
     */
    public function __construct(
        string $source,
        string $message,
        private array $measures = [],
    ) {
        parent::__construct($source, $message);
    }

    public function isRunning(): bool
    {
        return $this->startedAt !== null;
    }

    public function start(): void
    {
        if ($this->isRunning()) {
            throw new LogicException("A measure {$this->message} is already started");
        }

        $this->startedAt = microtime(true);
    }

    public function stop(): void
    {
        if (!$this->isRunning()) {
            throw new LogicException("A measure {$this->message} is not started");
        }

        $this->updateMicroTime();
        $this->measures[] = microtime(true) - $this->startedAt;
        $this->startedAt = null;
    }

    public function lastMeasure(): float
    {
        return last($this->measures);
    }

    protected function message(): string
    {
        if (count($this->measures) === 1) {
            return $this->singleMeasureMessage();
        }

        return $this->multipleMeasuresMessage();
    }

    private function singleMeasureMessage(): string
    {
        return sprintf(
            '%s -> %s',
            $this->message,
            Time::humanizeSeconds($this->measures[0])
        );
    }

    private function multipleMeasuresMessage(): string
    {
        $stats = new Stats($this->measures);

        return sprintf(
            '%s -> %s cumulated on %s entries (avg : %s - min : %s - max : %s - std : %s)',
            $this->message,
            Time::humanizeSeconds($stats->sum()),
            $stats->count(),
            Time::humanizeSeconds($stats->avg()),
            Time::humanizeSeconds($stats->min()),
            Time::humanizeSeconds($stats->max()),
            Time::humanizeSeconds($stats->std())
        );
    }
}
