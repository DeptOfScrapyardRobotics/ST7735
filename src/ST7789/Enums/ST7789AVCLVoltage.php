<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums;

/**
 * AVCL[1:0] — analog negative supply voltage (bits 5:4 of PWCTRL1 byte 2, 0xD0).
 */
enum ST7789AVCLVoltage: int
{
    case VN4_4 = 0x00;
    case VN4_6 = 0x01;
    case VN4_8 = 0x02;
    case VN5_0 = 0x03;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
