<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Exceptions;

use RuntimeException;

class ST7789Exception extends RuntimeException
{
    public static function invalidProperty(string $name): static
    {
        return new static("Invalid property $name");
    }

    public static function invalidRegisterValue(string $field, int $value, int $min, int $max): static
    {
        return new static("Valid $field values are between $min and $max, you input $value.");
    }
}
