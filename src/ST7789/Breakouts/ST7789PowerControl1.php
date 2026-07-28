<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789AVCLVoltage;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789AVDDVoltage;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789VDSVoltage;

/**
 * PWCTRL1 (0xD0) — power control 1.
 *
 * 2 parameter bytes:
 *   1. fixed 0xA4
 *   2. AVDD[7:6] | AVCL[5:4] | reserved[3:2] | VDS[1:0]
 */
readonly class ST7789PowerControl1
{
    public function __construct(
        public ST7789AVDDVoltage $avdd = ST7789AVDDVoltage::V6_8,
        public ST7789AVCLVoltage $avcl = ST7789AVCLVoltage::VN4_8,
        public ST7789VDSVoltage $vds = ST7789VDSVoltage::V2_3,
    ) {}

    public function toSecondByteBits(): string
    {
        $avdd_bits = $this->avdd->toBits();
        $bits76 = "{$avdd_bits[1]}{$avdd_bits[0]}";
        $avcl_bits = $this->avcl->toBits();
        $bits54 = "{$avcl_bits[1]}{$avcl_bits[0]}";
        $bits32 = '00';
        $vds_bits = $this->vds->toBits();
        $bits10 = "{$vds_bits[1]}{$vds_bits[0]}";

        return "{$bits76}{$bits54}{$bits32}{$bits10}";
    }

    /**
     * @return list<int>
     */
    public function toBytes(): array
    {
        return [
            0xA4,
            bindec($this->toSecondByteBits()),
        ];
    }

    public static function fromBytes(
        int $avdd = 2,
        int $avcl = 2,
        int $vds = 1,
    ): static {
        return new static(
            ST7789AVDDVoltage::from($avdd),
            ST7789AVCLVoltage::from($avcl),
            ST7789VDSVoltage::from($vds),
        );
    }
}
