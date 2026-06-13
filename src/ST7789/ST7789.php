<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Adapters\ST7789DataCarrier;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Concerns\ST7789API;
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
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Exceptions\ST7789Exception;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Factory\ST7789Factory;
use Exception;
use RealityInterface\Displays\Attributes\OutputsColor;
use RealityInterface\Displays\Contracts\Applied\FullColorTFT\FullColorDisplayInterface;
use RealityInterface\Displays\EmbeddedDisplay;
use ScrapyardIO\NutsAndBolts\DataObjects\DumpedBuffer;
use ScrapyardIO\NutsAndBolts\DataObjects\FormatSpec;
use ScrapyardIO\NutsAndBolts\Enums\BitDepth;
use ScrapyardIO\NutsAndBolts\Enums\Endianness;
use ScrapyardIO\NutsAndBolts\Enums\PixelFormat;
use ScrapyardIO\NutsAndBolts\Enums\ScanDirection;
use Waveforms\Carriers\GPIO\GPIO;
use Waveforms\Carriers\SPI\SPI;

#[OutputsColor]
class ST7789 extends EmbeddedDisplay implements FullColorDisplayInterface
{
    use ST7789API;

    protected bool $booted = false;

    /**
     * @throws Exception
     */
    public function __construct(
        protected readonly ST7789DataCarrier $carrier,
        int $width,
        int $height,
        ST7789PorchControl $porch_ctrl,
        ST7789GateControl $gate_ctrl,
        ST7789VCOMControl $v_com_ctrl,
        ST7789LCMControl $lcm_ctrl,
        ST7789VDVVRHEnable $vdv_vrh_enable,
        ST7789VRHSet $vrh,
        ST7789VDVSet $vdv,
        ST7789FrameRateControl $frame_rate_ctrl,
        ST7789PowerControl1 $pwr_ctrl1,
        ST7789MADControl $mad_ctrl,
        protected ST7789ColorMode $color_mode,
        ST7789GammaPositive $gamma_positive,
        ST7789GammaNegative $gamma_negative
    ) {
        $this->boot(
            $porch_ctrl,
            $gate_ctrl,
            $v_com_ctrl,
            $lcm_ctrl,
            $vdv_vrh_enable,
            $vrh,
            $vdv,
            $frame_rate_ctrl,
            $pwr_ctrl1,
            $mad_ctrl,
            $color_mode,
            $gamma_positive,
            $gamma_negative
        );
        parent::__construct($width, $height);
    }

    /**
     * @throws Exception
     */
    public function __set(string $name, mixed $value): void
    {
        match ($name) {
            'display_on' => $this->setDisplay((bool) $value),
            'sleep_mode_enabled' => $this->setSleepMode((bool) $value),
            'porch_control' => $this->setPorchControl($value),
            'gate_control' => $this->setGateControl($value),
            'v_com_control' => $this->setVComControl($value),
            'lcm_control' => $this->setLcmControl($value),
            'vdv_vrh_enabled' => $this->setVdvVrhEnable($value),
            'vrh' => $this->setVrh($value),
            'vdv' => $this->setVdv($value),
            'frctl_normal' => $this->setFrameRateControlNormal($value),
            'power_control1' => $this->setPowerControl1($value),
            'display_inversion_enabled' => $this->setDisplayInversion((bool) $value),
            'mad_control' => $this->setMADControl($value),
            'pixel_format' => $this->setPixelFormat($value),
            'color_gamma_positive' => $this->setGammaPositive($value),
            'color_gamma_negative' => $this->setGammaNegative($value),
            'normal_mode_on' => $this->setNormalDisplayMode((bool) $value),
            default => throw ST7789Exception::invalidProperty($name)
        };
    }

    /**
     * @throws Exception
     */
    protected function boot(
        ST7789PorchControl $porch_ctrl,
        ST7789GateControl $gate_ctrl,
        ST7789VCOMControl $v_com_ctrl,
        ST7789LCMControl $lcm_ctrl,
        ST7789VDVVRHEnable $vdv_vrh_enable,
        ST7789VRHSet $vrh,
        ST7789VDVSet $vdv,
        ST7789FrameRateControl $frame_rate_ctrl,
        ST7789PowerControl1 $pwr_ctrl1,
        ST7789MADControl $mad_ctrl,
        ST7789ColorMode $color_mode,
        ST7789GammaPositive $gamma_positive,
        ST7789GammaNegative $gamma_negative
    ): void {
        if (! $this->booted) {
            $this->carrier->reset();

            $this->displayOff();
            $this->sleepModeOff();

            $this->setMADControl($mad_ctrl);
            $this->setPixelFormat($color_mode);
            $this->setPorchControl($porch_ctrl);
            $this->setGateControl($gate_ctrl);
            $this->setVComControl($v_com_ctrl);
            $this->setLcmControl($lcm_ctrl);
            $this->setVdvVrhEnable($vdv_vrh_enable);
            $this->setVrh($vrh);
            $this->setVdv($vdv);
            $this->setFrameRateControlNormal($frame_rate_ctrl);
            $this->setPowerControl1($pwr_ctrl1);
            $this->setColorControl($gamma_positive, $gamma_negative);

            $this->displayInversionOn();
            $this->setNormalDisplayMode(true);
            $this->displayOn();

            $this->booted = true;
        }
    }

    /**
     * @throws Exception
     */
    public static function connection(string $driver): ST7789Factory
    {
        return new ST7789Factory(
            SPI::connection($driver),
            GPIO::connection($driver)
        );
    }

    public function generateFormatSpec(): FormatSpec
    {
        return new FormatSpec(
            PixelFormat::ROW_MAJOR,
            BitDepth::from($this->color_mode->bitsPerPixel()),
            ScanDirection::TOP_TO_BOTTOM,
            endianness: Endianness::MSB,
        );
    }

    public function display(DumpedBuffer $buffer): void
    {
        $width = $buffer->width ?? $this->width();
        $height = $buffer->height ?? $this->height();
        $this->setAddressWindow($buffer->origin_x, $buffer->origin_y, $width, $height);
        $this->writeFrame($buffer->raw_data);
    }
}
