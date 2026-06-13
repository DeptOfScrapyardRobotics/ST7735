<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums;

/**
 * Interface Pixel Format (COLMOD, 0x3A) register values.
 *
 * Bits [6:4] select the RGB interface colour format and bits [2:0] the
 * control (MCU) interface colour format. The cases below set both nibbles
 * to the same depth, which is the configuration used over SPI.
 */
enum ST7796ColorMode: int
{
    /**
     * 12-bit per pixel (4096 colors)
     * RGB444 format
     */
    case COLOR12 = 0x53;

    /**
     * 16-bit per pixel (65K colors)
     * RGB565 format (5 bits red, 6 bits green, 5 bits blue)
     */
    case COLOR16 = 0x55;

    /**
     * 18-bit per pixel (262K colors)
     * RGB666 format (6 bits per color)
     */
    case COLOR18 = 0x66;

    public function bitsPerPixel(): int
    {
        return match ($this) {
            self::COLOR12 => 12,
            self::COLOR16 => 16,
            self::COLOR18 => 18,
        };
    }
}
