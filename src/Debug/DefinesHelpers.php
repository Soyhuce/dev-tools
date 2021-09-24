<?php

namespace Soyhuce\DevTools\Debug;

use Symfony\Component\VarDumper\VarDumper;

trait DefinesHelpers
{
    public function measuring(string $name, callable $callable): mixed
    {
        $this->startMeasure($name);
        $result = $callable();
        $this->stopMeasure($name);

        return $result;
    }

    public function dd(mixed ...$vars): void
    {
        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        $this->log();

        exit(1);
    }
}
