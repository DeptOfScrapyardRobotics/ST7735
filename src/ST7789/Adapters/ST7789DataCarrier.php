<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Adapters;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789OpCode;
use Waveforms\Carriers\SPI\SPIDevice;

abstract class ST7789DataCarrier
{
    public function __construct(
        protected SPIDevice $carrier
    ) {}

    abstract public function data(array $data): void;

    abstract public function command(ST7789OpCode $register_hex, array $command_data = []): void;

    public function reset(): void {}
}
