<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts;

class ST7735PartialModeFrameRateControl
{
    public ST7735ClockCycle $dot_inversion_osc_clock_cycles_per_line;

    public ST7735PorchLines $dot_inversion_front_porch_lines;

    public ST7735PorchLines $dot_inversion_back_porch_lines;

    public ST7735ClockCycle $line_inversion_osc_clock_cycles_per_line;

    public ST7735PorchLines $line_inversion_front_porch_lines;

    public ST7735PorchLines $line_inversion_back_porch_lines;

    public function __construct(
        int $dot_inversion_osc_clock_cycles_per_line = 1,
        int $dot_inversion_front_porch_lines = 44,
        int $dot_inversion_back_porch_lines = 45,
        int $line_inversion_osc_clock_cycles_per_line = 1,
        int $line_inversion_front_porch_lines = 44,
        int $line_inversion_back_porch_lines = 45
    ) {
        $this->dot_inversion_osc_clock_cycles_per_line = new ST7735ClockCycle($dot_inversion_osc_clock_cycles_per_line);
        $this->dot_inversion_front_porch_lines = new ST7735PorchLines($dot_inversion_front_porch_lines);
        $this->dot_inversion_back_porch_lines = new ST7735PorchLines($dot_inversion_back_porch_lines);

        $this->line_inversion_osc_clock_cycles_per_line = new ST7735ClockCycle($line_inversion_osc_clock_cycles_per_line);
        $this->line_inversion_front_porch_lines = new ST7735PorchLines($line_inversion_front_porch_lines);
        $this->line_inversion_back_porch_lines = new ST7735PorchLines($line_inversion_back_porch_lines);
    }

    public function toBytes(): array
    {
        return [
            $this->dot_inversion_osc_clock_cycles_per_line->toByte(),
            $this->dot_inversion_front_porch_lines->toByte(),
            $this->dot_inversion_back_porch_lines->toByte(),
            $this->line_inversion_osc_clock_cycles_per_line->toByte(),
            $this->line_inversion_front_porch_lines->toByte(),
            $this->line_inversion_back_porch_lines->toByte(),
        ];
    }

    public static function fromBytes(
        int $dot_inversion_osc_clock_cycles_per_line = 1,
        int $dot_inversion_front_porch_lines = 44,
        int $dot_inversion_back_porch_lines = 45,
        int $line_inversion_osc_clock_cycles_per_line = 1,
        int $line_inversion_front_porch_lines = 44,
        int $line_inversion_back_porch_lines = 45
    ): static {
        return new static(
            $dot_inversion_osc_clock_cycles_per_line,
            $dot_inversion_front_porch_lines,
            $dot_inversion_back_porch_lines,
            $line_inversion_osc_clock_cycles_per_line,
            $line_inversion_front_porch_lines,
            $line_inversion_back_porch_lines,
        );
    }
}
