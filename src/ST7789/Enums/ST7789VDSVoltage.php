<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums;

/**
 * VDS[1:0] — VDS regulator output voltage (bits 1:0 of PWCTRL1 byte 2, 0xD0).
 */
enum ST7789VDSVoltage: int
{
    case V2_19 = 0x00;
    case V2_3 = 0x01;
    case V2_4 = 0x02;
    case V2_51 = 0x03;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
