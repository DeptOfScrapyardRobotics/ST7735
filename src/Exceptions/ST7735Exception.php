<?php

namespace ScrapyardIO\Displays\Color\ST7735\Exceptions;

use ScrapyardIO\Support\Exceptions\ScrapyardIOException;

class ST7735Exception extends ScrapyardIOException
{
    public static function invalidProtocol(string $name): static
    {
        return new static("Unsupported protocol '{$name}'.");
    }

    public static function pixelOutOfBounds(int $x): static
    {
        return new static("$x not a valid pixel index");
    }
}
