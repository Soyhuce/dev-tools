<?php

namespace Soyhuce\DevTools\Faker;

use Intervention\Image\Gd\Font;
use Intervention\Image\Image as InterventionImage;
use Soyhuce\DevTools\Faker\Utils\ColorUtils;

/**
 * Class Image
 */
class Image
{
    public static function generate($width = 640, $height = 640, $text = null, $encoding = 'jpg')
    {
        if (!class_exists('Intervention\Image\Image')) {
            throw new \Exception('package intervention/image is required to use Image::generate');
        }

        $backgroundColor = static::generateRandomColor();
        $img = InterventionImage::canvas($width, $height, $backgroundColor);
        $fontColor = self::getComplementaryColor($backgroundColor);
        $text = $text ?? $width . 'x' . $height;
        $fontSize = $width / mb_strlen($text);

        $img->text(
            $text,
            $width / 2,
            $height / 2,
            static function (Font $font) use ($fontSize, $fontColor) {
                $font->file(__DIR__ . '/../../assets/CamingoCode-Italic.ttf');
                $font->size($fontSize);
                $font->align('center');
                $font->valign('middle');
                $font->color($fontColor);
            }
        );
        $img->encode($encoding);

        return $img;
    }

    private static function generateRandomColor()
    {
        return [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)];
    }

    private static function getComplementaryColor($rgb)
    {
        $hsv = ColorUtils::rgbtohsv($rgb);

        [$h, $s, $v] = $hsv;

        $hp = ($h * 360 + 180) % 360 / 360;
        $vp = ($v * ($s - 1) + 1);
        $sp = ($v * $s) / $vp;

        return ColorUtils::hsvtorgb([$hp, $sp, $vp]);
    }
}
