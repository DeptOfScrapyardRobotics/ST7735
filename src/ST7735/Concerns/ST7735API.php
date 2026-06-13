<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Concerns;

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
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735OpCode;
use Exception;

trait ST7735API
{
    use ST7735InternalAPI;

    public function displayOn(): void
    {
        $this->command(ST7735OpCode::TOGGLE_DISPLAY_ON);
        $this->display_on = true;
    }

    public function displayOff(): void
    {
        $this->command(ST7735OpCode::TOGGLE_DISPLAY_OFF);
        $this->display_on = false;
    }

    public function sleepModeOn(): void
    {
        $this->command(ST7735OpCode::ENTER_SLEEP_MODE);
        $this->sleep_mode_on = true;
    }

    public function sleepModeOff(): void
    {
        $this->command(ST7735OpCode::EXIT_SLEEP_MODE);
        usleep(120000);
        $this->sleep_mode_on = false;
    }

    public function setFrameRateControlNormal(ST7735NormalFrameRateControl $control): void
    {
        $this->command(ST7735OpCode::FRAME_RATE_CONTROL_NORMAL, $control->toBytes());
    }

    public function setFrameRateControlIdle(ST7735IdleModeFrameRateControl $control): void
    {
        $this->command(ST7735OpCode::FRAME_RATE_CONTROL_IDLE, $control->toBytes());
    }

    public function setFrameRateControlPartial(ST7735PartialModeFrameRateControl $control): void
    {
        $this->command(ST7735OpCode::FRAME_RATE_CONTROL_PARTIAL, $control->toBytes());
    }

    public function setPowerControl1(ST7735PowerControl1 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_1, $register->toBytes());
    }

    public function setPowerControl2(ST7735PowerControl2 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_2, [$register->toByte()]);
    }

    public function setPowerControl3(ST7735PowerControl3 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_3, $register->toBytes());
    }

    public function setPowerControl4(ST7735PowerControl4 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_4, $register->toBytes());
    }

    public function setPowerControl5(ST7735PowerControl5 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_5, $register->toBytes());
    }

    public function setVComControl(ST7735VCOMControl1 $register): void
    {
        $this->command(ST7735OpCode::VCOM_CONTROL_1, [$register->toByte()]);
    }

    public function displayInversionOn(): void
    {
        $this->command(ST7735OpCode::DISPLAY_INVERSION_ON);
    }

    public function displayInversionOff(): void
    {
        $this->command(ST7735OpCode::DISPLAY_INVERSION_OFF);
    }

    public function displayNormalMode(): void
    {
        $this->command(ST7735OpCode::NORMAL_MODE_ON);
    }

    public function displayPartialMode(): void
    {
        $this->command(ST7735OpCode::PARTIAL_MODE_ON);
    }

    public function setMADControl(ST7735MADControl $control): void
    {
        $this->command(ST7735OpCode::MEMORY_ACCESS_CONTROL, [$control->toByte()]);
    }

    public function setGammaPositive(ST7735GammaPositive $gamma_positive): void
    {
        $this->command(ST7735OpCode::GAMMA_CORRECTION_POSITIVE, $gamma_positive->toBytes());
    }

    public function setGammaNegative(ST7735GammaNegative $gamma_negative): void
    {
        $this->command(ST7735OpCode::GAMMA_CORRECTION_NEGATIVE, $gamma_negative->toBytes());
    }

    /**
     * @throws Exception
     */
    public function setPixelFormat(ST7735ColorMode|int $color_mode): void
    {
        if (is_int($color_mode)) {
            $color_mode = match ($color_mode) {
                12 => ST7735ColorMode::COLOR12,
                16 => ST7735ColorMode::COLOR16,
                18 => ST7735ColorMode::COLOR18,
                default => throw new Exception("Invalid color mode: $color_mode")
            };
        }

        $this->command(ST7735OpCode::SET_PIXEL_FORMAT, [$color_mode->value]);
        $this->_color_mode = $color_mode;
    }

    public function setAddressWindow(int $x, int $y, int $width, int $height): void
    {
        $x_start = $x + $this->x_offset;
        $x_end = $x_start + $width - 1;
        $y_start = $y + $this->y_offset;
        $y_end = $y_start + $height - 1;
        $this->command(ST7735OpCode::SET_COLUMN_ADDRESS, [
            ($x_start >> 8) & 0xFF, $x_start & 0xFF, ($x_end >> 8) & 0xFF, $x_end & 0xFF,
        ]);
        $this->command(ST7735OpCode::SET_ROW_ADDRESS, [
            ($y_start >> 8) & 0xFF, $y_start & 0xFF, ($y_end >> 8) & 0xFF, $y_end & 0xFF,
        ]);
    }

    public function writeFrame(array $data): void
    {
        $this->command(ST7735OpCode::WRITE_MEMORY_START);
        $this->data($data);
    }
}
