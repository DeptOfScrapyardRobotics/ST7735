<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735AnalogVoltageStepUpRatio;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735GateDriveVoltage;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735PowerMode;

readonly class ST7735PowerControl1
{
    public function __construct(
        public ST7735AnalogVoltageStepUpRatio $avdd = ST7735AnalogVoltageStepUpRatio::V5_0,
        public ST7735GateDriveVoltage $positive_gvdd = ST7735GateDriveVoltage::V4_50,
        public ST7735GateDriveVoltage $negative_gvdd = ST7735GateDriveVoltage::V4_50,
        public ST7735PowerMode $power_mode = ST7735PowerMode::AUTO
    ) {}

    public function toFirstByteBits(): string
    {
        $avdd_bits = $this->avdd->toBits();
        $bits765 = "{$avdd_bits[2]}{$avdd_bits[1]}{$avdd_bits[0]}";
        $p_gvdd_bits = $this->positive_gvdd->toBits();
        $bits43210 = "{$p_gvdd_bits[4]}{$p_gvdd_bits[3]}{$p_gvdd_bits[2]}{$p_gvdd_bits[1]}{$p_gvdd_bits[0]}";

        return "{$bits765}{$bits43210}";
    }

    public function toSecondByteBits(): string
    {
        $bits765 = '000';
        $n_gvdd_bits = $this->negative_gvdd->toBits();
        $bits43210 = "{$n_gvdd_bits[4]}{$n_gvdd_bits[3]}{$n_gvdd_bits[2]}{$n_gvdd_bits[1]}{$n_gvdd_bits[0]}";

        return "{$bits765}{$bits43210}";
    }

    public function toThirdByteBits(): string
    {
        $mode_bits = $this->power_mode->toBits();
        $bits76 = "{$mode_bits[1]}{$mode_bits[0]}";
        $bits5432 = '0001';
        $p_gvdd_bits = $this->positive_gvdd->toBits();
        $bit1 = "{$p_gvdd_bits[5]}";
        $n_gvdd_bits = $this->negative_gvdd->toBits();
        $bit0 = "{$n_gvdd_bits[5]}";

        return "{$bits76}{$bits5432}{$bit1}{$bit0}";
    }

    public function toBytes(): array
    {
        return [
            bindec($this->toFirstByteBits()),
            bindec($this->toSecondByteBits()),
            bindec($this->toThirdByteBits()),
        ];
    }

    public static function fromBytes(
        int $avdd = 5,
        int $positive_gvdd = 4,
        int $negative_gvdd = 4,
        int $power_mode = 2
    ): static {
        return new static(
            ST7735AnalogVoltageStepUpRatio::from($avdd),
            ST7735GateDriveVoltage::from($positive_gvdd),
            ST7735GateDriveVoltage::from($negative_gvdd),
            ST7735PowerMode::from($power_mode),
        );
    }
}
