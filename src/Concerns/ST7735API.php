<?php

namespace ScrapyardIO\Libraries\Displays\Drivers\ST7735\Concerns;

trait ST7735API
{
    use ST7735WriteRegisters;

    public function performSoftwareReset(): void
    {
        $this->writeSoftwareReset();
        $this->wait(120);
        // @todo - reset all variables
        $this->partial_mode_on = false;
        $this->display_inverted = false;
        $this->sleeping = true;
    }

    public function putToSleep(): void
    {
        $this->writeEnterSleepMode();
        $this->wait(120);
        $this->sleeping = true;
    }

    public function wakeUp(): void
    {
        $this->writeExitSleepMode();
        $this->wait(120);
        $this->sleeping = false;
    }

    public function togglePartialAddressWindow(): void
    {
        $this->writePartialDisplayModeOn();
        $this->partial_mode_on = true;
    }

    public function toggleNormalDisplayMode(): void
    {
        $this->writeNormalDisplayModeOn();
        $this->partial_mode_on = false;
    }

    public function toggleDisplayInversion(bool $flag): void
    {
        if($flag) $this->writeDisplayInversionOn();
        else $this->writeDisplayInversionOff();

        $this->display_inverted = $flag;
    }

    public function setGammaCurve(): void
    {
        $byte = bitsbyte([
            (int)$this->gamma_correction_control['GC0'], (int)$this->gamma_correction_control['GC1'],
            (int)$this->gamma_correction_control['GC2'], (int)$this->gamma_correction_control['GC3'],
            0,0,0,0
        ]);
        $this->writeGammaSet($byte);
    }

    public function turnDisplayOff(): void
    {
        $this->writeToggleDisplay(false);
    }

    public function turnDisplayOn(): void
    {
        $this->writeToggleDisplay();
    }

    public function setYRange(int $min, int $max): void
    {
        $y_start = $min + $this->y_offset;
        $y_end   = $max + $this->y_offset;

        $this->writeRowAddressSet(
            ($y_start >> 8) & 0xFF, $y_start & 0xFF,  // Start row (high, low)
            ($y_end >> 8) & 0xFF, $y_end & 0xFF   // End row (high, low)
        );
    }

    public function setXRange(int $min, int $max): void
    {
        $x_start = $min + $this->x_offset;
        $x_end   = $max + $this->x_offset;

        $this->writeColumnAddressSet(
            ($x_start >> 8) & 0xFF, $x_start & 0xFF,  // Start column (high, low)
            ($x_end >> 8) & 0xFF, $x_end & 0xFF   // End column (high, low)
        );
    }

    public function turnTearingOff(): void
    {
        $this->writeToggleTearing(false);
    }

    public function turnTearingOn(): void
    {
        $this->writeToggleTearing();
    }

    public function setMADControl(): void
    {
        $this->writeMADControl(
            bitsbyte(array_reverse(array_values($this->memory_access_control)))
        );
    }

    public function idleModeOff(): void
    {
        $this->writeIdleMode(false);
    }

    public function idleModeOn(): void
    {
        $this->writeIdleMode();
    }

    public function setColorMode(): void
    {
        $this->writeColorMode($this->color_mode->value);
        $this->writeAdjustGammaPositive(array_values($this->gamma_positive));
        $this->writeAdjustGammaNegative(array_values($this->gamma_negative));
    }

    protected function setFrameRateControl(): void
    {
        $this->writeFrameControl1(array_values($this->frame_control_normal));
        $this->writeFrameControl2(array_values($this->frame_control_idle));
        $this->writeFrameControl3(array_values($this->frame_control_partial));
        $this->writeInversionControl($this->inversion_mode->value);
    }

    protected function setPowerControls(): void
    {
        $this->writePowerControl1(array_values($this->power_control_1));
        $this->writePowerControl2(array_values($this->power_control_2));
        $this->writePowerControl3(array_values($this->power_control_3));
        $this->writePowerControl4(array_values($this->power_control_4));
        $this->writePowerControl5(array_values($this->power_control_5));
        $this->writeVComControl(array_values($this->vcom_control));

    }
}
