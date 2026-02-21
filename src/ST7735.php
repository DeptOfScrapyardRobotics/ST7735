<?php

namespace ScrapyardIO\Libraries\Displays\Drivers\ST7735;

use ScrapyardIO\Libraries\Displays\Contracts\AddressWindowSetting;
use ScrapyardIO\Libraries\Displays\Displays\TFTColorDisplay;
use ScrapyardIO\Libraries\Displays\Drivers\ST7735\Concerns\ST7735API;
use ScrapyardIO\Libraries\Displays\Drivers\ST7735\Enums\Properties\ST7735ColorMode;
use ScrapyardIO\Libraries\Displays\Drivers\ST7735\Enums\Properties\ST7735InversionMode;

class ST7735 extends TFTColorDisplay implements AddressWindowSetting
{
    use ST7735API;
    protected int $width = 128;
    protected int $height = 160;
    protected int $x_offset = 0;
    protected int $y_offset = 0;

    protected bool $sleeping = true;
    protected bool $partial_mode_on = false;
    protected bool $display_inverted = false;
    protected ST7735ColorMode $color_mode = ST7735ColorMode::COLOR16;
    protected ST7735InversionMode $inversion_mode = ST7735InversionMode::FRAME_INVERSION;

    protected array $gamma_correction_control = [
        'GC3' => false, // Curve 4 1=G2.2; 0=G1.0;
        'GC2' => true,  // Curve 3 1=G1.8; 0=G2.5;
        'GC1' => true,  // Curve 2 1=G2.5; 0=G2.2;
        'GC0' => false, // Curve 1 1=G1.0; 0=G1.8;
    ];

    protected array $memory_access_control = [
        'Y_ORDER' => false,
        'X_ORDER' => true,
        'XY_EXCHANGE' => true,
        'V_REFRESH_ORDER' => false, // 1=Bottom2Top; 0=Top2Bottom;
        'RGB_BGR_ORDER' => true, // 1=BGR; 0=RGB;
        'H_REFRESH_ORDER' => false, // 1=Right2Left; 0=Left2Right;
        '-2' => false,
        '-1' => false,
    ];

    protected array $frame_control_normal = [
        'clock_cycles'                  =>  1,  // Values 0 - 15
        'blank_lines_before_each_frame' =>  44, // Values 0 - 63
        'blank_lines_after_each_frame'  =>  45, // Values 0 - 63
    ];

    protected array $frame_control_idle = [
        'clock_cycles'                  =>  1,  // Values 0 - 15
        'blank_lines_before_each_frame' =>  44, // Values 0 - 63
        'blank_lines_after_each_frame'  =>  45, // Values 0 - 63
    ];

    protected array $frame_control_partial = [
        'dot_inversion_clock_cycles'        => 1,   // Values 0 - 15
        'dot_inversion_blank_lines_before'  => 44,  // Values 0 - 63
        'dot_inversion_blank_lines_after'   => 45,  // Values 0 - 63

        'line_inversion_clock_cycles'       => 1,   // Values 0 - 15
        'line_inversion_blank_lines_before' => 44,  // Values 0 - 63
        'line_inversion_blank_lines_after'  => 45   // Values 0 - 63
    ];

    protected array $power_control_1 = [
        'avdd_voltage_level' => 0xA2,        // Analog supply voltage
        'gvdd_voltage' => 0x02,              // Gate driver voltage (2.6V)
        'mode' => 0x84                       // AUTO mode enabled
    ];

    protected array $power_control_2 = [
        'gate_voltages' => 0xC5              // VGH=3×AVDD, VGL=-10V
    ];

    protected array $power_control_3 = [
        'op_amp_current_normal_mode' => 0x0A,  // Medium-low drive strength
        'boost_frequency' => 0x00              // DC-DC converter freq
    ];

    protected array $power_control_4 = [
        'op_amp_current_idle_mode' => 0x8A,    // BCLK/2 + medium current
        'op_amp_current_adjustment' => 0x2A    // Fine-tuning
    ];

    protected array $power_control_5 = [
        'op_amp_current_partial_mode' => 0x8A,  // BCLK/2 + medium current
        'op_amp_current_adjustment' => 0xEE     // Higher drive strength
    ];

    protected array $vcom_control = [
        'vcomh_voltage' => 0x0E              // Common voltage level
    ];

    protected array $gamma_positive = [
        0x02, 0x1c, 0x07, 0x12,
        0x37, 0x32, 0x29, 0x2d,
        0x29, 0x25, 0x2B, 0x39,
        0x00, 0x01, 0x03, 0x10
    ];

    protected array $gamma_negative = [
        0x03, 0x1d, 0x07, 0x06,
        0x2E, 0x2C, 0x29, 0x2D,
        0x2E, 0x2E, 0x37, 0x3F,
        0x00, 0x00, 0x02, 0x10
    ];

    public function small(): void
    {
        $this->width = 90;
        $this->height = 160;
    }

    public function square(): void
    {
        $this->width = 128;
        $this->height = 128;
    }

    public function normal(): void
    {
        $this->width = 128;
        $this->height = 160;
    }

    public function offset(int $x, int $y): void
    {
        $this->x_offset = $x;
        $this->y_offset = $y;
    }

    public function colorMode(ST7735ColorMode $color_mode): void
    {
        $this->color_mode = $color_mode;
    }

    public function inversionMode(ST7735InversionMode $inversion_mode): void
    {
        $this->inversion_mode = $inversion_mode;
    }

    public function topBottomRowAddresses(): void
    {
        $this->memory_access_control['Y_ORDER'] = false;
    }

    public function bottomTopRowAddresses(): void
    {
        $this->memory_access_control['Y_ORDER'] = true;
    }

    public function leftRightColumnAddresses(): void
    {
        $this->memory_access_control['X_ORDER'] = false;
    }

    public function rightLeftColumnAddresses(): void
    {
        $this->memory_access_control['X_ORDER'] = true;
    }

    public function pixelDirectionVertical(): void
    {
        $this->memory_access_control['XY_EXCHANGE'] = true;
    }

    public function pixelDirectionHorizontal(): void
    {
        $this->memory_access_control['XY_EXCHANGE'] = false;
    }

    public function bottomTopRefresh(): void
    {
        $this->memory_access_control['V_REFRESH_ORDER'] = true;
    }

    public function topBottomRefresh(): void
    {
        $this->memory_access_control['V_REFRESH_ORDER'] = false;
    }

    public function rgbColor(): void
    {
        $this->memory_access_control['RGB_BGR_ORDER'] = false;
    }

    public function bgrColor(): void
    {
        $this->memory_access_control['RGB_BGR_ORDER'] = true;
    }

    public function leftRightRefresh(): void
    {
        $this->memory_access_control['H_REFRESH_ORDER'] = false;
    }

    public function rightLeftRefresh(): void
    {
        $this->memory_access_control['H_REFRESH_ORDER'] = true;
    }

    public function start(): static
    {
        $this->resetDisplay();
        $this->turnDisplayOff();
        $this->wakeUp();
        $this->setFrameRateControl();
        $this->setPowerControls();
        $this->toggleDisplayInversion($this->display_inverted);
        $this->setMADControl();
        $this->setColorMode();
        $this->toggleNormalDisplayMode();
        $this->turnDisplayOn();

        return $this;
    }

    protected function resetDisplay(): void
    {
        $this->rstHigh();
        $this->wait(10);

        $this->rstLow();
        $this->wait(10);

        $this->rstHigh();
        $this->wait(120);
    }

    public function bitsPerPixel(): int
    {
        return $this->color_mode->bitsPerPixel();
    }

    public function setAddressWindow(int $x_min, int $x_max, int $y_min, int $y_max): void
    {
        $this->setYRange($y_min, $y_max);
        $this->setXRange($x_min, $x_max);
        $this->startWrite();
    }
}
