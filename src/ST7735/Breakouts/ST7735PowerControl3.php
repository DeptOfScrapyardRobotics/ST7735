<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735BoosterClockDivider;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735OperationalAmplifierCurrent;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735SourceAmplifierCurrent;

readonly class ST7735PowerControl3
{
    public function __construct(
        public ST7735BoosterClockDivider $dca5 = ST7735BoosterClockDivider::BCLK_DIV_1,
        public ST7735SourceAmplifierCurrent $sapa = ST7735SourceAmplifierCurrent::SMALL,
        public ST7735OperationalAmplifierCurrent $apa = ST7735OperationalAmplifierCurrent::MEDIUM_LOW,
        public ST7735BoosterClockDivider $dca4 = ST7735BoosterClockDivider::BCLK_DIV_3,
        public ST7735BoosterClockDivider $dca3 = ST7735BoosterClockDivider::BCLK_DIV_1,
        public ST7735BoosterClockDivider $dca2 = ST7735BoosterClockDivider::BCLK_DIV_1,
        public ST7735BoosterClockDivider $dca1 = ST7735BoosterClockDivider::BCLK_DIV_1,
    ) {}

    public function toFirstByteBits(): string
    {
        $dca_bits = $this->dca5->toBits();
        $bits76 = "{$dca_bits[1]}{$dca_bits[0]}";
        $sapa_bits = $this->sapa->toBits();
        $bits543 = "{$sapa_bits[2]}{$sapa_bits[1]}{$sapa_bits[0]}";
        $apa_bits = $this->apa->toBits();
        $bits210 = "{$apa_bits[2]}{$apa_bits[1]}{$apa_bits[0]}";

        return "{$bits76}{$bits543}{$bits210}";
    }

    public function toSecondByteBits(): string
    {
        $dca4_bits = $this->dca4->toCircuit2Bits();
        $bits76 = "{$dca4_bits[1]}{$dca4_bits[0]}";

        $dca3_bits = $this->dca3->toBits();
        $bits54 = "{$dca3_bits[1]}{$dca3_bits[0]}";

        $dca2_bits = $this->dca2->toBits();
        $bits32 = "{$dca2_bits[1]}{$dca2_bits[0]}";

        $dca1_bits = $this->dca1->toBits();
        $bits10 = "{$dca1_bits[1]}{$dca1_bits[0]}";

        return "{$bits76}{$bits54}{$bits32}{$bits10}";
    }

    public function toBytes(): array
    {
        return [
            bindec($this->toFirstByteBits()),
            bindec($this->toSecondByteBits()),
        ];
    }

    public static function fromBytes(
        int $dca5 = 0,
        int $sapa = 1,
        int $apa = 2,
        int $dca4 = 1,
        int $dca3 = 0,
        int $dca2 = 0,
        int $dca1 = 0,
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
