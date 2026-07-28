<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7796;

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796DisplayFunctionControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796DisplayInversionControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796DisplayOutputCtrlAdjust;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796PowerControl3;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Breakouts\ST7796VCOMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Concerns\ST7796API;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\Enums\ST7796OpCode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxCarrierTransport;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxException;
use Exception;
use Fabricate\Contracts\Circuits\Attributes\IntegratedCircuit;
use Fabricate\Contracts\Circuits\IntegratedCircuit as CircuitContract;
use Fabricate\Contracts\Displays\Interfaces\FullColorDisplay;
use Fabricate\Contracts\Displays\Interfaces\PartiallyRefreshable;
use Fabricate\Contracts\NutsAndBolts\BootSequence;
use Fabricate\Framebuffers\DataObjects\DumpedBuffer;
use Fabricate\Framebuffers\FormatSpec;
use GeneralPurposeIO\Digital\DigitalIO;
use GeneralPurposeIO\Digital\DigitalOutputPin;
use GeneralPurposeIO\SPI\SPI;
use GeneralPurposeIO\SPI\SPIDevice;

#[IntegratedCircuit('SPI', 'DigitalIO')]
class ST7796 implements CircuitContract, BootSequence, FullColorDisplay, PartiallyRefreshable
{
    use ST7796API;

    protected FormatSpec $format_spec;

    /**
     * @throws \Exception
     */
    public function __construct(
        protected ST77xxCarrierTransport $transport,
        protected int $width,
        protected int $height,
        protected int $max_packet_size,
        protected int $x_offset,
        protected int $y_offset,
        protected ST7796MADControl $_mad_ctrl,
        protected ST7796ColorMode $_color_mode,
        protected ST7796DisplayInversionControl $_inversion_ctrl,
        protected ST7796DisplayFunctionControl $_display_fn_ctrl,
        protected ST7796DisplayOutputCtrlAdjust $_output_adjust,
        protected ST7796PowerControl2 $_power_control_2,
        protected ST7796PowerControl3 $_power_control_3,
        protected ST7796VCOMControl $_v_com_ctrl,
        protected ST7796GammaPositive $_gamma_positive,
        protected ST7796GammaNegative $_gamma_negative,
        bool $boot_now = false,
    ) {
        $this->format_spec = $this->_generateFormatSpec();

        if ($boot_now) {
            $this->boot();
        }
    }

    public function width(): int
    {
        return $this->width;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function formatSpec(): FormatSpec
    {
        return $this->format_spec;
    }

    public function generateFormatSpec(): FormatSpec
    {
        $this->format_spec = $this->_generateFormatSpec();

        return $this->format_spec;
    }

    /**
     * Frame the target rectangle with the column/row address registers
     * (panel x/y offsets applied by setAddressWindow), open a RAM write, and
     * stream the row-major pixel bytes; the transport chunks them by
     * max_packet_size.
     */
    public function transmit(DumpedBuffer $frame): void
    {
        $this->setAddressWindow(
            $frame->origin_x,
            $frame->origin_y,
            $frame->width ?? $this->width,
            $frame->height ?? $this->height
        );

        $this->command(ST7796OpCode::WRITE_MEMORY_START);
        $this->data($frame->raw_data);
    }

    public function close(): void
    {
        $this->transport->close();
    }

    /**
     * @throws ST77xxException
     */
    public static function spi(
        string|int $spi_device,
        string|int $chip_select,
        string|int $digital_device,
        int $dc_pin,
        int $rst_pin,
        ?string $spi_adapter = null,
        ?string $digital_adapter = null,
        int $width = 480,
        int $height = 320,
        int $max_packet_size = 2048,
        int $x_offset = 0,
        int $y_offset = 0,
        ?ST7796MADControl $mad_ctrl = null,
        ST7796ColorMode $color_mode = ST7796ColorMode::COLOR16,
        ?ST7796DisplayInversionControl $inversion_ctrl = null,
        ?ST7796DisplayFunctionControl $display_fn_ctrl = null,
        ?ST7796DisplayOutputCtrlAdjust $output_adjust = null,
        ?ST7796PowerControl2 $power_control_2 = null,
        ?ST7796PowerControl3 $power_control_3 = null,
        ?ST7796VCOMControl $v_com_ctrl = null,
        ?ST7796GammaPositive $gamma_positive = null,
        ?ST7796GammaNegative $gamma_negative = null,
        bool $boot_now = true,
    ): static {

        $bus = SPI::adapter($spi_adapter)->device($spi_device)
            ->mode(0)->speed(40000000)->bus();

        $spi = $bus->select($chip_select);

        if(!$bus->canServeDigitalPins())
        {
            $bus = DigitalIO::adapter($digital_adapter)->device($digital_device)->bus();
        }

        $dc = $bus->output($dc_pin);
        $rst = $bus->output($rst_pin);

        return static::fromSPIBus($spi, $dc, $rst,
            $width,
            $height,
            $max_packet_size,
            $x_offset,
            $y_offset,
            $mad_ctrl,
            $color_mode,
            $inversion_ctrl,
            $display_fn_ctrl,
            $output_adjust,
            $power_control_2,
            $power_control_3,
            $v_com_ctrl,
            $gamma_positive,
            $gamma_negative,
            $boot_now,
        );
    }

    /**
     * @throws ST77xxException
     * @throws Exception
     */
    public static function fromSPIBus(
        SPIDevice $spi,
        DigitalOutputPin $dc,
        DigitalOutputPin $rst,
        int $width = 480,
        int $height = 320,
        int $max_packet_size = 2048,
        int $x_offset = 0,
        int $y_offset = 0,
        ?ST7796MADControl $mad_ctrl = null,
        ST7796ColorMode $color_mode = ST7796ColorMode::COLOR16,
        ?ST7796DisplayInversionControl $inversion_ctrl = null,
        ?ST7796DisplayFunctionControl $display_fn_ctrl = null,
        ?ST7796DisplayOutputCtrlAdjust $output_adjust = null,
        ?ST7796PowerControl2 $power_control_2 = null,
        ?ST7796PowerControl3 $power_control_3 = null,
        ?ST7796VCOMControl $v_com_ctrl = null,
        ?ST7796GammaPositive $gamma_positive = null,
        ?ST7796GammaNegative $gamma_negative = null,
        bool $boot_now = true,
    ): static {
        $transport = new ST77xxCarrierTransport(spi: $spi, dc: $dc, rst: $rst);

        $mad_ctrl ??= new ST7796MADControl(
            false,
            false,
            true,
            false,
            true,
            false,
        );
        $inversion_ctrl ??= new ST7796DisplayInversionControl;
        $display_fn_ctrl ??= new ST7796DisplayFunctionControl;
        $output_adjust ??= new ST7796DisplayOutputCtrlAdjust;
        $power_control_2 ??= new ST7796PowerControl2;
        $power_control_3 ??= new ST7796PowerControl3;
        $v_com_ctrl ??= new ST7796VCOMControl;
        $gamma_positive ??= new ST7796GammaPositive;
        $gamma_negative ??= new ST7796GammaNegative;

        return new static(
            $transport,
            $width,
            $height,
            $max_packet_size,
            $x_offset,
            $y_offset,
            $mad_ctrl,
            $color_mode,
            $inversion_ctrl,
            $display_fn_ctrl,
            $output_adjust,
            $power_control_2,
            $power_control_3,
            $v_com_ctrl,
            $gamma_positive,
            $gamma_negative,
            $boot_now,
        );
    }
}