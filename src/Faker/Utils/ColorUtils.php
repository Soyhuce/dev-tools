<?php

namespace Soyhuce\DevTools\Faker\Utils;

/**
 * Class ColorUtils
 */
class ColorUtils
{
    public static function rgbtohsv(array $rgb)
    {
        // HSV Results:Number 0-1

        $r = ($rgb[0] / 255);
        $g = ($rgb[1] / 255);
        $b = ($rgb[2] / 255);

        $minRGB = min($r, $g, $b);
        $maxRGB = max($r, $g, $b);
        $delta = $maxRGB - $minRGB;

        $v = $maxRGB;

        if ($delta == 0) {
            $h = 0;
            $s = 0;
        } else {
            $s = $delta / $maxRGB;

            $deltaR = ((($maxRGB - $r) / 6) + ($delta / 2)) / $delta;
            $deltaG = ((($maxRGB - $g) / 6) + ($delta / 2)) / $delta;
            $deltaB = ((($maxRGB - $b) / 6) + ($delta / 2)) / $delta;

            if ($r == $maxRGB) {
                $h = $deltaB - $deltaG;
            } elseif ($g == $maxRGB) {
                $h = (1 / 3) + $deltaR - $deltaB;
            } else { // ($b == $maxRGB)
                $h = (2 / 3) + $deltaG - $deltaR;
            }

            if ($h < 0) {
                $h++;
            }
            if ($h > 1) {
                $h--;
            }
        }

        return [$h, $s, $v];
    }

    public static function hsvtorgb(array $hsv)
    {
        $h = $hsv[0];
        $s = $hsv[1];
        $v = $hsv[2];

        if ($s == 0) {
            $r = $v;
            $g = $v;
            $b = $v;
        } else {
            $h = $h * 6;
            $i = floor($h);
            $x = $v * (1 - $s);
            $y = $v * (1 - $s * ($h - $i));
            $z = $v * (1 - $s * (1 - ($h - $i)));

            switch ($i) {
                case 0:
                    $r = $v;
                    $g = $z;
                    $b = $x;

                    break;
                case 1:
                    $r = $y;
                    $g = $v;
                    $b = $x;

                    break;
                case 2:
                    $r = $x;
                    $g = $v;
                    $b = $z;

                    break;
                case 3:
                    $r = $x;
                    $g = $y;
                    $b = $v;

                    break;
                case 4:
                    $r = $z;
                    $g = $x;
                    $b = $v;

                    break;
                default:
                    $r = $v;
                    $g = $x;
                    $b = $y;

                    break;
            }
        }

        return [$r * 255, $g * 255, $b * 255];
    }
}
