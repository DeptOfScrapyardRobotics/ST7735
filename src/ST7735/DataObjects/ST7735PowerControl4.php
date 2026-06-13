<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735BoosterClockDivider;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735OperationalAmplifierCurrent;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735SourceAmplifierCurrent;

readonly class ST7735PowerControl4 extends ST7735PowerControl3
{
    public function __construct(
        ST7735BoosterClockDivider $dca5 = ST7735BoosterClockDivider::BCLK_DIV_2,
        ST7735SourceAmplifierCurrent $sapa = ST7735SourceAmplifierCurrent::SMALL,
        ST7735OperationalAmplifierCurrent $apa = ST7735OperationalAmplifierCurrent::MEDIUM_LOW,
        ST7735BoosterClockDivider $dca4 = ST7735BoosterClockDivider::BCLK_DIV_3,
        ST7735BoosterClockDivider $dca3 = ST7735BoosterClockDivider::BCLK_DIV_2,
        ST7735BoosterClockDivider $dca2 = ST7735BoosterClockDivider::BCLK_DIV_2,
        ST7735BoosterClockDivider $dca1 = ST7735BoosterClockDivider::BCLK_DIV_2,
    ) {
        parent::__construct(
            $dca5,
            $sapa,
            $apa,
            $dca4,
            $dca3,
            $dca2,
            $dca1
        );
    }

    public static function fromBytes(
        int $dca5 = 2,
        int $sapa = 1,
        int $apa = 2,
        int $dca4 = 1,
        int $dca3 = 2,
        int $dca2 = 2,
        int $dca1 = 2,
    ): static {
        return new static(
            ST7735BoosterClockDivider::from($dca5),
            ST7735SourceAmplifierCurrent::from($sapa),
            ST7735OperationalAmplifierCurrent::from($apa),
            ST7735BoosterClockDivider::from($dca4),
            ST7735BoosterClockDivider::from($dca3),
            ST7735BoosterClockDivider::from($dca2),
            ST7735BoosterClockDivider::from($dca1),
        );
    }
}
