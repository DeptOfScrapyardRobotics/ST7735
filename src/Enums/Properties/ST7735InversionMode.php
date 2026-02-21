<?php

namespace ScrapyardIO\Libraries\Displays\Drivers\ST7735\Enums\Properties;

/**
 * Display Inversion Control register values
 */
enum ST7735InversionMode: int
{
    /**
     * Line inversion mode
     * Alternates between inverted and non-inverted lines
     */
    case LINE_INVERSION = 0x00;

    /**
     * Frame inversion mode
     * Entire frame is inverted
     */
    case FRAME_INVERSION = 0x07;
}

