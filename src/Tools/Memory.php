<?php

namespace Soyhuce\DevTools\Tools;

class Memory
{
    public static function humanize(int $size, int $precision = 2): string
    {
        if ($size === 0) {
            return '0o';
        }

        $sign = $size < 0 ? '-' : '';
        $size = abs($size);

        $base = log($size) / log(1024);
        $suffixes = ['o', 'ko', 'Mo', 'Go', 'To'];

        return $sign . round(1024 ** ($base - floor($base)), $precision) . $suffixes[(int) floor($base)];
    }

    public static function toBytes(string $value): int
    {
        if (!$value) {
            return 0;
        }

        $number = (int) mb_substr($value, 0, -2);

        return match (mb_strtoupper(mb_substr($value, -2))) {
            'KB', 'KO' => $number * 1024,
            'MB', 'MO' => $number * 1024 ** 2,
            'GB', 'GO' => $number * 1024 ** 3,
            default => $number,
        };
    }
}
