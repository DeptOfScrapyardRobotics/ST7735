<?php

namespace ScrapyardIO\Libraries\Displays\Drivers\ST7735\Enums\Properties;
/**
 * Interface Pixel Format (COLMOD) register values
 */
enum ST7735ColorMode: int
{
    /**
     * 12-bit per pixel (4096 colors)
     * RGB444 format
     */
    case COLOR12 = 0x03;

    /**
     * 16-bit per pixel (65K colors)
     * RGB565 format (5 bits red, 6 bits green, 5 bits blue)
     */
    case COLOR16 = 0x05;

    /**
     * 18-bit per pixel (262K colors)
     * RGB666 format (6 bits per color)
     */
    case COLOR18 = 0x06;

    public function bitsPerPixel(): int
    {
        return match ($this) {
            self::COLOR12 => 12,
            self::COLOR16 => 16,
            self::COLOR18 => 18,
        };
    }
}

