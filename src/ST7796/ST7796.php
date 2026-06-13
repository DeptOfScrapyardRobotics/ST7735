<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Adapters\ST7796DataCarrier;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Concerns\ST7796API;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796DisplayFunctionControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796DisplayInversionControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796DisplayOutputCtrlAdjust;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796PowerControl3;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\DataObjects\ST7796VCOMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Exceptions\ST7796Exception;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Factory\ST7796Factory;
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
class ST7796 extends EmbeddedDisplay implements FullColorDisplayInterface
{
    use ST7796API;

    protected bool $booted = false;

    /**
     * @throws Exception
     */
    public function __construct(
        protected readonly ST7796DataCarrier $carrier,
        int $width,
        int $height,
        ST7796MADControl $mad_ctrl,
        protected ST7796ColorMode $color_mode,
        ST7796DisplayInversionControl $inversion_ctrl,
        ST7796DisplayFunctionControl $display_fn_ctrl,
        ST7796DisplayOutputCtrlAdjust $output_adjust,
        ST7796PowerControl2 $pwr_ctrl2,
        ST7796PowerControl3 $pwr_ctrl3,
        ST7796VCOMControl $v_com_ctrl,
        ST7796GammaPositive $gamma_positive,
        ST7796GammaNegative $gamma_negative
    ) {
        $this->boot(
            $mad_ctrl,
            $color_mode,
            $inversion_ctrl,
            $display_fn_ctrl,
            $output_adjust,
            $pwr_ctrl2,
            $pwr_ctrl3,
            $v_com_ctrl,
            $gamma_positive,
            $gamma_negative
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
            'mad_control' => $this->setMADControl($value),
            'pixel_format' => $this->setPixelFormat($value),
            'inversion_control' => $this->setDisplayInversionControl($value),
            'display_function_control' => $this->setDisplayFunctionControl($value),
            'display_output_ctrl_adjust' => $this->setDisplayOutputCtrlAdjust($value),
            'power_control2' => $this->setPowerControl2($value),
            'power_control3' => $this->setPowerControl3($value),
            'v_com_control' => $this->setVComControl($value),
            'display_inversion_enabled' => $this->setDisplayInversion((bool) $value),
            'color_gamma_positive' => $this->setGammaPositive($value),
            'color_gamma_negative' => $this->setGammaNegative($value),
            'normal_mode_on' => $this->setNormalDisplayMode((bool) $value),
            default => throw ST7796Exception::invalidProperty($name)
        };
    }

    /**
     * @throws Exception
     */
    protected function boot(
        ST7796MADControl $mad_ctrl,
        ST7796ColorMode $color_mode,
        ST7796DisplayInversionControl $inversion_ctrl,
        ST7796DisplayFunctionControl $display_fn_ctrl,
        ST7796DisplayOutputCtrlAdjust $output_adjust,
        ST7796PowerControl2 $pwr_ctrl2,
        ST7796PowerControl3 $pwr_ctrl3,
        ST7796VCOMControl $v_com_ctrl,
        ST7796GammaPositive $gamma_positive,
        ST7796GammaNegative $gamma_negative
    ): void {
        if (! $this->booted) {
            $this->carrier->reset();

            $this->displayOff();
            $this->sleepModeOff();

            $this->commandSetEnable();

            $this->setMADControl($mad_ctrl);
            $this->setPixelFormat($color_mode);
            $this->setDisplayInversionControl($inversion_ctrl);
            $this->setDisplayFunctionControl($display_fn_ctrl);
            $this->setDisplayOutputCtrlAdjust($output_adjust);
            $this->setPowerControl2($pwr_ctrl2);
            $this->setPowerControl3($pwr_ctrl3);
            $this->setVComControl($v_com_ctrl);
            $this->setColorControl($gamma_positive, $gamma_negative);

            $this->commandSetDisable();

            $this->displayInversionOff();
            $this->setNormalDisplayMode(true);
            $this->displayOn();

            $this->booted = true;
        }
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

    /**
     * @throws Exception
     */
    public static function connection(string $driver): ST7796Factory
    {
        return new ST7796Factory(
            SPI::connection($driver),
            GPIO::connection($driver)
        );
    }
}
