<?php

namespace Soyhuce\DevTools\Faker;

use Exception;
use Intervention\Image\Image as InterventionImage;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;

class Image
{
    public static function generate(
        int $width = 640,
        int $height = 640,
        ?string $text = null,
        string $encoding = 'jpg',
    ): InterventionImage {
        if (!class_exists(InterventionImage::class)) {
            throw new Exception('package intervention/image is required to use Image::generate');
        }

        $backgroundColor = static::generateRandomColor();
        $fontColor = ColorUtils::getComplementaryColor($backgroundColor);
        $img = ImageManager::create($width, $height)->fill($backgroundColor);
        $text ??= $width . 'x' . $height;
        $fontSize = (int) ($width / mb_strlen($text));

        $img->text(
            $text,
            (int) ($width / 2),
            (int) ($height / 2),
            function (FontFactory $font) use ($fontSize, $fontColor): void {
                $font->file(__DIR__ . '/../../assets/CamingoCode-Italic.ttf');
                $font->size($fontSize);
                $font->align('center');
                $font->valign('middle');
                $font->color($fontColor);
            }
        );
        $img->encodeByExtension($encoding);

        return $img;
    }

    /**
     * @static
     * @return array{int, int, int}
     */
    private static function generateRandomColor(): array
    {
        return [random_int(0, 255), random_int(0, 255), random_int(0, 255)];
    }
}
