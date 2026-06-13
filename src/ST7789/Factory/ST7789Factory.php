<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Factory;

use BareMetal\CircuitFactory;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Adapters\ST7789SPIAdapter;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789FrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789GateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789LCMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789PorchControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789PowerControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789VCOMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789VDVSet;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789VDVVRHEnable;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789VRHSet;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\ST7789;
use Exception;
use Waveforms\Carriers\GPIO\Factory\GPIOConnectionBuilder;
use Waveforms\Carriers\GPIO\GPIOPin;
use Waveforms\Carriers\SPI\Enums\SPIMode;
use Waveforms\Carriers\SPI\Factory\SPIConnectionBuilder;

class ST7789Factory extends CircuitFactory
{
    protected bool $has_dc = false;

    protected bool $has_rst = false;

    protected int $width = 240;

    protected int $height = 240;

    protected int $max_packet_size = 2048;

    public string $consumer = 'st7789';

    public ST7789PorchControl $porch_ctrl;

    public ST7789GateControl $gate_ctrl;

    public ST7789VCOMControl $v_com_ctrl;

    public ST7789LCMControl $lcm_ctrl;

    public ST7789VDVVRHEnable $vdv_vrh_enable;

    public ST7789VRHSet $vrh;

    public ST7789VDVSet $vdv;

    public ST7789FrameRateControl $frame_rate_ctrl;

    public ST7789PowerControl1 $power_control_1;

    public ST7789MADControl $mad_ctrl;

    public ST7789ColorMode $color_mode = ST7789ColorMode::COLOR16;

    public ST7789GammaPositive $gamma_positive;

    public ST7789GammaNegative $gamma_negative;

    public ?SPIConnectionBuilder $connection = null;

    public function __construct(
        public SPIConnectionBuilder $spi_connection,
        public GPIOConnectionBuilder $gpio_connection
    ) {
        $this->porch_ctrl = new ST7789PorchControl;
        $this->gate_ctrl = new ST7789GateControl;
        $this->v_com_ctrl = new ST7789VCOMControl;
        $this->lcm_ctrl = new ST7789LCMControl;
        $this->vdv_vrh_enable = new ST7789VDVVRHEnable;
        $this->vrh = new ST7789VRHSet;
        $this->vdv = new ST7789VDVSet;
        $this->frame_rate_ctrl = new ST7789FrameRateControl;
        $this->power_control_1 = new ST7789PowerControl1;
        $this->mad_ctrl = new ST7789MADControl(
            false,
            false,
            false,
            false,
            false,
            false,
        );
        $this->gamma_positive = new ST7789GammaPositive;
        $this->gamma_negative = new ST7789GammaNegative;
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

    public function porchControl(ST7789PorchControl $control): static
    {
        $this->porch_ctrl = $control;

        return $this;
    }

    public function gateControl(ST7789GateControl $control): static
    {
        $this->gate_ctrl = $control;

        return $this;
    }

    public function vcomControl(ST7789VCOMControl $control): static
    {
        $this->v_com_ctrl = $control;

        return $this;
    }

    public function lcmControl(ST7789LCMControl $control): static
    {
        $this->lcm_ctrl = $control;

        return $this;
    }

    public function vdvVrhEnable(ST7789VDVVRHEnable $control): static
    {
        $this->vdv_vrh_enable = $control;

        return $this;
    }

    public function vrh(ST7789VRHSet $register): static
    {
        $this->vrh = $register;

        return $this;
    }

    public function vdv(ST7789VDVSet $register): static
    {
        $this->vdv = $register;

        return $this;
    }

    public function frameRateControl(ST7789FrameRateControl $control): static
    {
        $this->frame_rate_ctrl = $control;

        return $this;
    }

    public function powerControl1(ST7789PowerControl1 $control): static
    {
        $this->power_control_1 = $control;

        return $this;
    }

    public function madControl(ST7789MADControl $control): static
    {
        $this->mad_ctrl = $control;

        return $this;
    }

    public function colorMode(ST7789ColorMode $color_mode): static
    {
        $this->color_mode = $color_mode;

        return $this;
    }

    public function gammaPositive(ST7789GammaPositive $gamma): static
    {
        $this->gamma_positive = $gamma;

        return $this;
    }

    public function gammaNegative(ST7789GammaNegative $gamma): static
    {
        $this->gamma_negative = $gamma;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function create(): ST7789
    {
        $carrier = $this->connection?->boot();
        if (is_null($carrier)) {
            throw new Exception('A connection was not registered.');
        }

        $gpio = $this->gpio_connection
            ->shareConnectionWith($carrier)
            ->consumer($this->consumer)
            ->boot();

        $carrier = new ST7789SPIAdapter($carrier, $gpio, $this->max_packet_size);

        return new ST7789(
            $carrier,
            $this->width,
            $this->height,
            $this->porch_ctrl,
            $this->gate_ctrl,
            $this->v_com_ctrl,
            $this->lcm_ctrl,
            $this->vdv_vrh_enable,
            $this->vrh,
            $this->vdv,
            $this->frame_rate_ctrl,
            $this->power_control_1,
            $this->mad_ctrl,
            $this->color_mode,
            $this->gamma_positive,
            $this->gamma_negative,
        );
    }
}
