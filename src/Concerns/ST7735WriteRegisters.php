<?php

namespace ScrapyardIO\Libraries\Displays\Drivers\ST7735\Concerns;


use ScrapyardIO\Libraries\Displays\Drivers\ST7735\Enums\CommandRegister\ST7735CommandRegister;

trait ST7735WriteRegisters
{
    protected function writeSoftwareReset(): bool
    {
        return $this->command(ST7735CommandRegister::SOFTWARE_RESET->value);
    }

    protected function writeEnterSleepMode(): bool
    {
        return $this->command(ST7735CommandRegister::ENTER_SLEEP_MODE->value);
    }

    protected function writeExitSleepMode(): bool
    {
        return $this->command(ST7735CommandRegister::EXIT_SLEEP_MODE->value);
    }

    protected function writePartialDisplayModeOn(): bool
    {
        return $this->command(ST7735CommandRegister::PARTIAL_MODE_ON->value);
    }

    protected function writeNormalDisplayModeOn(): bool
    {
        return $this->command(ST7735CommandRegister::PARTIAL_MODE_ON->value);
    }

    protected function writeDisplayInversionOn(): bool
    {
        return $this->command(ST7735CommandRegister::INVERSION_ON->value);
    }

    protected function writeDisplayInversionOff(): bool
    {
        return $this->command(ST7735CommandRegister::INVERSION_OFF->value);
    }

    protected function writeGammaSet(int $byte): bool
    {
        return $this->command(ST7735CommandRegister::GAMMA_CURVE_SELECT->value, [$byte]);
    }

    protected function writeToggleDisplay(bool $flag = true): bool
    {
        return $this->command($flag
            ? ST7735CommandRegister::DISPLAY_ON->value
            : ST7735CommandRegister::DISPLAY_OFF->value
        );
    }

    protected function writeColumnAddressSet(int $xs_high, int $xs_low, int $xe_high, int $xe_low): bool
    {
        return $this->command(ST7735CommandRegister::SET_COLUMN_ADDRESS->value, [$xs_high, $xs_low, $xe_high, $xe_low]);
    }
    protected function writeRowAddressSet(int $ys_high, int $ys_low, int $ye_high, int $ye_low): bool
    {
        return $this->command(ST7735CommandRegister::SET_ROW_ADDRESS->value, [$ys_high, $ys_low, $ye_high, $ye_low]);
    }

    protected function startWrite(): void
    {
        $this->command(ST7735CommandRegister::WRITE_MEMORY_START->value);
    }

    protected function writeRGBSet(array $lut): bool
    {
        return $this->command(ST7735CommandRegister::SET_ROW_ADDRESS->value, $lut);
    }

    protected function writeToggleTearing(bool $flag = true): bool
    {
        return $this->command($flag
            ? ST7735CommandRegister::TEARING_LINE_EFFECT_ON->value
            : ST7735CommandRegister::TEARING_LINE_EFFECT_OFF->value
        );
    }

    protected function writeMADControl(int $byte): bool
    {
        return $this->command(ST7735CommandRegister::MEMORY_ACCESS_CONTROL->value, [$byte]);
    }

    protected function writeIdleMode(bool $flag = true): bool
    {
        return $this->command($flag
            ? ST7735CommandRegister::IDLE_MODE_ON->value
            : ST7735CommandRegister::IDLE_MODE_OFF->value
        );
    }

    protected function writeColorMode(int $byte): bool
    {
        return $this->command(ST7735CommandRegister::SET_PIXEL_FORMAT->value, [$byte]);
    }

    protected function writeFrameControl1(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::FRAME_RATE_CONTROL_NORMAL->value, $bytes);
    }

    protected function writeFrameControl2(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::FRAME_RATE_CONTROL_IDLE->value, $bytes);
    }

    protected function writeFrameControl3(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::FRAME_RATE_CONTROL_PARTIAL->value, $bytes);
    }

    protected function writeInversionControl(int $byte): bool
    {
        return $this->command(ST7735CommandRegister::INVERSION_CONTROL->value, [$byte]);
    }

    protected function writePowerControl1(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::POWER_CONTROL_1->value, $bytes);
    }

    protected function writePowerControl2(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::POWER_CONTROL_2->value, $bytes);
    }

    protected function writePowerControl3(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::POWER_CONTROL_3->value, $bytes);
    }

    protected function writePowerControl4(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::POWER_CONTROL_4->value, $bytes);
    }

    protected function writePowerControl5(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::POWER_CONTROL_5->value, $bytes);
    }

    protected function writeVComControl(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::VCOM_CONTROL_1->value, $bytes);
    }

    protected function writeAdjustGammaPositive(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::GAMMA_CORRECTION_POSITIVE->value, $bytes);
    }

    protected function writeAdjustGammaNegative(array $bytes): bool
    {
        return $this->command(ST7735CommandRegister::GAMMA_CORRECTION_NEGATIVE->value, $bytes);
    }
}
