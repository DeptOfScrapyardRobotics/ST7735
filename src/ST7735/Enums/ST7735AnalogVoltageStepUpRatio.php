<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

enum ST7735AnalogVoltageStepUpRatio: int
{
    case V4_5 = 0x00;
    case V4_6 = 0x01;
    case V4_7 = 0x02;
    case V4_8 = 0x03;
    case V4_9 = 0x04;
    case V5_0 = 0x05;
    case V5_1 = 0x06;
    case V5_2 = 0x07;

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
