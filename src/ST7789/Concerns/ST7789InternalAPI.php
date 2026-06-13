<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\DataObjects\ST7789GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789OpCode;

trait ST7789InternalAPI
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
        ST7789GammaPositive $gamma_positive,
        ST7789GammaNegative $gamma_negative
    ): void {
        $this->setGammaPositive($gamma_positive);
        $this->setGammaNegative($gamma_negative);
    }

    protected function command(ST7789OpCode $register_hex, array $command_data = []): void
    {
        $this->carrier->command($register_hex, $command_data);
    }

    protected function data(array $data): void
    {
        $this->carrier->data($data);
    }
}
