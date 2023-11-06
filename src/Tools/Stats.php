<?php

namespace Soyhuce\DevTools\Tools;

use function count;

class Stats
{
    /** @var array<float|int> */
    private array $cache = [];

    /**
     * @param array<float|int> $series
     */
    public function __construct(
        private readonly array $series,
    ) {
    }

    public function sum(): int|float
    {
        return $this->cache['sum'] ??= array_sum($this->series);
    }

    public function count(): int|float
    {
        return $this->cache['count'] ??= count($this->series);
    }

    public function max(): int|float
    {
        return $this->cache['max'] ??= max($this->series);
    }

    public function min(): int|float
    {
        return $this->cache['min'] ??= min($this->series);
    }

    public function avg(): float
    {
        return $this->cache['avg'] ??= $this->sum() / $this->count();
    }

    public function std(): float
    {
        return $this->cache['std'] ??= $this->computeStd();
    }

    private function computeStd(): float
    {
        $squares = array_reduce(
            $this->series,
            fn ($sum, $item) => $sum + ($this->avg() - $item) ** 2,
            0
        );

        return sqrt($squares / $this->count());
    }
}
