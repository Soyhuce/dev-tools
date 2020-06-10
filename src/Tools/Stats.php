<?php

namespace Soyhuce\DevTools\Tools;

class Stats
{
    private array $series;

    private array $cache = [];

    /** @var int|float|null */
    private $min;

    /** @var int|float */
    private $max;

    public function __construct(array $series)
    {
        $this->series = $series;
    }

    public function sum()
    {
        return $this->cache['sum'] ??= array_sum($this->series);
    }

    public function count()
    {
        return $this->cache['count'] ??= count($this->series);
    }

    public function max()
    {
        return $this->cache['max'] ??= max($this->series);
    }

    public function min()
    {
        return $this->cache['min'] ??= min($this->series);
    }

    public function avg()
    {
        return $this->cache['avg'] ??= $this->sum() / $this->count();
    }

    public function std()
    {
        return $this->cache['std'] ??= $this->computeStd();
    }
    
    private function computeStd(): float
    {
        $squares = array_reduce(
            $this->series,
            fn($sum, $item) => $sum + ($this->avg() - $item) ** 2,
            0
        );

        return sqrt($squares / $this->count());
    }
}
