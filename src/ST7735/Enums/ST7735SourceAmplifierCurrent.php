<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

/**
 * SAPA[2:0] — fixed current from source for the source driver op-amp (bits 5:3 of PWCTR3 byte 1).
 */
enum ST7735SourceAmplifierCurrent: int
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
