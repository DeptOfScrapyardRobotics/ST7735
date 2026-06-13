<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Exceptions\ST7796Exception;

/**
 * DFC (0xB6) — display function control.
 *
 * 3 parameter bytes:
 *   1. gate/scan direction and interval-scan control (GS, SS, REV).
 *   2. source/gate non-overlap and scan cycle (ISC, NL high bits).
 *   3. number of driven lines (NL) — 0x3B == 480 lines for a 480x320 panel.
 */
readonly class ST7796DisplayFunctionControl
{
    public function __construct(
        public int $scan_control = 0x80,
        public int $source_gate_control = 0x02,
        public int $driven_lines = 0x3B,
    ) {
        $this->assertByte($this->scan_control, 'scan_control');
        $this->assertByte($this->source_gate_control, 'source_gate_control');
        $this->assertByte($this->driven_lines, 'driven_lines');
    }

    private function assertByte(int $value, string $field): void
    {
        if (($value < 0) || ($value > 0xFF)) {
            throw ST7796Exception::invalidRegisterValue($field, $value, 0, 0xFF);
        }
    }

    /**
     * @return list<int>
     */
    public function toBytes(): array
    {
        return [
            $this->scan_control & 0xFF,
            $this->source_gate_control & 0xFF,
            $this->driven_lines & 0xFF,
        ];
    }

    public static function fromBytes(
        int $scan_control = 0x80,
        int $source_gate_control = 0x02,
        int $driven_lines = 0x3B,
    ): static {
        return new static($scan_control, $source_gate_control, $driven_lines);
    }
}
