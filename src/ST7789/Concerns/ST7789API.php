<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789FrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789GateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789LCMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789PorchControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789PowerControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789VCOMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789VDVSet;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789VDVVRHEnable;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789VRHSet;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789OpCode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxException;

trait ST7789API
{
    use ST7789InternalAPI;

    protected bool $display_on = false;

    protected bool $sleep_mode_on = false;

    public function displayOn(): void
    {
        $this->command(ST7789OpCode::TOGGLE_DISPLAY_ON);
        $this->display_on = true;
    }

    public function displayOff(): void
    {
        $this->command(ST7789OpCode::TOGGLE_DISPLAY_OFF);
        $this->display_on = false;
    }

    public function sleepModeOn(): void
    {
        $this->command(ST7789OpCode::ENTER_SLEEP_MODE);
        $this->sleep_mode_on = true;
    }

    public function sleepModeOff(): void
    {
        $this->command(ST7789OpCode::EXIT_SLEEP_MODE);
        usleep(120000);
        $this->sleep_mode_on = false;
    }

    public function setMADControl(ST7789MADControl $control): void
    {
        $this->command(ST7789OpCode::MEMORY_ACCESS_CONTROL, [$control->toByte()]);
        $this->_mad_ctrl = $control;
    }

    /**
     * @throws ST77xxException
     */
    public function setPixelFormat(ST7789ColorMode|int $color_mode): void
    {
        if (is_int($color_mode)) {
            $color_mode = match ($color_mode) {
                12 => ST7789ColorMode::COLOR12,
                16 => ST7789ColorMode::COLOR16,
                18 => ST7789ColorMode::COLOR18,
                default => throw new ST77xxException("Invalid color mode: $color_mode")
            };
        }

        $this->command(ST7789OpCode::SET_PIXEL_FORMAT, [$color_mode->value]);
        $this->_color_mode = $color_mode;
    }

    public function setPorchControl(ST7789PorchControl $control): void
    {
        $this->command(ST7789OpCode::PORCH_CONTROL, $control->toBytes());
        $this->_porch_ctrl = $control;
    }

    public function setGateControl(ST7789GateControl $control): void
    {
        $this->command(ST7789OpCode::GATE_CONTROL, [$control->toByte()]);
        $this->_gate_ctrl = $control;
    }

    public function setVComControl(ST7789VCOMControl $control): void
    {
        $this->command(ST7789OpCode::VCOM_SETTING, [$control->toByte()]);
        $this->_v_com_ctrl = $control;
    }

    public function setLcmControl(ST7789LCMControl $control): void
    {
        $this->command(ST7789OpCode::LCM_CONTROL, [$control->toByte()]);
        $this->_lcm_ctrl = $control;
    }

    public function setVdvVrhEnable(ST7789VDVVRHEnable $control): void
    {
        $this->command(ST7789OpCode::VDV_VRH_COMMAND_ENABLE, $control->toBytes());
        $this->_vdv_vrh_enable = $control;
    }

    public function setVrh(ST7789VRHSet $register): void
    {
        $this->command(ST7789OpCode::VRH_SET, [$register->toByte()]);
        $this->_vrh = $register;
    }

    public function setVdv(ST7789VDVSet $register): void
    {
        $this->command(ST7789OpCode::VDV_SET, [$register->toByte()]);
        $this->_vdv = $register;
    }

    public function setFrameRateControlNormal(ST7789FrameRateControl $control): void
    {
        $this->command(ST7789OpCode::FRAME_RATE_CONTROL_NORMAL, [$control->toByte()]);
        $this->_frame_rate_ctrl = $control;
    }

    public function setPowerControl1(ST7789PowerControl1 $register): void
    {
        $this->command(ST7789OpCode::POWER_CONTROL_1, $register->toBytes());
        $this->_power_control_1 = $register;
    }

    public function setGammaPositive(ST7789GammaPositive $gamma_positive): void
    {
        $this->command(ST7789OpCode::GAMMA_CORRECTION_POSITIVE, $gamma_positive->toBytes());
        $this->_gamma_positive = $gamma_positive;
    }

    public function setGammaNegative(ST7789GammaNegative $gamma_negative): void
    {
        $this->command(ST7789OpCode::GAMMA_CORRECTION_NEGATIVE, $gamma_negative->toBytes());
        $this->_gamma_negative = $gamma_negative;
    }

    public function displayInversionOn(): void
    {
        $this->command(ST7789OpCode::DISPLAY_INVERSION_ON);
    }

    public function displayInversionOff(): void
    {
        $this->command(ST7789OpCode::DISPLAY_INVERSION_OFF);
    }

    public function displayNormalMode(): void
    {
        $this->command(ST7789OpCode::NORMAL_MODE_ON);
    }

    public function displayPartialMode(): void
    {
        $this->command(ST7789OpCode::PARTIAL_MODE_ON);
    }

    public function setAddressWindow(int $x, int $y, int $width, int $height): void
    {
        $x_start = $x + $this->x_offset;
        $x_end = $x_start + $width - 1;
        $y_start = $y + $this->y_offset;
        $y_end = $y_start + $height - 1;
        $this->command(ST7789OpCode::SET_COLUMN_ADDRESS, [
            ($x_start >> 8) & 0xFF, $x_start & 0xFF, ($x_end >> 8) & 0xFF, $x_end & 0xFF,
        ]);
        $this->command(ST7789OpCode::SET_ROW_ADDRESS, [
            ($y_start >> 8) & 0xFF, $y_start & 0xFF, ($y_end >> 8) & 0xFF, $y_end & 0xFF,
        ]);
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
        $on ? $this->displayOn() : $this->displayOff();
    }

    public function setDisplay(bool $on): void
    {
        $on ? $this->displayOn() : $this->displayOff();
    }
}