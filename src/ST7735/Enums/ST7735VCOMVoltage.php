<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

/**
 * VCOMS[5:0] — VCOM voltage setting for VMCTR1 (0xC5), bits [5:0] of the single parameter byte.
 * Linear scale: -0.425V (0x00) down to -2.0V (0x3F) in 25mV steps.
 */
enum ST7735VCOMVoltage: int
{
    case VN0_425 = 0x00;
    case VN0_450 = 0x01;
    case VN0_475 = 0x02;
    case VN0_500 = 0x03;
    case VN0_525 = 0x04;
    case VN0_550 = 0x05;
    case VN0_575 = 0x06;
    case VN0_600 = 0x07;
    case VN0_625 = 0x08;
    case VN0_650 = 0x09;
    case VN0_675 = 0x0A;
    case VN0_700 = 0x0B;
    case VN0_725 = 0x0C;
    case VN0_750 = 0x0D;
    case VN0_775 = 0x0E;
    case VN0_800 = 0x0F;
    case VN0_825 = 0x10;
    case VN0_850 = 0x11;
    case VN0_875 = 0x12;
    case VN0_900 = 0x13;
    case VN0_925 = 0x14;
    case VN0_950 = 0x15;
    case VN0_975 = 0x16;
    case VN1_000 = 0x17;
    case VN1_025 = 0x18;
    case VN1_050 = 0x19;
    case VN1_075 = 0x1A;
    case VN1_100 = 0x1B;
    case VN1_125 = 0x1C;
    case VN1_150 = 0x1D;
    case VN1_175 = 0x1E;
    case VN1_200 = 0x1F;
    case VN1_225 = 0x20;
    case VN1_250 = 0x21;
    case VN1_275 = 0x22;
    case VN1_300 = 0x23;
    case VN1_325 = 0x24;
    case VN1_350 = 0x25;
    case VN1_375 = 0x26;
    case VN1_400 = 0x27;
    case VN1_425 = 0x28;
    case VN1_450 = 0x29;
    case VN1_475 = 0x2A;
    case VN1_500 = 0x2B;
    case VN1_525 = 0x2C;
    case VN1_550 = 0x2D;
    case VN1_575 = 0x2E;
    case VN1_600 = 0x2F;
    case VN1_625 = 0x30;
    case VN1_650 = 0x31;
    case VN1_675 = 0x32;
    case VN1_700 = 0x33;
    case VN1_725 = 0x34;
    case VN1_750 = 0x35;
    case VN1_775 = 0x36;
    case VN1_800 = 0x37;
    case VN1_825 = 0x38;
    case VN1_850 = 0x39;
    case VN1_875 = 0x3A;
    case VN1_900 = 0x3B;
    case VN1_925 = 0x3C;
    case VN1_950 = 0x3D;
    case VN1_975 = 0x3E;
    case VN2_000 = 0x3F;

    public function volts(): float
    {
        return round(-1 * (0.425 + 0.025 * $this->value), 3);
    }

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
