<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums;

/**
 * VGLS[2:0] — gate driver low voltage (bits 2:0 of GCTRL, 0xB7).
 */
enum ST7789GateLowVoltage: int
{
    case VN7_16 = 0x00;
    case VN7_67 = 0x01;
    case VN8_23 = 0x02;
    case VN8_87 = 0x03;
    case VN9_6 = 0x04;
    case VN10_43 = 0x05;
    case VN11_38 = 0x06;
    case VN12_5 = 0x07;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
