<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Concerns;

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
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796CommandSet;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796OpCode;
use Exception;

trait ST7796API
{
    use ST7796InternalAPI;

    public function displayOn(): void
    {
        $this->command(ST7796OpCode::TOGGLE_DISPLAY_ON);
    }

    public function displayOff(): void
    {
        $this->command(ST7796OpCode::TOGGLE_DISPLAY_OFF);
    }

    public function sleepModeOn(): void
    {
        $this->command(ST7796OpCode::ENTER_SLEEP_MODE);
    }

    public function sleepModeOff(): void
    {
        $this->command(ST7796OpCode::EXIT_SLEEP_MODE);
        usleep(120000);
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
    }

    public function setDisplayFunctionControl(ST7796DisplayFunctionControl $control): void
    {
        $this->command(ST7796OpCode::DISPLAY_FUNCTION_CONTROL, $control->toBytes());
    }

    public function setDisplayOutputCtrlAdjust(ST7796DisplayOutputCtrlAdjust $control): void
    {
        $this->command(ST7796OpCode::DISPLAY_OUTPUT_CTRL_ADJUST, $control->toBytes());
    }

    public function setPowerControl2(ST7796PowerControl2 $register): void
    {
        $this->command(ST7796OpCode::POWER_CONTROL_2, [$register->toByte()]);
    }

    public function setPowerControl3(ST7796PowerControl3 $register): void
    {
        $this->command(ST7796OpCode::POWER_CONTROL_3, [$register->toByte()]);
    }

    public function setVComControl(ST7796VCOMControl $register): void
    {
        $this->command(ST7796OpCode::VCOM_CONTROL, [$register->toByte()]);
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
    }

    public function setGammaPositive(ST7796GammaPositive $gamma_positive): void
    {
        $this->command(ST7796OpCode::GAMMA_CORRECTION_POSITIVE, $gamma_positive->toBytes());
    }

    public function setGammaNegative(ST7796GammaNegative $gamma_negative): void
    {
        $this->command(ST7796OpCode::GAMMA_CORRECTION_NEGATIVE, $gamma_negative->toBytes());
    }

    /**
     * @throws Exception
     */
    public function setPixelFormat(ST7796ColorMode|int $color_mode): void
    {
        if (is_int($color_mode)) {
            $color_mode = match ($color_mode) {
                12 => ST7796ColorMode::COLOR12,
                16 => ST7796ColorMode::COLOR16,
                18 => ST7796ColorMode::COLOR18,
                default => throw new Exception("Invalid color mode: $color_mode")
            };
        }

        $this->command(ST7796OpCode::SET_PIXEL_FORMAT, [$color_mode->value]);
        $this->color_mode = $color_mode;
    }

    public function setAddressWindow(int $x, int $y, int $width, int $height): void
    {
        $x_end = $x + $width - 1;
        $y_end = $y + $height - 1;
        $this->command(ST7796OpCode::SET_COLUMN_ADDRESS, [
            ($x >> 8) & 0xFF, $x & 0xFF, ($x_end >> 8) & 0xFF, $x_end & 0xFF,
        ]);
        $this->command(ST7796OpCode::SET_ROW_ADDRESS, [
            ($y >> 8) & 0xFF, $y & 0xFF, ($y_end >> 8) & 0xFF, $y_end & 0xFF,
        ]);
    }

    public function writeFrame(array $data): void
    {
        $this->command(ST7796OpCode::WRITE_MEMORY_START);
        $this->data($data);
    }
}
