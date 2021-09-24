<?php

namespace Soyhuce\DevTools\Debug;

use Symfony\Component\VarDumper\VarDumper;

trait DefinesHelpers
{
    /**
     * @return mixed
     */
    public function measuring(string $name, callable $callable)
    {
        $this->startMeasure($name);
        $result = $callable();
        $this->stopMeasure($name);

        return $result;
    }

    public function dd(...$vars): void
    {
        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        $this->log();

        exit(1);
    }
}
