<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects;

/**
 * NVGAMCTRL (0xE1) — negative-polarity gamma correction.
 * Identical structure to PVGAMCTRL; only the default tuning values differ.
 */
readonly class ST7789GammaNegative extends ST7789GammaPositive
{
    public function __construct(
        int $v0_v63 = 0xD0,
        int $v1 = 0x04,
        int $v2 = 0x0C,
        int $v4 = 0x11,
        int $v6 = 0x13,
        int $v13_j0 = 0x2C,
        int $v20 = 0x3F,
        int $v27_v36 = 0x44,
        int $v43 = 0x51,
        int $v50_j1 = 0x2F,
        int $v57 = 0x1F,
        int $v59 = 0x1F,
        int $v61 = 0x20,
        int $v62 = 0x23,
    ) {
        parent::__construct(
            $v0_v63,
            $v1,
            $v2,
            $v4,
            $v6,
            $v13_j0,
            $v20,
            $v27_v36,
            $v43,
            $v50_j1,
            $v57,
            $v59,
            $v61,
            $v62,
        );
    }
}
