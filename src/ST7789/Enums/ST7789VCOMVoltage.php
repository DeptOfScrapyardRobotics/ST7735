<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums;

/**
 * VCOMS[5:0] — VCOM voltage setting for VCOMS (0xBB), bits [5:0] of the single parameter byte.
 * Linear scale: 0.1V (0x00) up to 1.675V (0x3F) in 25mV steps.
 */
enum ST7789VCOMVoltage: int
{
    case V0_100 = 0x00;
    case V0_125 = 0x01;
    case V0_150 = 0x02;
    case V0_175 = 0x03;
    case V0_200 = 0x04;
    case V0_225 = 0x05;
    case V0_250 = 0x06;
    case V0_275 = 0x07;
    case V0_300 = 0x08;
    case V0_325 = 0x09;
    case V0_350 = 0x0A;
    case V0_375 = 0x0B;
    case V0_400 = 0x0C;
    case V0_425 = 0x0D;
    case V0_450 = 0x0E;
    case V0_475 = 0x0F;
    case V0_500 = 0x10;
    case V0_525 = 0x11;
    case V0_550 = 0x12;
    case V0_575 = 0x13;
    case V0_600 = 0x14;
    case V0_625 = 0x15;
    case V0_650 = 0x16;
    case V0_675 = 0x17;
    case V0_700 = 0x18;
    case V0_725 = 0x19;
    case V0_750 = 0x1A;
    case V0_775 = 0x1B;
    case V0_800 = 0x1C;
    case V0_825 = 0x1D;
    case V0_850 = 0x1E;
    case V0_875 = 0x1F;
    case V0_900 = 0x20;
    case V0_925 = 0x21;
    case V0_950 = 0x22;
    case V0_975 = 0x23;
    case V1_000 = 0x24;
    case V1_025 = 0x25;
    case V1_050 = 0x26;
    case V1_075 = 0x27;
    case V1_100 = 0x28;
    case V1_125 = 0x29;
    case V1_150 = 0x2A;
    case V1_175 = 0x2B;
    case V1_200 = 0x2C;
    case V1_225 = 0x2D;
    case V1_250 = 0x2E;
    case V1_275 = 0x2F;
    case V1_300 = 0x30;
    case V1_325 = 0x31;
    case V1_350 = 0x32;
    case V1_375 = 0x33;
    case V1_400 = 0x34;
    case V1_425 = 0x35;
    case V1_450 = 0x36;
    case V1_475 = 0x37;
    case V1_500 = 0x38;
    case V1_525 = 0x39;
    case V1_550 = 0x3A;
    case V1_575 = 0x3B;
    case V1_600 = 0x3C;
    case V1_625 = 0x3D;
    case V1_650 = 0x3E;
    case V1_675 = 0x3F;

    public function volts(): float
    {
        return round(0.1 + 0.025 * $this->value, 3);
    }

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
