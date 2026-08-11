<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxCarrierTransport;

/**
 * Solid-color fill without tubes/framebuffers — used by hardware smoke sketches.
 *
 * @property ST77xxCarrierTransport $transport
 * @property int $max_packet_size
 */
trait ST77xxFillsRgb565
{
    abstract public function width(): int;

    abstract public function height(): int;

    abstract public function setAddressWindow(int $x, int $y, int $width, int $height): void;

    /**
     * Fill the full panel with an RGB565 color (big-endian byte order on the wire).
     */
    public function fillRgb565(int $color): void
    {
        $width = $this->width();
        $height = $this->height();
        $this->setAddressWindow(0, 0, $width, $height);

        // RAMWR — same opcode across ST7735 / ST7789 / ST7796
        $this->transport->command(0x2C);

        $hi = ($color >> 8) & 0xFF;
        $lo = $color & 0xFF;
        $pixels = $width * $height;
        $chunkPixels = max(1, intdiv(max(2, $this->max_packet_size), 2));

        $fullChunk = [];
        for ($i = 0; $i < $chunkPixels; $i++) {
            $fullChunk[] = $hi;
            $fullChunk[] = $lo;
        }

        $remaining = $pixels;
        while ($remaining > 0) {
            $count = min($remaining, $chunkPixels);
            $this->transport->data(
                $count === $chunkPixels
                    ? $fullChunk
                    : array_slice($fullChunk, 0, $count * 2)
            );
            $remaining -= $count;
        }
    }
}
