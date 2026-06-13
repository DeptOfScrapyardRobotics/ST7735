<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

/**
 * VGLSEL[1:0] — VGL gate driver low voltage (bits 3:2 of PWCTR2).
 */
enum ST7735VGLGateDriveVoltageLow: int
{
    case N7_5 = 0x00;
    case N10_0 = 0x01;
    case N12_5 = 0x02;
    case N13_0 = 0x03;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
