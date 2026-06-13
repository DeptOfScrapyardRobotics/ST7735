<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Adapters;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735OpCode;
use Waveforms\Carriers\SPI\SPIDevice;

abstract class ST7735DataCarrier
{
    public function __construct(
        protected SPIDevice $carrier
    ) {}

    abstract public function data(array $data): void;

    abstract public function command(ST7735OpCode $register_hex, array $command_data = []): void;

    public function reset(): void {}
}
