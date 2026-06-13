<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

/**
 * GVDD reference voltage — VRHP[5:0] split across 0xC0 bytes 1 and 3.
 * VRHP[4:0] → byte 1 bits [4:0]. VRHP[5] → byte 3 bit 0.
 */
enum ST7735GateDriveVoltage: int
{
    case V4_70 = 0x00;
    case V4_65 = 0x01;
    case V4_60 = 0x02;
    case V4_55 = 0x03;
    case V4_50 = 0x04;
    case V4_45 = 0x05;
    case V4_40 = 0x06;
    case V4_35 = 0x07;
    case V4_30 = 0x08;
    case V4_25 = 0x09;
    case V4_20 = 0x0A;
    case V4_15 = 0x0B;
    case V4_10 = 0x0C;
    case V4_05 = 0x0D;
    case V4_00 = 0x0E;
    case V3_95 = 0x0F;
    case V3_90 = 0x10;
    case V3_85 = 0x11;
    case V3_80 = 0x12;
    case V3_75 = 0x13;
    case V3_70 = 0x14;
    case V3_65 = 0x15;
    case V3_60 = 0x16;
    case V3_55 = 0x17;
    case V3_50 = 0x18;
    case V3_45 = 0x19;
    case V3_40 = 0x1A;
    case V3_35 = 0x1B;
    case V3_30 = 0x1C;
    case V3_25 = 0x1D;
    case V3_20 = 0x1E;
    case V3_15 = 0x1F;
    case V5_00 = 0x20;
    case V4_95 = 0x21;
    case V4_90 = 0x22;
    case V4_85 = 0x23;
    case V4_80 = 0x24;
    case V4_75 = 0x25;
    case RESERVED_26 = 0x26;
    case RESERVED_27 = 0x27;
    case RESERVED_28 = 0x28;
    case RESERVED_29 = 0x29;
    case RESERVED_2A = 0x2A;
    case RESERVED_2B = 0x2B;
    case RESERVED_2C = 0x2C;
    case RESERVED_2D = 0x2D;
    case RESERVED_2E = 0x2E;
    case RESERVED_2F = 0x2F;
    case RESERVED_30 = 0x30;
    case RESERVED_31 = 0x31;
    case RESERVED_32 = 0x32;
    case RESERVED_33 = 0x33;
    case RESERVED_34 = 0x34;
    case RESERVED_35 = 0x35;
    case RESERVED_36 = 0x36;
    case RESERVED_37 = 0x37;
    case RESERVED_38 = 0x38;
    case RESERVED_39 = 0x39;
    case RESERVED_3A = 0x3A;
    case RESERVED_3B = 0x3B;
    case RESERVED_3C = 0x3C;
    case RESERVED_3D = 0x3D;
    case RESERVED_3E = 0x3E;
    case RESERVED_3F = 0x3F;

    public function vrhp5(): int
    {
        return $this->value >= 0x20 ? 1 : 0;
    }

    public function vrhp4to0(): int
    {
        return $this->value & 0x1F;
    }

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
