<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Adapters;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796OpCode;
use Waveforms\Carriers\GPIO\GPIOBus;
use Waveforms\Carriers\SPI\SPIDevice;

class ST7796SPIAdapter extends ST7796DataCarrier
{
    public function __construct(
        SPIDevice $carrier,
        protected GPIOBus $gpio,
        protected int $max_packet_size
    ) {
        parent::__construct($carrier);
    }

    public function reset(): void
    {
        $this->gpio->rst()->high();
        usleep(3000);

        $this->gpio->rst()->low();
        usleep(3000);

        $this->gpio->rst()->high();
        usleep(3000);
    }

    public function data(array $data): void
    {
        foreach (array_chunk($data, $this->max_packet_size) as $chunk) {
            $this->gpio->dc()->high();
            $this->carrier->write($chunk);
        }
    }

    public function command(ST7796OpCode $register_hex, array $command_data = []): void
    {
        $this->gpio->dc()->low();
        $this->carrier->write([$register_hex->value]);
        if (count($command_data) > 0) {
            $this->data($command_data);
        }
    }
}
