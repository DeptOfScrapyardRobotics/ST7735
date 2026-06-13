<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

/**
 * VGHBT[1:0] — VGH gate driver high voltage formula (bits 1:0 of PWCTR2).
 */
enum ST7735VGHGateDriveVoltageHigh: int
{
    case DOUBLE_AVDD_PLUS_VGH25_MINUS_HALF = 0x00;
    case TRIPLE_AVDD_MINUS_HALF = 0x01;
    case TRIPLE_AVDD_PLUS_VGH25_MINUS_HALF = 0x02;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
