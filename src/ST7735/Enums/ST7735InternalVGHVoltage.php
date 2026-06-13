<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

/**
 * VGH25[1:0] — internal VGH reference voltage (bits 7:6 of PWCTR2).
 * Applied when VCI = 2.5V.
 */
enum ST7735InternalVGHVoltage: int
{
    case V2_1 = 0x00;
    case V2_2 = 0x01;
    case V2_3 = 0x02;
    case V2_4 = 0x03;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
