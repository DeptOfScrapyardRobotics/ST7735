<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects;

use BareMetal\DataObjects\DataRegister;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Exceptions\ST7796Exception;

/**
 * VCMPCTL (0xC5) — VCOM control, single parameter byte.
 *   bits [5:0] VCM — VCOM voltage level (0x18 == datasheet default).
 */
readonly class ST7796VCOMControl extends DataRegister
{
    public function __construct(
        public int $vcm = 0x18,
    ) {
        if (($this->vcm < 0) || ($this->vcm > 0x3F)) {
            throw ST7796Exception::invalidRegisterValue('vcm', $this->vcm, 0, 0x3F);
        }
    }

    public function toBits(): string
    {
        $broken_down = byte2bits($this->vcm);

        return "00{$broken_down[5]}{$broken_down[4]}{$broken_down[3]}{$broken_down[2]}{$broken_down[1]}{$broken_down[0]}";
    }

    public static function fromByte(int $byte): static
    {
        return new static($byte & 0x3F);
    }

    public static function none(): static
    {
        return new static(0);
    }
}
