<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Concerns;

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
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789OpCode;
use Exception;

trait ST7789API
{
    use ST7789InternalAPI;

    public function displayOn(): void
    {
        $this->command(ST7789OpCode::TOGGLE_DISPLAY_ON);
    }

    public function displayOff(): void
    {
        $this->command(ST7789OpCode::TOGGLE_DISPLAY_OFF);
    }

    public function sleepModeOn(): void
    {
        $this->command(ST7789OpCode::ENTER_SLEEP_MODE);
    }

    public function sleepModeOff(): void
    {
        $this->command(ST7789OpCode::EXIT_SLEEP_MODE);
        usleep(120000);
    }

    public function setPorchControl(ST7789PorchControl $control): void
    {
        $this->command(ST7789OpCode::PORCH_CONTROL, $control->toBytes());
    }

    public function setGateControl(ST7789GateControl $control): void
    {
        $this->command(ST7789OpCode::GATE_CONTROL, [$control->toByte()]);
    }

    public function setVComControl(ST7789VCOMControl $control): void
    {
        $this->command(ST7789OpCode::VCOM_SETTING, [$control->toByte()]);
    }

    public function setLcmControl(ST7789LCMControl $control): void
    {
        $this->command(ST7789OpCode::LCM_CONTROL, [$control->toByte()]);
    }

    public function setVdvVrhEnable(ST7789VDVVRHEnable $control): void
    {
        $this->command(ST7789OpCode::VDV_VRH_COMMAND_ENABLE, $control->toBytes());
    }

    public function setVrh(ST7789VRHSet $register): void
    {
        $this->command(ST7789OpCode::VRH_SET, [$register->toByte()]);
    }

    public function setVdv(ST7789VDVSet $register): void
    {
        $this->command(ST7789OpCode::VDV_SET, [$register->toByte()]);
    }

    public function setFrameRateControlNormal(ST7789FrameRateControl $control): void
    {
        $this->command(ST7789OpCode::FRAME_RATE_CONTROL_NORMAL, [$control->toByte()]);
    }

    public function setPowerControl1(ST7789PowerControl1 $register): void
    {
        $this->command(ST7789OpCode::POWER_CONTROL_1, $register->toBytes());
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

    public function setMADControl(ST7789MADControl $control): void
    {
        $this->command(ST7789OpCode::MEMORY_ACCESS_CONTROL, [$control->toByte()]);
    }

    public function setGammaPositive(ST7789GammaPositive $gamma_positive): void
    {
        $this->command(ST7789OpCode::GAMMA_CORRECTION_POSITIVE, $gamma_positive->toBytes());
    }

    public function setGammaNegative(ST7789GammaNegative $gamma_negative): void
    {
        $this->command(ST7789OpCode::GAMMA_CORRECTION_NEGATIVE, $gamma_negative->toBytes());
    }

    /**
     * @throws Exception
     */
    public function setPixelFormat(ST7789ColorMode|int $color_mode): void
    {
        if (is_int($color_mode)) {
            $color_mode = match ($color_mode) {
                12 => ST7789ColorMode::COLOR12,
                16 => ST7789ColorMode::COLOR16,
                18 => ST7789ColorMode::COLOR18,
                default => throw new Exception("Invalid color mode: $color_mode")
            };
        }

        $this->command(ST7789OpCode::SET_PIXEL_FORMAT, [$color_mode->value]);
        $this->color_mode = $color_mode;
    }

    public function setAddressWindow(int $x, int $y, int $width, int $height): void
    {
        $x_end = $x + $width - 1;
        $y_end = $y + $height - 1;
        $this->command(ST7789OpCode::SET_COLUMN_ADDRESS, [
            ($x >> 8) & 0xFF, $x & 0xFF, ($x_end >> 8) & 0xFF, $x_end & 0xFF,
        ]);
        $this->command(ST7789OpCode::SET_ROW_ADDRESS, [
            ($y >> 8) & 0xFF, $y & 0xFF, ($y_end >> 8) & 0xFF, $y_end & 0xFF,
        ]);
    }

    public function writeFrame(array $data): void
    {
        $this->command(ST7789OpCode::WRITE_MEMORY_START);
        $this->data($data);
    }
}
