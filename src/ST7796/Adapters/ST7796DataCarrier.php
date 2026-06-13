<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Adapters;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796OpCode;
use Waveforms\Carriers\SPI\SPIDevice;

abstract class ST7796DataCarrier
{
    public function __construct(
        protected SPIDevice $carrier
    ) {}

    abstract public function data(array $data): void;

    abstract public function command(ST7796OpCode $register_hex, array $command_data = []): void;

    public function reset(): void {}
}
