<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

/**
 * APA[2:0] — drive current of the operational amplifier itself (bits 2:0 of PWCTR3 byte 1).
 */
enum ST7735OperationalAmplifierCurrent: int
{
    case OFF = 0x00;
    case SMALL = 0x01;
    case MEDIUM_LOW = 0x02;
    case MEDIUM = 0x03;
    case MEDIUM_HIGH = 0x04;
    case LARGE = 0x05;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
