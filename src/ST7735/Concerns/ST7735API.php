<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735IdleModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735NormalFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PartialModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl3;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl4;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl5;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735VCOMControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735OpCode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxException;

trait ST7735API
{
    use ST7735InternalAPI;

    protected bool $display_on = false;

    protected bool $sleep_mode_on = false;

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
        $this->_nfc = $control;
    }

    public function setFrameRateControlIdle(ST7735IdleModeFrameRateControl $control): void
    {
        $this->command(ST7735OpCode::FRAME_RATE_CONTROL_IDLE, $control->toBytes());
        $this->_ifc = $control;
    }

    public function setFrameRateControlPartial(ST7735PartialModeFrameRateControl $control): void
    {
        $this->command(ST7735OpCode::FRAME_RATE_CONTROL_PARTIAL, $control->toBytes());
        $this->_pfc = $control;
    }

    public function setPowerControl1(ST7735PowerControl1 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_1, $register->toBytes());
        $this->_power_control_1 = $register;
    }

    public function setPowerControl2(ST7735PowerControl2 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_2, [$register->toByte()]);
        $this->_power_control_2 = $register;
    }

    public function setPowerControl3(ST7735PowerControl3 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_3, $register->toBytes());
        $this->_power_control_3 = $register;
    }

    public function setPowerControl4(ST7735PowerControl4 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_4, $register->toBytes());
        $this->_power_control_4 = $register;
    }

    public function setPowerControl5(ST7735PowerControl5 $register): void
    {
        $this->command(ST7735OpCode::POWER_CONTROL_5, $register->toBytes());
        $this->_power_control_5 = $register;
    }

    public function setVComControl(ST7735VCOMControl1 $register): void
    {
        $this->command(ST7735OpCode::VCOM_CONTROL_1, [$register->toByte()]);
        $this->_v_com_ctrl = $register;

    }

    public function displayInversionOn(): void
    {
        $this->command(ST7735OpCode::DISPLAY_INVERSION_ON);
        $this->_invert_display = true;
    }

    public function displayInversionOff(): void
    {
        $this->command(ST7735OpCode::DISPLAY_INVERSION_OFF);
        $this->_invert_display = false;
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
        $this->_mad_ctrl = $control;
    }

    public function setGammaPositive(ST7735GammaPositive $gamma_positive): void
    {
        $this->command(ST7735OpCode::GAMMA_CORRECTION_POSITIVE, $gamma_positive->toBytes());
        $this->_gamma_positive = $gamma_positive;
    }

    public function setGammaNegative(ST7735GammaNegative $gamma_negative): void
    {
        $this->command(ST7735OpCode::GAMMA_CORRECTION_NEGATIVE, $gamma_negative->toBytes());
        $this->_gamma_negative = $gamma_negative;
    }

    /**
     * @throws ST77xxException
     */
    public function setPixelFormat(ST7735ColorMode|int $color_mode): void
    {
        if (is_int($color_mode)) {
            $color_mode = match ($color_mode) {
                12 => ST7735ColorMode::COLOR12,
                16 => ST7735ColorMode::COLOR16,
                18 => ST7735ColorMode::COLOR18,
                default => throw new ST77xxException("Invalid color mode: $color_mode")
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

    public function setPartialDisplayMode(bool $on): void
    {
        $on ? $this->displayPartialMode() : $this->displayNormalMode();
    }

    public function setNormalDisplayMode(bool $on): void
    {
        $on ? $this->displayNormalMode() : $this->displayPartialMode();
    }

    public function setDisplayInversion(bool $on): void
    {
        $on ? $this->displayInversionOn() : $this->displayInversionOff();
    }

    public function setSleepMode(bool $on): void
    {
        $on ? $this->sleepModeOn() : $this->sleepModeOff();
    }

    public function setDisplay(bool $on): void
    {
        $on ? $this->displayOn() : $this->displayOff();
    }
}