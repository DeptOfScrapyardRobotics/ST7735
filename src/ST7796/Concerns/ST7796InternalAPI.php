<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796OpCode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxException;
use DeptOfScrapyardRobotics\Displays\ST77xx\Concerns\ST77xxInternalAPI;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796GammaPositive;

trait ST7796InternalAPI
{
    use ST77xxInternalAPI;

    protected function command(ST7796OpCode $register_hex, array $command_data = []): void
    {
        $this->transport->command($register_hex->value, $command_data);
    }

    protected function setColorControl(
        ST7796GammaPositive $gamma_positive,
        ST7796GammaNegative $gamma_negative
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

        $this->commandSetEnable();

        $this->setMADControl($this->_mad_ctrl);
        $this->setPixelFormat($this->_color_mode);
        $this->setDisplayInversionControl($this->_inversion_ctrl);
        $this->setDisplayFunctionControl($this->_display_fn_ctrl);
        $this->setDisplayOutputCtrlAdjust($this->_output_adjust);
        $this->setPowerControl2($this->_power_control_2);
        $this->setPowerControl3($this->_power_control_3);
        $this->setVComControl($this->_v_com_ctrl);
        $this->setColorControl($this->_gamma_positive, $this->_gamma_negative);

        $this->commandSetDisable();

        $this->displayInversionOff();
        $this->setNormalDisplayMode(true);
        $this->displayOn();
    }
}