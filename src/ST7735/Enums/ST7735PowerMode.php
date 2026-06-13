<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

enum ST7735PowerMode: int
{
    case DOUBLE_X = 0x00;
    case RESERVED_01 = 0x01;
    case AUTO = 0x02;
    case RESERVED_03 = 0x03;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
