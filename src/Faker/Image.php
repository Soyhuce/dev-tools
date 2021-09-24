<?php

namespace Soyhuce\DevTools\Faker;

use Exception;
use Intervention\Image\AbstractFont;
use Intervention\Image\Image as InterventionImage;
use Intervention\Image\ImageManagerStatic;

class Image
{
    public static function generate(
        int $width = 640,
        int $height = 640,
        ?string $text = null,
        string $encoding = 'jpg',
    ): InterventionImage {
        if (!class_exists('Intervention\Image\Image')) {
            throw new Exception('package intervention/image is required to use Image::generate');
        }

        $backgroundColor = static::generateRandomColor();
        $fontColor = ColorUtils::getComplementaryColor($backgroundColor);
        $img = ImageManagerStatic::canvas($width, $height, $backgroundColor);
        $text = $text ?? $width . 'x' . $height;
        $fontSize = (int) ($width / mb_strlen($text));

        $img->text(
            $text,
            (int) ($width / 2),
            (int) ($height / 2),
            static function (AbstractFont $font) use ($fontSize, $fontColor): void {
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

    /**
     * @static
     * @return array{int, int, int}
     */
    private static function generateRandomColor(): array
    {
        return [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)];
    }
}
