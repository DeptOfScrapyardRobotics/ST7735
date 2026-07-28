<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts;

/**
 * GMCTRN1 (0xE1) — negative-polarity gamma correction.
 * Identical structure to GMCTRP1; only the default tuning values differ.
 */
readonly class ST7735GammaNegative extends ST7735GammaPositive
{
    public function __construct(
        int $vrf0 = 0x03,
        int $vos0 = 0x1D,
        int $pk0 = 0x07,
        int $pk1 = 0x06,
        int $pk2 = 0x2E,
        int $pk3 = 0x2C,
        int $pk4 = 0x29,
        int $pk5 = 0x2D,
        int $pk6 = 0x2E,
        int $pk7 = 0x2E,
        int $pk8 = 0x37,
        int $pk9 = 0x3F,
        int $selv0 = 0x00,
        int $selv1 = 0x00,
        int $selv62 = 0x02,
        int $selv63 = 0x10,
    ) {
        parent::__construct(
            $vrf0,
            $vos0,
            $pk0,
            $pk1,
            $pk2,
            $pk3,
            $pk4,
            $pk5,
            $pk6,
            $pk7,
            $pk8,
            $pk9,
            $selv0,
            $selv1,
            $selv62,
            $selv63,
        );
    }
}
