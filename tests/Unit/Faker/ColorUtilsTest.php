<?php

use Soyhuce\DevTools\Faker\ColorUtils;

test('get complementary color for white', function (): void {
    expect(ColorUtils::getComplementaryColor([255, 255, 255]))->toEqual([0, 0, 0]);
});
