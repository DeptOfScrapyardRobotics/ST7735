<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects;

use BareMetal\DataObjects\DataRegister;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Exceptions\ST7735Exception;

readonly class ST7735PorchLines extends DataRegister
{
    public function __construct(
        public int $blank_lines_to_insert = 0x2C
    ) {
        if (($this->blank_lines_to_insert < 0) || ($this->blank_lines_to_insert > 63)) {
            throw ST7735Exception::invalidBlankLinesToInsert($this->blank_lines_to_insert);
        }
    }

    public function toBits(): string
    {
        $bits76 = '00';
        $broken_down = byte2bits($this->blank_lines_to_insert);
        $bits543210 = "{$broken_down[5]}{$broken_down[4]}{$broken_down[3]}{$broken_down[2]}{$broken_down[1]}{$broken_down[0]}";

        return "{$bits76}{$bits543210}";
    }

    public static function fromByte(int $byte): static
    {
        return new static($byte);
    }

    public static function none(): static
    {
        return new static(0);
    }
}
