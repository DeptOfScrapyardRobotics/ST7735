<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

enum ST7735OpCode: int
{
    case SOFTWARE_RESET = 0x01;
    case ENTER_SLEEP_MODE = 0x10;
    case EXIT_SLEEP_MODE = 0x11;
    case PARTIAL_MODE_ON = 0x12;
    case NORMAL_MODE_ON = 0x13;

    case DISPLAY_INVERSION_ON = 0x20;
    case DISPLAY_INVERSION_OFF = 0x21;

    case TOGGLE_DISPLAY_OFF = 0x28;
    case TOGGLE_DISPLAY_ON = 0x29;
    case SET_COLUMN_ADDRESS = 0x2A;
    case SET_ROW_ADDRESS = 0x2B;
    case WRITE_MEMORY_START = 0x2C;

    case MEMORY_ACCESS_CONTROL = 0x36;
    case SET_PIXEL_FORMAT = 0x3A;

    case FRAME_RATE_CONTROL_NORMAL = 0xB1;
    case FRAME_RATE_CONTROL_IDLE = 0xB2;
    case FRAME_RATE_CONTROL_PARTIAL = 0xB3;
    case INVERSION_CONTROL = 0xB4;

    case POWER_CONTROL_1 = 0xC0;                        // Power control 1 (+ 3 bytes: AVDD, VRHP, VRHN, MODE)
    case POWER_CONTROL_2 = 0xC1;                        // Power control 2 (+ 1 byte: VGH25, VGSEL, VGHBT)
    case POWER_CONTROL_3 = 0xC2;                        // Power control 3 normal mode (+ 2 bytes: op-amp, DC voltage)
    case POWER_CONTROL_4 = 0xC3;                        // Power control 4 idle mode (+ 2 bytes)
    case POWER_CONTROL_5 = 0xC4;                        // Power control 5 partial mode (+ 2 bytes)
    case VCOM_CONTROL_1 = 0xC5;                         // VCOM voltage control (+ 1 byte: VCOMH level)

    case GAMMA_CORRECTION_POSITIVE = 0xE0;              // Positive gamma correction (+ 16 bytes)
    case GAMMA_CORRECTION_NEGATIVE = 0xE1;              // Negative gamma correction (+ 16 bytes)

    case POWER_CONTROL_6 = 0xFC;                        // Power control 6 partial+idle (+ 1 byte)
}
