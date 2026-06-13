<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums;

/**
 * Display Inversion Control (INVTR, 0xB4) — NLA[2:0] dot inversion selection.
 */
enum ST7796InversionMode: int
{
    case COLUMN = 0x00;
    case ONE_DOT = 0x01;
    case TWO_DOT = 0x02;
}
