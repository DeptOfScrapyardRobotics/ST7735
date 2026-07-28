<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Concerns;

use DeptOfScrapyardRobotics\Displays\ST77xx\Concerns\ST77xxInternalAPI;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789OpCode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxException;

trait ST7789InternalAPI
{
    use ST77xxInternalAPI;

    protected function command(ST7789OpCode $register_hex, array $command_data = []): void
    {
        $this->transport->command($register_hex->value, $command_data);
    }

    protected function setColorControl(
        ST7789GammaPositive $gamma_positive,
        ST7789GammaNegative $gamma_negative
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

        $this->deviceReset(3000);
        $this->displayOff();
        $this->sleepModeOff();

        $this->setMADControl($this->_mad_ctrl);
        $this->setPixelFormat($this->_color_mode);
        $this->setPorchControl($this->_porch_ctrl);
        $this->setGateControl($this->_gate_ctrl);
        $this->setVComControl($this->_v_com_ctrl);
        $this->setLcmControl($this->_lcm_ctrl);
        $this->setVdvVrhEnable($this->_vdv_vrh_enable);
        $this->setVrh($this->_vrh);
        $this->setVdv($this->_vdv);
        $this->setFrameRateControlNormal($this->_frame_rate_ctrl);
        $this->setPowerControl1($this->_power_control_1);
        $this->setColorControl($this->_gamma_positive, $this->_gamma_negative);

        $this->displayInversionOn();
        $this->setNormalDisplayMode(true);
        $this->displayOn();
    }
}