<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects;

/**
 * NGC (0xE1) — negative-polarity gamma correction.
 * Identical structure to PGC; only the default tuning values differ.
 */
readonly class ST7796GammaNegative extends ST7796GammaPositive
{
    public function __construct(
        int $v0_v63 = 0xE0,
        int $v1 = 0x09,
        int $v2 = 0x0B,
        int $v4 = 0x06,
        int $v6 = 0x04,
        int $v13_j0 = 0x03,
        int $v20 = 0x2B,
        int $v27_v36 = 0x43,
        int $v43 = 0x42,
        int $v50_j1 = 0x3B,
        int $v57 = 0x16,
        int $v59 = 0x14,
        int $v61 = 0x17,
        int $v62 = 0x1B,
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
