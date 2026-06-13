<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums;

/**
 * CSCON (0xF0) — command set control payload values.
 *
 * The ST7796 hides its manufacturer (command 2) registers behind a two-write
 * unlock/lock handshake. Both ENABLE values must be written to expose the
 * extended registers (0xB6, 0xC1..0xC5, 0xE0/0xE1, 0xE8, ...) and both
 * DISABLE values to lock them again before turning the display on.
 */
enum ST7796CommandSet: int
{
    case ENABLE_EXTENSION_PART_1 = 0xC3;
    case ENABLE_EXTENSION_PART_2 = 0x96;
    case DISABLE_EXTENSION_PART_1 = 0x3C;
    case DISABLE_EXTENSION_PART_2 = 0x69;
}
