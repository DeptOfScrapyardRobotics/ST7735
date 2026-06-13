<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735IdleModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735NormalFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PartialModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735OpCode;

trait ST7735InternalAPI
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

    protected function setFrameRateControl(
        ST7735NormalFrameRateControl $nfc,
        ST7735IdleModeFrameRateControl $ifc,
        ST7735PartialModeFrameRateControl $pfc
    ): void {
        $this->setFrameRateControlNormal($nfc);
        $this->setFrameRateControlIdle($ifc);
        $this->setFrameRateControlPartial($pfc);
    }

    protected function setPowerControl(
        ST7735PowerControl1 $pwr_ctrl1,
        ST7735PowerControl2 $pwr_ctrl2,
        $pwr_ctrl3,
        $pwr_ctrl4,
        $pwr_ctrl5,
        $v_com_ctrl,
    ): void {
        $this->setPowerControl1($pwr_ctrl1);
        $this->setPowerControl2($pwr_ctrl2);
        $this->setPowerControl3($pwr_ctrl3);
        $this->setPowerControl4($pwr_ctrl4);
        $this->setPowerControl5($pwr_ctrl5);
        $this->setVComControl($v_com_ctrl);
    }

    protected function setColorControl(
        ST7735GammaPositive $gamma_positive,
        ST7735GammaNegative $gamma_negative
    ): void {
        $this->setGammaPositive($gamma_positive);
        $this->setGammaNegative($gamma_negative);
    }

    protected function command(ST7735OpCode $register_hex, array $command_data = []): void
    {
        $this->carrier->command($register_hex, $command_data);
    }

    protected function data(array $data): void
    {
        $this->carrier->data($data);
    }
}
