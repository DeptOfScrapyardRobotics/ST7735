<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796DisplayFunctionControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796DisplayInversionControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796DisplayOutputCtrlAdjust;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796PowerControl3;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796VCOMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796CommandSet;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796OpCode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxException;

trait ST7796API
{
    use ST7796InternalAPI;

    protected bool $display_on = false;

    protected bool $sleep_mode_on = false;

    public function displayOn(): void
    {
        $this->command(ST7796OpCode::TOGGLE_DISPLAY_ON);
        $this->display_on = true;
    }

    public function displayOff(): void
    {
        $this->command(ST7796OpCode::TOGGLE_DISPLAY_OFF);
        $this->display_on = false;
    }

    public function sleepModeOn(): void
    {
        $this->command(ST7796OpCode::ENTER_SLEEP_MODE);
        $this->sleep_mode_on = true;
    }

    public function sleepModeOff(): void
    {
        $this->command(ST7796OpCode::EXIT_SLEEP_MODE);
        usleep(120000);
        $this->sleep_mode_on = false;
    }

    public function commandSetEnable(): void
    {
        $this->command(ST7796OpCode::COMMAND_SET_CONTROL, [ST7796CommandSet::ENABLE_EXTENSION_PART_1->value]);
        $this->command(ST7796OpCode::COMMAND_SET_CONTROL, [ST7796CommandSet::ENABLE_EXTENSION_PART_2->value]);
    }

    public function commandSetDisable(): void
    {
        $this->command(ST7796OpCode::COMMAND_SET_CONTROL, [ST7796CommandSet::DISABLE_EXTENSION_PART_1->value]);
        $this->command(ST7796OpCode::COMMAND_SET_CONTROL, [ST7796CommandSet::DISABLE_EXTENSION_PART_2->value]);
    }

    public function setDisplayInversionControl(ST7796DisplayInversionControl $control): void
    {
        $this->command(ST7796OpCode::DISPLAY_INVERSION_CONTROL, [$control->toByte()]);
        $this->_inversion_ctrl = $control;
    }

    public function setDisplayFunctionControl(ST7796DisplayFunctionControl $control): void
    {
        $this->command(ST7796OpCode::DISPLAY_FUNCTION_CONTROL, $control->toBytes());
        $this->_display_fn_ctrl = $control;
    }

    public function setDisplayOutputCtrlAdjust(ST7796DisplayOutputCtrlAdjust $control): void
    {
        $this->command(ST7796OpCode::DISPLAY_OUTPUT_CTRL_ADJUST, $control->toBytes());
        $this->_output_adjust = $control;
    }

    public function setPowerControl2(ST7796PowerControl2 $register): void
    {
        $this->command(ST7796OpCode::POWER_CONTROL_2, [$register->toByte()]);
        $this->_power_control_2 = $register;
    }

    public function setPowerControl3(ST7796PowerControl3 $register): void
    {
        $this->command(ST7796OpCode::POWER_CONTROL_3, [$register->toByte()]);
        $this->_power_control_3 = $register;
    }

    public function setVComControl(ST7796VCOMControl $register): void
    {
        $this->command(ST7796OpCode::VCOM_CONTROL, [$register->toByte()]);
        $this->_v_com_ctrl = $register;
    }

    public function displayInversionOn(): void
    {
        $this->command(ST7796OpCode::DISPLAY_INVERSION_ON);
    }

    public function displayInversionOff(): void
    {
        $this->command(ST7796OpCode::DISPLAY_INVERSION_OFF);
    }

    public function displayNormalMode(): void
    {
        $this->command(ST7796OpCode::NORMAL_MODE_ON);
    }

    public function displayPartialMode(): void
    {
        $this->command(ST7796OpCode::PARTIAL_MODE_ON);
    }

    public function setMADControl(ST7796MADControl $control): void
    {
        $this->command(ST7796OpCode::MEMORY_ACCESS_CONTROL, [$control->toByte()]);
        $this->_mad_ctrl = $control;
    }

    public function setGammaPositive(ST7796GammaPositive $gamma_positive): void
    {
        $this->command(ST7796OpCode::GAMMA_CORRECTION_POSITIVE, $gamma_positive->toBytes());
        $this->_gamma_positive = $gamma_positive;
    }

    public function setGammaNegative(ST7796GammaNegative $gamma_negative): void
    {
        $this->command(ST7796OpCode::GAMMA_CORRECTION_NEGATIVE, $gamma_negative->toBytes());
        $this->_gamma_negative = $gamma_negative;
    }

    /**
     * @throws ST77xxException
     */
    public function setPixelFormat(ST7796ColorMode|int $color_mode): void
    {
        if (is_int($color_mode)) {
            $color_mode = match ($color_mode) {
                12 => ST7796ColorMode::COLOR12,
                16 => ST7796ColorMode::COLOR16,
                18 => ST7796ColorMode::COLOR18,
                default => throw new ST77xxException("Invalid color mode: $color_mode")
            };
        }

        $this->command(ST7796OpCode::SET_PIXEL_FORMAT, [$color_mode->value]);
        $this->_color_mode = $color_mode;
    }

    public function setAddressWindow(int $x, int $y, int $width, int $height): void
    {
        $x_start = $x + $this->x_offset;
        $x_end = $x_start + $width - 1;
        $y_start = $y + $this->y_offset;
        $y_end = $y_start + $height - 1;
        $this->command(ST7796OpCode::SET_COLUMN_ADDRESS, [
            ($x_start >> 8) & 0xFF, $x_start & 0xFF, ($x_end >> 8) & 0xFF, $x_end & 0xFF,
        ]);
        $this->command(ST7796OpCode::SET_ROW_ADDRESS, [
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
        $on ? $this->sleepModeOn() : $this->sleepModeOff();
    }

    public function setDisplay(bool $on): void
    {
        $on ? $this->displayOn() : $this->displayOff();
    }
}