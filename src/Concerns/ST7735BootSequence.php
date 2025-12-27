<?php

namespace ScrapyardIO\Displays\Color\ST7735\Concerns;

use ScrapyardIO\Displays\Colors\Color;
use ScrapyardIO\Support\DataManipulation\ByteRegister;
use ScrapyardIO\Displays\Color\ST7735\Enums\ST7735Command;
use ScrapyardIO\Displays\Color\ST7735\Enums\ST7735ColorMode;
use ScrapyardIO\Displays\Color\ST7735\Enums\ST7735InversionMode;

trait ST7735BootSequence
{
    protected array $frame_rate_normal = [
        'clock_cycles' => 0x01,
        'blank_lines_before_each_frame' => 0x2C,
        'blank_lines_after_each_frame' => 0x2D
    ];

    protected array $frame_rate_idle = [
        'clock_cycles' => 0x01,
        'blank_lines_before_each_frame' => 0x2C,
        'blank_lines_after_each_frame' => 0x2D
    ];

    protected array $frame_rate_partial = [
        'dot_inversion_clock_cycles' => 0x01,
        'dot_inversion_blank_lines_before' => 0x2C,
        'dot_inversion_blank_lines_after' => 0x2D,

        'line_inversion_clock_cycles' => 0x01,
        'line_inversion_blank_lines_before' => 0x2C,
        'line_inversion_blank_lines_after' => 0x2D
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

    protected int $x_offset = 0;
    protected int $y_offset = 0;
    protected bool $bgr_order_mode = true;              //RGB
    protected bool $bottom_top_refresh = false;         //ML
    protected bool $right_left_refresh = false;         //MH
    protected bool $bottom_top_row_addresses = true;   //MY
    protected bool $pixel_direction_vertical = false;   //MV
    protected bool $right_left_column_addresses = true; //MX

    protected ST7735InversionMode $inversion_mode = ST7735InversionMode::FRAME_INVERSION;
    protected ST7735ColorMode $color_mode = ST7735ColorMode::COLOR18;

    abstract public function wait(int $ms): void;
    abstract public function sendCommand(array $bytes): void;

    protected function turnDisplayOff(): void
    {
        $this->sendCommand([ST7735Command::SOFTWARE_RESET->value]);
        $this->wait(150);
    }

    protected function exitSleepMode(): void
    {
        $this->sendCommand([ST7735Command::EXIT_SLEEP_MODE->value]);
        $this->wait(150);
    }

    protected function setFrameRateControl(): void
    {
        $this->sendCommand([
            ST7735Command::FRAME_RATE_CONTROL_NORMAL->value,
            ...array_values($this->frame_rate_normal)
        ]);

        $this->sendCommand([
            ST7735Command::FRAME_RATE_CONTROL_IDLE->value,
            ...array_values($this->frame_rate_idle)
        ]);

        $this->sendCommand([
            ST7735Command::FRAME_RATE_CONTROL_PARTIAL->value,
            ...array_values($this->frame_rate_partial)
        ]);

        $this->sendCommand([
            ST7735Command::INVERSION_CONTROL->value,
            ...[$this->inversion_mode->value]
        ]);
    }

    protected function setPowerControl(): void
    {
        $this->sendCommand([
            ST7735Command::POWER_CONTROL_1->value,
            ...array_values($this->power_control_1)
        ]);
        $this->sendCommand([
            ST7735Command::POWER_CONTROL_2->value,
            ...array_values($this->power_control_2)
        ]);
        $this->sendCommand([
            ST7735Command::POWER_CONTROL_3->value,
            ...array_values($this->power_control_3)
        ]);
        $this->sendCommand([
            ST7735Command::POWER_CONTROL_4->value,
            ...array_values($this->power_control_4)
        ]);
        $this->sendCommand([
            ST7735Command::POWER_CONTROL_5->value,
            ...array_values($this->power_control_5)
        ]);
        $this->sendCommand([
            ST7735Command::VCOM_CONTROL_1->value,
            ...array_values($this->vcom_control)
        ]);
    }

    protected function turnInversionOff(): void
    {
        $this->sendCommand([ST7735Command::INVERSION_OFF->value]);
    }

    protected function setMADControl(): void
    {
        $this->sendCommand([
            ST7735Command::MEMORY_ACCESS_CONTROL->value,
            ...[$this->memoryAccessControl()]
        ]);
    }

    protected function setColorControl(): void
    {
        $this->sendCommand([
            ST7735Command::SET_PIXEL_FORMAT->value,
            ...[$this->color_mode->value]
        ]);

        $this->sendCommand([
            ST7735Command::GAMMA_CORRECTION_POSITIVE->value,
            ...array_values($this->gamma_positive)
        ]);

        $this->sendCommand([
            ST7735Command::GAMMA_CORRECTION_NEGATIVE->value,
            ...array_values($this->gamma_negative)
        ]);
    }

    protected function setNormalDisplayMode(): void
    {
        $this->sendCommand([ST7735Command::NORMAL_MODE_ON->value]);
        $this->wait(10);
    }

    protected function turnDisplayOn(): void
    {
        $this->sendCommand([ST7735Command::DISPLAY_ON->value]);
        $this->wait(100);
    }

    protected function turnInversionOn(): void
    {
        $this->sendCommand([ST7735Command::INVERSION_ON->value]);
    }

    public function memoryAccessControl(): int
    {
        return (new ByteRegister(0))
            ->update(7, $this->bottom_top_row_addresses)
            ->update(6, $this->right_left_column_addresses)
            ->update(5, $this->pixel_direction_vertical)
            ->update(4, $this->bottom_top_refresh)
            ->update(3, $this->bgr_order_mode)
            ->update(2, $this->right_left_refresh)
            ->update(1, 0)
            ->update(0, 0)
            ->byte;
    }

    protected function startWrite(): void
    {
        $this->sendCommand([ST7735Command::WRITE_MEMORY_START->value]);
    }

    public function display(): static
    {
        $this->setAddressWindow($this->min_y, $this->max_y, $this->min_x, $this->max_x);

        $colors = match($this->color_mode) {
            ST7735ColorMode::COLOR12 => array_map(fn(Color $color) => $color->to12BitInt(), $this->wire->toRows()),
            ST7735ColorMode::COLOR18 => array_map(fn(Color $color) => $color->to18BitInt(), $this->wire->toRows()),
            default => array_map(fn(Color $color) => $color->to16BitInt(), $this->wire->toRows()),
        };

        $payload = [];
        if($this->color_mode == ST7735ColorMode::COLOR16)
        {
            foreach($colors as $color)
            {
                $payload[] = $color >> 8;
                $payload[] = $color & 0xFF;
            }
        }
        elseif($this->color_mode == ST7735ColorMode::COLOR18)
        {
            foreach($colors as $color)
            {
                $payload[] = (($color >> 12) & 0x3F) << 2;
                $payload[] = (($color >> 6) & 0x3F) << 2;
                $payload[] = ($color & 0x3F) << 2;
            }
        }
        elseif($this->color_mode == ST7735ColorMode::COLOR12)
        {
            foreach($colors as $color)
            {
                $payload[] = (($color >> 8) & 0x0F) << 4;
                $payload[] = (($color >> 4) & 0x0F) << 4;
                $payload[] = ($color & 0x0F) << 4;
            }
        }

        $this->startWrite();
        foreach(array_chunk($payload, $this->max_packet_size) as $chunk)
        {
            $this->sendData($chunk);
        }

        return $this;
    }

    protected function setAddressWindow(int $y_min, int $y_max, int $x_min, int $x_max): void
    {
        $this->setXRange($x_min, $x_max);
        $this->setYRange($y_min, $y_max);
    }

    public function setYRange(int $min, int $max): void
    {
        $y_start = $min + $this->y_offset;
        $y_end   = $max + $this->y_offset;

        $this->sendCommand([
            ST7735Command::SET_ROW_ADDRESS->value,
            ($y_start >> 8) & 0xFF, $y_start & 0xFF,  // Start row (high, low)
            ($y_end >> 8) & 0xFF, $y_end & 0xFF   // End row (high, low)
        ]);
    }

    public function setXRange(int $min, int $max): void
    {
        $x_start = $min + $this->x_offset;
        $x_end   = $max + $this->x_offset;
        $this->sendCommand([
            ST7735Command::SET_COLUMN_ADDRESS->value,
            ($x_start >> 8) & 0xFF, $x_start & 0xFF,  // Start column (high, low)
            ($x_end >> 8) & 0xFF, $x_end & 0xFF   // End column (high, low)

        ]);
    }
}
