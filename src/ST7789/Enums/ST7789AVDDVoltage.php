<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums;

/**
 * AVDD[1:0] — analog positive supply voltage (bits 7:6 of PWCTRL1 byte 2, 0xD0).
 */
enum ST7789AVDDVoltage: int
{
    case V6_4 = 0x00;
    case V6_6 = 0x01;
    case V6_8 = 0x02;
    case RESERVED = 0x03;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
