<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Adapters\ST7735DataCarrier;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Concerns\ST7735API;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735IdleModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735NormalFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PartialModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl3;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl4;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735PowerControl5;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\DataObjects\ST7735VCOMControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Exceptions\ST7735Exception;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Factory\ST7735Factory;
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
class ST7735 extends EmbeddedDisplay implements FullColorDisplayInterface
{
    use ST7735API;

    protected bool $booted = false;

    protected bool $display_on = false;

    protected bool $sleep_mode_on = false;

    /**
     * @throws Exception
     */
    public function __construct(
        protected readonly ST7735DataCarrier $carrier,
        int $width,
        int $height,
        ST7735NormalFrameRateControl $nfc,
        ST7735IdleModeFrameRateControl $ifc,
        ST7735PartialModeFrameRateControl $pfc,
        ST7735PowerControl1 $pwr_ctrl1,
        ST7735PowerControl2 $pwr_ctrl2,
        ST7735PowerControl3 $pwr_ctrl3,
        ST7735PowerControl4 $pwr_ctrl4,
        ST7735PowerControl5 $pwr_ctrl5,
        ST7735VCOMControl1 $v_com_ctrl,
        ST7735MADControl $mad_ctrl,
        protected ST7735ColorMode $_color_mode,
        ST7735GammaPositive $gamma_positive,
        ST7735GammaNegative $gamma_negative,
        protected int $x_offset,
        protected int $y_offset,
        protected bool $invert_display,
    ) {
        $this->boot(
            $nfc,
            $ifc,
            $pfc,
            $pwr_ctrl1,
            $pwr_ctrl2,
            $pwr_ctrl3,
            $pwr_ctrl4,
            $pwr_ctrl5,
            $v_com_ctrl,
            $mad_ctrl,
            $_color_mode,
            $gamma_positive,
            $gamma_negative,
            $invert_display
        );
        parent::__construct($width, $height);
    }

    public function display(DumpedBuffer $buffer): void
    {
        $width = $buffer->width ?? $this->width();
        $height = $buffer->height ?? $this->height();
        $this->setAddressWindow($buffer->origin_x, $buffer->origin_y, $width, $height);
        $this->writeFrame($buffer->raw_data);
    }

    /**
     * @throws Exception
     */
    public function __set(string $name, mixed $value): void
    {
        match ($name) {
            'display_on' => $this->setDisplay((bool) $value),
            'sleep_mode_enabled' => $this->setSleepMode((bool) $value),
            'frctl_normal' => $this->setFrameRateControlNormal($value),
            'frctl_idle' => $this->setFrameRateControlIdle($value),
            'frctl_partial' => $this->setFrameRateControlPartial($value),
            'power_control1' => $this->setPowerControl1($value),
            'power_control2' => $this->setPowerControl2($value),
            'power_control3' => $this->setPowerControl3($value),
            'power_control4' => $this->setPowerControl4($value),
            'power_control5' => $this->setPowerControl5($value),
            'v_com_control' => $this->setVComControl($value),
            'display_inversion_enabled' => $this->setDisplayInversion((bool) $value),
            'mad_control' => $this->setMADControl($value),
            'color_mode' => $this->setPixelFormat($value),
            'color_gamma_positive' => $this->setGammaPositive($value),
            'color_gamma_negative' => $this->setGammaNegative($value),
            'normal_mode_on' => $this->setNormalDisplayMode((bool) $value),
            default => throw ST7735Exception::invalidProperty($name)
        };
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'display_on' => $this->display_on,
            'sleep_mode_enabled' => $this->sleep_mode_on,
            'color_mode' => $this->_color_mode,
            default => throw ST7735Exception::invalidProperty($name)
        };
    }

    /**
     * @throws Exception
     */
    protected function boot(
        ST7735NormalFrameRateControl $nfc,
        ST7735IdleModeFrameRateControl $ifc,
        ST7735PartialModeFrameRateControl $pfc,
        ST7735PowerControl1 $pwr_ctrl1,
        ST7735PowerControl2 $pwr_ctrl2,
        ST7735PowerControl3 $pwr_ctrl3,
        ST7735PowerControl4 $pwr_ctrl4,
        ST7735PowerControl5 $pwr_ctrl5,
        ST7735VCOMControl1 $v_com_ctrl,
        ST7735MADControl $mad_ctrl,
        ST7735ColorMode $color_mode,
        ST7735GammaPositive $gamma_positive,
        ST7735GammaNegative $gamma_negative,
        bool $invert_display,
    ): void {
        if (! $this->booted) {
            $this->carrier->reset();

            $this->displayOff();
            $this->sleepModeOff();
            $this->setFrameRateControl($nfc, $ifc, $pfc);
            $this->setPowerControl(
                $pwr_ctrl1,
                $pwr_ctrl2,
                $pwr_ctrl3,
                $pwr_ctrl4,
                $pwr_ctrl5,
                $v_com_ctrl,
            );

            if ($invert_display) {
                $this->displayInversionOn();
            } else {
                $this->displayInversionOff();
            }
            $this->setMADControl($mad_ctrl);
            $this->setPixelFormat($color_mode);
            $this->setColorControl($gamma_positive, $gamma_negative);
            $this->setNormalDisplayMode(true);
            $this->displayOn();

            $this->booted = true;
        }
    }

    public function generateFormatSpec(): FormatSpec
    {
        return new FormatSpec(
            PixelFormat::ROW_MAJOR,
            BitDepth::from($this->_color_mode->bitsPerPixel()),
            ScanDirection::TOP_TO_BOTTOM,
            endianness: Endianness::MSB,
        );
    }

    /**
     * @throws Exception
     */
    public static function connection(string $driver): ST7735Factory
    {
        return new ST7735Factory(
            SPI::connection($driver),
            GPIO::connection($driver)
        );
    }
}
