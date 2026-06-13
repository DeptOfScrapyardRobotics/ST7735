<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums;

enum ST7789OpCode: int
{
    case SOFTWARE_RESET = 0x01;
    case ENTER_SLEEP_MODE = 0x10;
    case EXIT_SLEEP_MODE = 0x11;
    case PARTIAL_MODE_ON = 0x12;
    case NORMAL_MODE_ON = 0x13;

    case DISPLAY_INVERSION_OFF = 0x20;
    case DISPLAY_INVERSION_ON = 0x21;

    case TOGGLE_DISPLAY_OFF = 0x28;
    case TOGGLE_DISPLAY_ON = 0x29;
    case SET_COLUMN_ADDRESS = 0x2A;
    case SET_ROW_ADDRESS = 0x2B;
    case WRITE_MEMORY_START = 0x2C;

    case MEMORY_ACCESS_CONTROL = 0x36;
    case SET_PIXEL_FORMAT = 0x3A;

    case PORCH_CONTROL = 0xB2;                          // Porch setting (+ 5 bytes: BPA, FPA, PSEN, BPB/FPB, BPC/FPC)
    case GATE_CONTROL = 0xB7;                           // Gate control (+ 1 byte: VGHS, VGLS)

    case VCOM_SETTING = 0xBB;                           // VCOM setting (+ 1 byte: VCOMS)

    case LCM_CONTROL = 0xC0;                            // LCM control (+ 1 byte)
    case VDV_VRH_COMMAND_ENABLE = 0xC2;                 // VDV and VRH command enable (+ 2 bytes)
    case VRH_SET = 0xC3;                                // VRH set (+ 1 byte)
    case VDV_SET = 0xC4;                                // VDV set (+ 1 byte)
    case FRAME_RATE_CONTROL_NORMAL = 0xC6;              // Frame rate control in normal mode (+ 1 byte: RTNA)
    case POWER_CONTROL_1 = 0xD0;                        // Power control 1 (+ 2 bytes: fixed, AVDD/AVCL/VDS)

    case GAMMA_CORRECTION_POSITIVE = 0xE0;              // Positive voltage gamma correction (+ 14 bytes)
    case GAMMA_CORRECTION_NEGATIVE = 0xE1;              // Negative voltage gamma correction (+ 14 bytes)
}
