<?php

namespace Soyhuce\DevTools\Tools;

class Time
{
    public static function humanizeMilliseconds(float $milliseconds): string
    {
        if ($milliseconds < 1) {
            return round($milliseconds * 1000, 2) . 'μs';
        }
        if ($milliseconds >= 1000) {
            return round($milliseconds / 1000, 2) . 's';
        }

        return round($milliseconds, 2) . 'ms';
    }
}
