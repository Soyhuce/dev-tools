<?php

namespace Soyhuce\DevTools\Tools;

class Memory
{
    public static function humanize(int $size, int $precision = 2): string
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

    public static function toBytes(string $value): int
    {
        if (!$value) {
            return 0;
        }

        $number = (int) mb_substr($value, 0, -2);
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
