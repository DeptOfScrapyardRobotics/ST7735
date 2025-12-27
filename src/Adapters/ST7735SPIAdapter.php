<?php

namespace ScrapyardIO\Displays\Color\ST7735\Adapters;

use ScrapyardIO\Displays\Adapters\ColorDisplayAdapter;
use ScrapyardIO\Displays\Color\ST7735\Concerns\ST7735SPIChip;
use ScrapyardIO\Displays\Color\ST7735\Concerns\ST7735BootSequence;

class ST7735SPIAdapter extends ColorDisplayAdapter
{
    use ST7735SPIChip;
    use ST7735BootSequence;

    public function bus(int $bus):static
    {
        $this->spi_st7735_bus($bus);
        return $this;
    }

    public function chipSelect(int $cs):static
    {
        $this->spi_st7735_chip_select($cs);
        return $this;
    }

    public function dcPin(int $chip, int $line): static
    {
        $this->dc_chip($chip);
        $this->dc_line($line);
        $this->dc_gpio();

        return $this;
    }

    public function rstPin(int $chip, int $line): static
    {
        $this->rst_chip($chip);
        $this->rst_line($line);
        $this->rst_gpio();

        return $this;
    }

    public function offsets(int $x, int $y): static
    {
        $this->y_offset = $y;
        $this->x_offset = $x;

        return $this;
    }

    public function boot(): static
    {
        $this->st7735_spi();

        $this->max_packet_size = 4096;

        $this->resetSequence();
        $this->turnDisplayOff();
        $this->exitSleepMode();
        $this->setFrameRateControl();
        $this->setPowerControl();
        $this->turnInversionOff();
        $this->setMADControl();
        $this->setColorControl();
        $this->setNormalDisplayMode();
        $this->turnDisplayOn();

        return $this;
    }
}
