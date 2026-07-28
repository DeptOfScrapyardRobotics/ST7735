<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx;

use DeptOfScrapyardRobotics\Displays\ST77xx\Concerns\ST77xxIO;
use GeneralPurposeIO\Digital\DigitalOutputPin;
use GeneralPurposeIO\SPI\SPIDevice;

class ST77xxCarrierTransport
{
    use ST77xxIO;

    protected int $max_packet_size = 1024;

    public readonly string $active_transport;

    /**
     * @throws ST77xxException
     */
    public function __construct(
        protected ?SPIDevice $spi = null,
        protected ?DigitalOutputPin $dc = null,
        protected ?DigitalOutputPin $rst = null,
    ) {
        $this->active_transport = $this->detectTransport();
    }

    public function command(int $register, array $command_data = []): int
    {
        return $this->spiCommand($register, $command_data);
    }

    public function data(array $data = []): void
    {
        $this->spiData($data);
    }

    public function reset(int $sleep_time): void
    {
        $this->rst->high();
        usleep($sleep_time);

        $this->rst->low();
        usleep($sleep_time);

        $this->rst->high();
        usleep($sleep_time);
    }

    public function maxPacketSize(int $size): static
    {
        $this->max_packet_size = $size;

        return $this;
    }

    public function close(): void
    {
        $this->spi?->close();
        $this->dc?->close();
        $this->rst?->close();
    }

    /**
     * @throws ST77xxException
     */
    protected function detectTransport(): string
    {
        if (! is_null($this->spi)) {
            if ((! is_null($this->dc)) && (! is_null($this->rst))) {
                return 'spi';
            }

            throw ST77xxException::missingDigitalPins();
        }

        throw ST77xxException::transportMissingProtocol();
    }
}