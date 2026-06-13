<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums;

/**
 * VGHS[2:0] — gate driver high voltage (bits 6:4 of GCTRL, 0xB7).
 */
enum ST7789GateHighVoltage: int
{
    case V12_2 = 0x00;
    case V12_54 = 0x01;
    case V12_89 = 0x02;
    case V13_26 = 0x03;
    case V13_65 = 0x04;
    case V14_06 = 0x05;
    case V14_5 = 0x06;
    case V14_97 = 0x07;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
