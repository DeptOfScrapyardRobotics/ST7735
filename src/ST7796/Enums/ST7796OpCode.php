<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums;

enum ST7796OpCode: int
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

    case INTERFACE_MODE_CONTROL = 0xB0;                 // Interface mode control (+ 1 byte)
    case FRAME_RATE_CONTROL_NORMAL = 0xB1;              // Frame rate control normal mode (+ 2 bytes: DIVA, RTNA)
    case DISPLAY_INVERSION_CONTROL = 0xB4;              // Display inversion control (+ 1 byte: NLA)
    case BLANKING_PORCH_CONTROL = 0xB5;                 // Blanking porch control (+ 4 bytes: VFP, VBP, HFP, HBP)
    case DISPLAY_FUNCTION_CONTROL = 0xB6;               // Display function control (+ 3 bytes)
    case ENTRY_MODE_SET = 0xB7;                         // Entry mode set (+ 1 byte)

    case POWER_CONTROL_1 = 0xC0;                        // Power control 1 (+ 2 bytes: VRH)
    case POWER_CONTROL_2 = 0xC1;                        // Power control 2 (+ 1 byte: VAP/VAN amplitude)
    case POWER_CONTROL_3 = 0xC2;                        // Power control 3 (+ 1 byte)
    case VCOM_CONTROL = 0xC5;                           // VCOM control (+ 1 byte: VCOM level)

    case GAMMA_CORRECTION_POSITIVE = 0xE0;              // Positive voltage gamma correction (+ 14 bytes)
    case GAMMA_CORRECTION_NEGATIVE = 0xE1;              // Negative voltage gamma correction (+ 14 bytes)
    case DISPLAY_OUTPUT_CTRL_ADJUST = 0xE8;             // Display output ctrl adjust (+ 8 bytes)

    case COMMAND_SET_CONTROL = 0xF0;                    // Command set control / CSCON (+ 1 byte: enable/disable command 2)
}
