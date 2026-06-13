<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects;

/**
 * VDVVRHEN (0xC2) — VDV and VRH command enable.
 *
 * 2 parameter bytes:
 *   1. CMDEN (bit 0) — when set, VDV and VRH are taken from the C3/C4 command
 *      registers instead of the NVM.
 *   2. fixed 0xFF.
 */
readonly class ST7789VDVVRHEnable
{
    public function __construct(
        public bool $command_enabled = true,
    ) {}

    /**
     * @return list<int>
     */
    public function toBytes(): array
    {
        return [
            $this->command_enabled ? 0x01 : 0x00,
            0xFF,
        ];
    }

    public static function fromBytes(bool $command_enabled = true): static
    {
        return new static($command_enabled);
    }
}
