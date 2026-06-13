<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums;

/**
 * DCA[9:8] — DC booster step-up clock divisor for booster circuit 1 (bits 7:6 of PWCTR3 byte 1).
 * Raw values match the DCA[9:8] bit encoding. Note: DCA[7:6] in byte 2 uses a different mapping.
 */
enum ST7735BoosterClockDivider: int
{
    case BCLK_DIV_1 = 0x00;
    case BCLK_DIV_3 = 0x01;
    case BCLK_DIV_2 = 0x02;
    case BCLK_DIV_4 = 0x03;

    /**
     * Returns the raw bits to write into DCA[7:6] (booster circuit 2),
     * which has an inverted bit-0 encoding relative to all other circuits.
     *
     * Circuit 2 register encoding:
     *   00 = BCLK/3,  01 = BCLK/1,  10 = BCLK/4,  11 = BCLK/2
     */
    public function toCircuit2Bits(): array
    {
        return byte2bits($this->value ^ 0x01);
    }

    public function toBits(): array
    {
        return byte2bits($this->value);
    }
}
