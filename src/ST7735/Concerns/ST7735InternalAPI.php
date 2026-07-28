<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\Concerns\ST77xxInternalAPI;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735IdleModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735NormalFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PartialModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl3;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl4;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl5;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735VCOMControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735OpCode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxException;

trait ST7735InternalAPI
{
    use ST77xxInternalAPI;

    protected function command(ST7735OpCode $register_hex, array $command_data = []): void
    {
        $this->transport->command($register_hex->value, $command_data);
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
        ST7735PowerControl3 $pwr_ctrl3,
        ST7735PowerControl4 $pwr_ctrl4,
        ST7735PowerControl5 $pwr_ctrl5,
        ST7735VCOMControl1 $v_com_ctrl,
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

    /**
     * @throws ST77xxException
     */
    protected function _boot(): void
    {
        $this->transport = $this->transport->maxPacketSize($this->max_packet_size);

        $this->deviceReset(10000);
        $this->displayOff();
        $this->sleepModeOff();
        $this->setFrameRateControl($this->_nfc, $this->_ifc, $this->_pfc);
        $this->setPowerControl(
            $this->_power_control_1,
            $this->_power_control_2,
            $this->_power_control_3,
            $this->_power_control_4,
            $this->_power_control_5,
            $this->_v_com_ctrl,
        );

        $this->setDisplayInversion($this->_invert_display);
        $this->setMADControl($this->_mad_ctrl);
        $this->setPixelFormat($this->_color_mode);
        $this->setColorControl($this->_gamma_positive, $this->_gamma_negative);
        $this->setNormalDisplayMode(true);
        $this->displayOn();

    }
}