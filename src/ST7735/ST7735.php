<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7735;

use DeptOfScrapyardRobotics\Displays\ST77xx\Concerns\ST77xxFillsRgb565;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735IdleModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735NormalFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PartialModeFrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl2;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl3;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl4;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735PowerControl5;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Breakouts\ST7735VCOMControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Concerns\ST7735API;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735OpCode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxCarrierTransport;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxException;
use Exception;
use GeneralPurposeIO\Circuits\Types\DisplayPanel;
use GeneralPurposeIO\Contracts\Circuits\Attributes\IntegratedCircuit;
use GeneralPurposeIO\Contracts\Circuits\Attributes\Pinout;
use GeneralPurposeIO\Contracts\Circuits\BootSequence;
use GeneralPurposeIO\Digital\DigitalIO;
use GeneralPurposeIO\Digital\DigitalOutputPin;
use GeneralPurposeIO\SPI\SPI;
use GeneralPurposeIO\SPI\SPIDevice;
use ScrapyardIO\Tubes\Contracts\Core\SupportsPartialRefresh;
use ScrapyardIO\Tubes\Contracts\Framebuffers\DumpedBuffer;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use ScrapyardIO\Tubes\Contracts\Panels\FullColorDisplay;

#[IntegratedCircuit(['SPI', 'DigitalIO'])]
#[Pinout(['SPI' => ['driver', 'device', 'chip_select'], 'DigitalIO' => ['driver', 'device', 'dc', 'rst']])]
class ST7735 extends DisplayPanel implements BootSequence, FullColorDisplay, SupportsPartialRefresh
{
    use ST7735API;
    use ST77xxFillsRgb565;

    protected FormatSpec $format_spec;

    /**
     * @throws Exception
     */
    public function __construct(
        protected ST77xxCarrierTransport $transport,
        protected int $width,
        protected int $height,
        protected int $max_packet_size,
        protected int $x_offset,
        protected int $y_offset,
        protected bool $_invert_display,
        protected ST7735NormalFrameRateControl $_nfc,
        protected ST7735IdleModeFrameRateControl $_ifc,
        protected ST7735PartialModeFrameRateControl $_pfc,
        protected ST7735PowerControl1 $_power_control_1,
        protected ST7735PowerControl2 $_power_control_2,
        protected ST7735PowerControl3 $_power_control_3,
        protected ST7735PowerControl4 $_power_control_4,
        protected ST7735PowerControl5 $_power_control_5,
        protected ST7735VCOMControl1 $_v_com_ctrl,
        protected ST7735MADControl $_mad_ctrl,
        protected ST7735ColorMode $_color_mode,
        protected ST7735GammaPositive $_gamma_positive,
        protected ST7735GammaNegative $_gamma_negative,
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

        $this->command(ST7735OpCode::WRITE_MEMORY_START);
        $this->data($frame->raw_data);
    }

    public function close(): void
    {
        $this->transport->close();
    }

    public static function spi(
        string|int $spi_device,
        string|int $chip_select,
        string|int $digital_device,
        int $dc_pin,
        int $rst_pin,
        ?string $spi_adapter = null,
        ?string $digital_adapter = null,
        int $width = 128,
        int $height = 128,
        int $max_packet_size = 2048,
        int $x_offset = 0,
        int $y_offset = 0,
        bool $invert_display = false,
        ?ST7735NormalFrameRateControl $nfc = null,
        ?ST7735IdleModeFrameRateControl $ifc = null,
        ?ST7735PartialModeFrameRateControl $pfc = null,
        ?ST7735PowerControl1 $power_control_1 = null,
        ?ST7735PowerControl2 $power_control_2 = null,
        ?ST7735PowerControl3 $power_control_3 = null,
        ?ST7735PowerControl4 $power_control_4 = null,
        ?ST7735PowerControl5 $power_control_5 = null,
        ?ST7735VCOMControl1 $v_com_ctrl = null,
        ?ST7735MADControl $mad_ctrl = null,
        ST7735ColorMode $color_mode = ST7735ColorMode::COLOR16,
        ?ST7735GammaPositive $gamma_positive = null,
        ?ST7735GammaNegative $gamma_negative = null,
        bool $boot_now = true,
    ): static {

        $bus = SPI::adapter($spi_adapter)->device($spi_device)
            ->mode(0)->speed(8000000)->bus();

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
            $invert_display,
            $nfc,
            $ifc,
            $pfc,
            $power_control_1,
            $power_control_2,
            $power_control_3,
            $power_control_4,
            $power_control_5,
            $v_com_ctrl,
            $mad_ctrl,
            $color_mode,
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
        int $width = 128,
        int $height = 128,
        int $max_packet_size = 2048,
        int $x_offset = 0,
        int $y_offset = 0,
        bool $invert_display = false,
        ?ST7735NormalFrameRateControl $nfc = null,
        ?ST7735IdleModeFrameRateControl $ifc = null,
        ?ST7735PartialModeFrameRateControl $pfc = null,
        ?ST7735PowerControl1 $power_control_1 = null,
        ?ST7735PowerControl2 $power_control_2 = null,
        ?ST7735PowerControl3 $power_control_3 = null,
        ?ST7735PowerControl4 $power_control_4 = null,
        ?ST7735PowerControl5 $power_control_5 = null,
        ?ST7735VCOMControl1 $v_com_ctrl = null,
        ?ST7735MADControl $mad_ctrl = null,
        ST7735ColorMode $color_mode = ST7735ColorMode::COLOR16,
        ?ST7735GammaPositive $gamma_positive = null,
        ?ST7735GammaNegative $gamma_negative = null,
        bool $boot_now = true,
    ): static {

        $transport = new ST77xxCarrierTransport(spi: $spi, dc: $dc, rst: $rst);

        $nfc ??= ST7735NormalFrameRateControl::fromBytes();
        $ifc ??= ST7735IdleModeFrameRateControl::fromBytes();
        $pfc ??= ST7735PartialModeFrameRateControl::fromBytes();

        $power_control_1 ??= new ST7735PowerControl1;
        $power_control_2 ??= new ST7735PowerControl2;
        $power_control_3 ??= new ST7735PowerControl3;
        $power_control_4 ??= new ST7735PowerControl4;
        $power_control_5 ??= new ST7735PowerControl5;
        $v_com_ctrl ??= new ST7735VCOMControl1;

        $mad_ctrl ??= new ST7735MADControl;

        $gamma_positive ??= new ST7735GammaPositive;
        $gamma_negative ??= new ST7735GammaNegative;

        return new static(
            $transport,
            $width,
            $height,
            $max_packet_size,
            $x_offset,
            $y_offset,
            $invert_display,
            $nfc,
            $ifc,
            $pfc,
            $power_control_1,
            $power_control_2,
            $power_control_3,
            $power_control_4,
            $power_control_5,
            $v_com_ctrl,
            $mad_ctrl,
            $color_mode,
            $gamma_positive,
            $gamma_negative,
            $boot_now,
        );
    }

}
