<?php

namespace Soyhuce\DevTools\Test\Unit\Faker;

use Soyhuce\DevTools\Faker\ColorUtils;
use Soyhuce\DevTools\Test\TestCase;

/**
 * @covers \Soyhuce\DevTools\Faker\ColorUtils
 */
class ColorUtilsTest extends TestCase
{
    /**
     * @test
     */
    public function getComplementaryColorForWhite(): void
    {
        $this->assertEquals([0, 0, 0], ColorUtils::getComplementaryColor([255, 255, 255]));
    }
}
