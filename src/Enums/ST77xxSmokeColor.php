<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\Enums;

/**
 * RGB565 solid colors for the st77xx-smoke sketch.
 */
enum ST77xxSmokeColor: int
{
    case RED = 0xF800;
    case GREEN = 0x07E0;
    case BLUE = 0x001F;
    case WHITE = 0xFFFF;
    case BLACK = 0x0000;

    /**
     * @return list<self>
     */
    public static function cycle(): array
    {
        return [
            self::RED,
            self::GREEN,
            self::BLUE,
            self::WHITE,
            self::BLACK,
        ];
    }
}
