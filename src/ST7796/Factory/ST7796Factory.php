<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Factory;

use BareMetal\CircuitFactory;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Adapters\ST7796SPIAdapter;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796DisplayFunctionControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796DisplayInversionControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796DisplayOutputCtrlAdjust;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796PowerControl3;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796VCOMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\ST7796;
use Exception;
use Waveforms\Carriers\GPIO\Factory\GPIOConnectionBuilder;
use Waveforms\Carriers\GPIO\GPIOPin;
use Waveforms\Carriers\SPI\Enums\SPIMode;
use Waveforms\Carriers\SPI\Factory\SPIConnectionBuilder;

class ST7796Factory extends CircuitFactory
{
    protected bool $has_dc = false;

    protected bool $has_rst = false;

    protected int $width = 480;

    protected int $height = 320;

    protected int $max_packet_size = 2048;

    public string $consumer = 'st7796';

    public ST7796MADControl $mad_ctrl;

    public ST7796ColorMode $color_mode = ST7796ColorMode::COLOR16;

    public ST7796DisplayInversionControl $inversion_ctrl;

    public ST7796DisplayFunctionControl $display_fn_ctrl;

    public ST7796DisplayOutputCtrlAdjust $output_adjust;

    public ST7796PowerControl2 $power_control_2;

    public ST7796PowerControl3 $power_control_3;

    public ST7796VCOMControl $v_com_ctrl;

    public ST7796GammaPositive $gamma_positive;

    public ST7796GammaNegative $gamma_negative;

    public ?SPIConnectionBuilder $connection = null;

    public function __construct(
        public SPIConnectionBuilder $spi_connection,
        public GPIOConnectionBuilder $gpio_connection
    ) {
        $this->mad_ctrl = new ST7796MADControl(
            false,
            false,
            true,
            false,
            true,
            false,
        );
        $this->inversion_ctrl = new ST7796DisplayInversionControl;
        $this->display_fn_ctrl = new ST7796DisplayFunctionControl;
        $this->output_adjust = new ST7796DisplayOutputCtrlAdjust;
        $this->power_control_2 = new ST7796PowerControl2;
        $this->power_control_3 = new ST7796PowerControl3;
        $this->v_com_ctrl = new ST7796VCOMControl;
        $this->gamma_positive = new ST7796GammaPositive;
        $this->gamma_negative = new ST7796GammaNegative;
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

    public function madControl(ST7796MADControl $control): static
    {
        $this->mad_ctrl = $control;

        return $this;
    }

    public function colorMode(ST7796ColorMode $color_mode): static
    {
        $this->color_mode = $color_mode;

        return $this;
    }

    public function inversionControl(ST7796DisplayInversionControl $control): static
    {
        $this->inversion_ctrl = $control;

        return $this;
    }

    public function displayFunctionControl(ST7796DisplayFunctionControl $control): static
    {
        $this->display_fn_ctrl = $control;

        return $this;
    }

    public function displayOutputCtrlAdjust(ST7796DisplayOutputCtrlAdjust $control): static
    {
        $this->output_adjust = $control;

        return $this;
    }

    public function powerControl2(ST7796PowerControl2 $control): static
    {
        $this->power_control_2 = $control;

        return $this;
    }

    public function powerControl3(ST7796PowerControl3 $control): static
    {
        $this->power_control_3 = $control;

        return $this;
    }

    public function vcomControl(ST7796VCOMControl $control): static
    {
        $this->v_com_ctrl = $control;

        return $this;
    }

    public function gammaPositive(ST7796GammaPositive $gamma): static
    {
        $this->gamma_positive = $gamma;

        return $this;
    }

    public function gammaNegative(ST7796GammaNegative $gamma): static
    {
        $this->gamma_negative = $gamma;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function create(): ST7796
    {
        $carrier = $this->connection?->boot();
        if (is_null($carrier)) {
            throw new Exception('A connection was not registered.');
        }

        $gpio = $this->gpio_connection
            ->shareConnectionWith($carrier)
            ->consumer($this->consumer)
            ->boot();

        $carrier = new ST7796SPIAdapter($carrier, $gpio, $this->max_packet_size);

        return new ST7796(
            $carrier,
            $this->width,
            $this->height,
            $this->mad_ctrl,
            $this->color_mode,
            $this->inversion_ctrl,
            $this->display_fn_ctrl,
            $this->output_adjust,
            $this->power_control_2,
            $this->power_control_3,
            $this->v_com_ctrl,
            $this->gamma_positive,
            $this->gamma_negative,
        );
    }
}
