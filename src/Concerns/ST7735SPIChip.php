<?php

namespace ScrapyardIO\Displays\Color\ST7735\Concerns;

use ScrapyardIO\Transports\SPITransport;
use ScrapyardIO\Transports\Concerns\ResetPin;
use ScrapyardIO\Transports\Concerns\DataCommandPin;

trait ST7735SPIChip
{
    use DataCommandPin, ResetPin;

    protected ?SPITransport $st7735_spi = null;
    protected int $st7735_spi_bus = 0;
    protected int $spi_st7735_chip_select = 0;
    protected int $max_packet_size = 0;

    abstract public function wait(int $ms): void;

    protected function spi_st7735_bus(?int $bus = null): int
    {
        if(!is_null($bus))
        {
            $this->st7735_spi_bus = $bus;
        }
        return $this->st7735_spi_bus;
    }

    protected function spi_st7735_chip_select(?int $cs = null): int
    {
        if($cs)
        {
            $this->spi_st7735_chip_select = $cs;
        }
        return $this->spi_st7735_chip_select;
    }

    protected function st7735_spi(): ?SPITransport
    {
        if(empty($this->st7735_spi))
        {
            $this->st7735_spi = new SPITransport(
                $this->spi_st7735_bus(),
                $this->spi_st7735_chip_select(),
                0,
                8000000,
                0
            );
        }

        return $this->st7735_spi;
    }

    public function sendData(array $bytes): void
    {
        $this->dcHigh();
        $this->wait(1);
        $this->st7735_spi()->send($bytes);
    }

    public function sendCommand(array $bytes): void
    {
        $this->dcLow();
        if(count($bytes) > 1)
        {
            $command = $bytes[0];
            $this->st7735_spi()->send([$command]);
            unset($bytes[0]);
            $payload = array_values($bytes);
            $this->sendData($payload);
        }
        else
        {
            $this->st7735_spi()->send($bytes);
        }
    }

    protected function resetSequence(): void
    {
        $this->rstHigh();
        $this->wait(10);

        $this->rstLow();
        $this->wait(10);

        $this->rstHigh();
        $this->wait(120);

        $this->dcLow();
    }
}
