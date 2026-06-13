<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Exceptions;

use RuntimeException;

class ST7735Exception extends RuntimeException
{
    public static function invalidProperty(string $name): static
    {
        return new static("Invalid property $name");
    }

    public static function invalidClockCycles(int $cycles): static
    {
        return new static("Valid Clock Cycles are between 0 and 15, you input $cycles.");
    }

    public static function invalidBlankLinesToInsert(int $lines): static
    {
        return new static("Valid Blank Lines to Insert are between 0 and 63, you input $lines.");
    }
}
