<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts;

class ST7735NormalFrameRateControl
{
    public ST7735ClockCycle $osc_clock_cycles_per_line;

    public ST7735PorchLines $front_porch_lines;

    public ST7735PorchLines $back_porch_lines;

    public function __construct(
        int $osc_clock_cycles_per_line = 2,
        int $front_porch_lines = 44,
        int $back_porch_lines = 45
    ) {
        $this->osc_clock_cycles_per_line = new ST7735ClockCycle($osc_clock_cycles_per_line);
        $this->front_porch_lines = new ST7735PorchLines($front_porch_lines);
        $this->back_porch_lines = new ST7735PorchLines($back_porch_lines);
    }

    public function toBytes(): array
    {
        return [
            $this->osc_clock_cycles_per_line->toByte(),
            $this->front_porch_lines->toByte(),
            $this->back_porch_lines->toByte(),
        ];
    }

    public static function fromBytes(
        int $osc_clock_cycles_per_line = 2,
        int $front_porch_lines = 44,
        int $back_porch_lines = 45
    ): static {
        return new static($osc_clock_cycles_per_line, $front_porch_lines, $back_porch_lines);
    }
}
