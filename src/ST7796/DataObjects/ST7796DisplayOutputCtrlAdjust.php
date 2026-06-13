<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects;

/**
 * DOCA (0xE8) — display output ctrl adjust.
 *
 * 8 parameter bytes controlling the source/gate equalize timing of the panel.
 * These are manufacturer tuning values; the defaults below are the
 * datasheet-recommended set for a 480x320 ST7796 module.
 */
readonly class ST7796DisplayOutputCtrlAdjust
{
    public function __construct(
        public int $adjustment_1 = 0x40,
        public int $adjustment_2 = 0x8A,
        public int $adjustment_3 = 0x00,
        public int $adjustment_4 = 0x00,
        public int $adjustment_5 = 0x29,
        public int $adjustment_6 = 0x19,
        public int $adjustment_7 = 0xA5,
        public int $adjustment_8 = 0x33,
    ) {}

    /**
     * @return list<int> The 8 parameter bytes in datasheet order.
     */
    public function toBytes(): array
    {
        return array_map(
            static fn (int $value): int => $value & 0xFF,
            [
                $this->adjustment_1,
                $this->adjustment_2,
                $this->adjustment_3,
                $this->adjustment_4,
                $this->adjustment_5,
                $this->adjustment_6,
                $this->adjustment_7,
                $this->adjustment_8,
            ]
        );
    }

    /**
     * @param  list<int>  $bytes  The 8 parameter bytes in datasheet order.
     */
    public static function fromBytes(array $bytes): static
    {
        return new static(...array_map(
            static fn (int $value): int => $value & 0xFF,
            array_values($bytes)
        ));
    }
}
