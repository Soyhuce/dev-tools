<?php

namespace Soyhuce\DevTools\Debug;

use Symfony\Component\VarDumper\VarDumper;

trait DefinesHelpers
{
    /**
     * @param string $name
     * @param callable $callable
     * @return mixed
     */
    public function measuring(string $name, callable $callable)
    {
        $this->startMeasure($name);
        $result = $callable();
        $this->stopMeasure($name);

        return $result;
    }

    function dd(...$vars): void
    {
        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        $this->log();

        exit(1);
    }
}
