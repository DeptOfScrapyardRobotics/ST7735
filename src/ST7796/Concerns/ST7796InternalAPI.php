<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796OpCode;

trait ST7796InternalAPI
{
    protected function setDisplay(bool $on): void
    {
        $on ? $this->displayOn() : $this->displayOff();
    }

    protected function setSleepMode(bool $on): void
    {
        $on ? $this->sleepModeOn() : $this->sleepModeOff();
    }

    protected function setDisplayInversion(bool $on): void
    {
        $on ? $this->displayInversionOn() : $this->displayInversionOff();
    }

    protected function setNormalDisplayMode(bool $on): void
    {
        $on ? $this->displayNormalMode() : $this->displayPartialMode();
    }

    protected function setPartialDisplayMode(bool $on): void
    {
        $on ? $this->displayPartialMode() : $this->displayNormalMode();
    }

    protected function setColorControl(
        ST7796GammaPositive $gamma_positive,
        ST7796GammaNegative $gamma_negative
    ): void {
        $this->setGammaPositive($gamma_positive);
        $this->setGammaNegative($gamma_negative);
    }

    protected function command(ST7796OpCode $register_hex, array $command_data = []): void
    {
        $this->carrier->command($register_hex, $command_data);
    }

    protected function data(array $data): void
    {
        $this->carrier->data($data);
    }
}
