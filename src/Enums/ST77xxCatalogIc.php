<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\Enums;

enum ST77xxCatalogIc: string
{
    case ST7735 = 'st7735';
    case ST7789 = 'st7789';
    case ST7796 = 'st7796';

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_map(
            static fn (self $case): string => $case->value,
            self::cases(),
        );
    }
}
