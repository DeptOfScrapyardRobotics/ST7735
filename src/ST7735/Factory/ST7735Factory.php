<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Factory;

use BareMetal\CircuitFactory;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Adapters\ST7735SPIAdapter;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735IdleModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735NormalFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PartialModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl3;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl4;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl5;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735VCOMControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\ST7735;
use Exception;
use Waveforms\Carriers\GPIO\Factory\GPIOConnectionBuilder;
use Waveforms\Carriers\GPIO\GPIOPin;
use Waveforms\Carriers\SPI\Enums\SPIMode;
use Waveforms\Carriers\SPI\Factory\SPIConnectionBuilder;

class ST7735Factory extends CircuitFactory
{
    protected bool $has_dc = false;

    protected bool $has_rst = false;

    protected int $x_offset = 0;

    protected int $y_offset = 0;

    protected int $width = 128;

    protected int $height = 160;

    protected bool $invert_display = true;

    protected int $max_packet_size = 2048;

    public string $consumer = 'st7735';

    public ST7735NormalFrameRateControl $nfc;

    public ST7735IdleModeFrameRateControl $ifc;

    public ST7735PartialModeFrameRateControl $pfc;

    public ST7735PowerControl1 $power_control_1;

    public ST7735PowerControl2 $power_control_2;

    public ST7735PowerControl3 $power_control_3;

    public ST7735PowerControl4 $power_control_4;

    public ST7735PowerControl5 $power_control_5;

    public ST7735VCOMControl1 $v_com_ctrl;

    public ST7735MADControl $mad_ctrl;

    public ST7735ColorMode $color_mode = ST7735ColorMode::COLOR16;

    public ST7735GammaPositive $gamma_positive;

    public ST7735GammaNegative $gamma_negative;

    public ?SPIConnectionBuilder $connection = null;

    public function __construct(
        public SPIConnectionBuilder $spi_connection,
        public GPIOConnectionBuilder $gpio_connection
    ) {
        $this->nfc = ST7735NormalFrameRateControl::fromBytes();
        $this->ifc = ST7735IdleModeFrameRateControl::fromBytes();
        $this->pfc = ST7735PartialModeFrameRateControl::fromBytes();
        $this->power_control_1 = new ST7735PowerControl1;
        $this->power_control_2 = new ST7735PowerControl2;
        $this->power_control_3 = new ST7735PowerControl3;
        $this->power_control_4 = new ST7735PowerControl4;
        $this->power_control_5 = new ST7735PowerControl5;
        $this->v_com_ctrl = new ST7735VCOMControl1;
        $this->mad_ctrl = new ST7735MADControl(
            false,
            true,
            true,
            false,
            true,
            false,
        );
        $this->gamma_positive = new ST7735GammaPositive;
        $this->gamma_negative = new ST7735GammaNegative;
    }

    public function spi(string|int $master, int $chip_select): static
    {
        $this->connection = $this->spi_connection->firstly($master)
            ->chip($chip_select)
            ->speed(32000000)
            ->mode(SPIMode::MODE_0);

        return $this;
    }

    public function gpiochip(int|string $chip): static
    {
        $this->gpio_connection = $this->gpio_connection->firstly($chip);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function dc(int $pin): static
    {
        if (! $this->has_dc) {
            $gpio_output = GPIOPin::createOutput($this->connection->connection(), $pin, 'dc');
            $this->gpio_connection = $this->gpio_connection->addOutput($gpio_output);
            $this->has_dc = true;
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function rst(int $pin): static
    {
        if (! $this->has_rst) {
            $gpio_output = GPIOPin::createOutput($this->connection->connection(), $pin, 'rst');
            $this->gpio_connection = $this->gpio_connection->addOutput($gpio_output);
            $this->has_rst = true;
        }

        return $this;
    }

    public function consumer(string $consumer): static
    {
        $this->consumer = $consumer;

        return $this;
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function maxPacketSize(int $max_packet_size): static
    {
        $this->max_packet_size = $max_packet_size;

        return $this;
    }

    public function invertDisplay(bool $flag): static
    {
        $this->invert_display = $flag;

        return $this;
    }

    public function powerControl1(ST7735PowerControl1 $control): static
    {
        $this->power_control_1 = $control;

        return $this;
    }

    public function powerControl2(ST7735PowerControl2 $control): static
    {
        $this->power_control_2 = $control;

        return $this;
    }

    public function powerControl3(ST7735PowerControl3 $control): static
    {
        $this->power_control_3 = $control;

        return $this;
    }

    public function powerControl4(ST7735PowerControl4 $control): static
    {
        $this->power_control_4 = $control;

        return $this;
    }

    public function powerControl5(ST7735PowerControl5 $control): static
    {
        $this->power_control_5 = $control;

        return $this;
    }

    public function vcomControl(ST7735VCOMControl1 $control): static
    {
        $this->v_com_ctrl = $control;

        return $this;
    }

    public function madControl(ST7735MADControl $control): static
    {
        $this->mad_ctrl = $control;

        return $this;
    }

    public function colorMode(ST7735ColorMode $color_mode): static
    {
        $this->color_mode = $color_mode;

        return $this;
    }

    public function gammaPositive(ST7735GammaPositive $gamma): static
    {
        $this->gamma_positive = $gamma;

        return $this;
    }

    public function gammaNegative(ST7735GammaNegative $gamma): static
    {
        $this->gamma_negative = $gamma;

        return $this;
    }

    public function square(): static
    {
        $this->width = 128;
        $this->height = 128;
        $this->x_offset = 1;
        $this->y_offset = 2;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function create(): ST7735
    {
        $carrier = $this->connection?->boot();
        if (is_null($carrier)) {
            throw new Exception('A connection was not registered.');
        }

        $gpio = $this->gpio_connection
            ->shareConnectionWith($carrier)
            ->consumer($this->consumer)
            ->boot();

        $carrier = new ST7735SPIAdapter($carrier, $gpio, $this->max_packet_size);

        return new ST7735(
            $carrier,
            $this->width,
            $this->height,
            $this->nfc,
            $this->ifc,
            $this->pfc,
            $this->power_control_1,
            $this->power_control_2,
            $this->power_control_3,
            $this->power_control_4,
            $this->power_control_5,
            $this->v_com_ctrl,
            $this->mad_ctrl,
            $this->color_mode,
            $this->gamma_positive,
            $this->gamma_negative,
            $this->x_offset,
            $this->y_offset,
            $this->invert_display
        );
    }
}
