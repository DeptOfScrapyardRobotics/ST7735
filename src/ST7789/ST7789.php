<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\ST7789;

use DeptOfScrapyardRobotics\Displays\ST77xx\Concerns\ST77xxFillsRgb565;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789FrameRateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789GammaNegative;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789GammaPositive;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789GateControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789LCMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789MADControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789PorchControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789PowerControl1;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789VCOMControl;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789VDVSet;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789VDVVRHEnable;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Breakouts\ST7789VRHSet;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Concerns\ST7789API;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\Enums\ST7789OpCode;
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
class ST7789 extends DisplayPanel implements BootSequence, FullColorDisplay, SupportsPartialRefresh
{
    use ST7789API;
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
        protected ST7789MADControl $_mad_ctrl,
        protected ST7789ColorMode $_color_mode,
        protected ST7789PorchControl $_porch_ctrl,
        protected ST7789GateControl $_gate_ctrl,
        protected ST7789VCOMControl $_v_com_ctrl,
        protected ST7789LCMControl $_lcm_ctrl,
        protected ST7789VDVVRHEnable $_vdv_vrh_enable,
        protected ST7789VRHSet $_vrh,
        protected ST7789VDVSet $_vdv,
        protected ST7789FrameRateControl $_frame_rate_ctrl,
        protected ST7789PowerControl1 $_power_control_1,
        protected ST7789GammaPositive $_gamma_positive,
        protected ST7789GammaNegative $_gamma_negative,
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

        $this->command(ST7789OpCode::WRITE_MEMORY_START);
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
        int $width = 240,
        int $height = 240,
        int $max_packet_size = 2048,
        int $x_offset = 0,
        int $y_offset = 0,
        ?ST7789MADControl $mad_ctrl = null,
        ST7789ColorMode $color_mode = ST7789ColorMode::COLOR16,
        ?ST7789PorchControl $porch_ctrl = null,
        ?ST7789GateControl $gate_ctrl = null,
        ?ST7789VCOMControl $v_com_ctrl = null,
        ?ST7789LCMControl $lcm_ctrl = null,
        ?ST7789VDVVRHEnable $vdv_vrh_enable = null,
        ?ST7789VRHSet $vrh = null,
        ?ST7789VDVSet $vdv = null,
        ?ST7789FrameRateControl $frame_rate_ctrl = null,
        ?ST7789PowerControl1 $power_control_1 = null,
        ?ST7789GammaPositive $gamma_positive = null,
        ?ST7789GammaNegative $gamma_negative = null,
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
            $porch_ctrl,
            $gate_ctrl,
            $v_com_ctrl,
            $lcm_ctrl,
            $vdv_vrh_enable,
            $vrh,
            $vdv,
            $frame_rate_ctrl,
            $power_control_1,
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
        int $width = 240,
        int $height = 240,
        int $max_packet_size = 2048,
        int $x_offset = 0,
        int $y_offset = 0,
        ?ST7789MADControl $mad_ctrl = null,
        ST7789ColorMode $color_mode = ST7789ColorMode::COLOR16,
        ?ST7789PorchControl $porch_ctrl = null,
        ?ST7789GateControl $gate_ctrl = null,
        ?ST7789VCOMControl $v_com_ctrl = null,
        ?ST7789LCMControl $lcm_ctrl = null,
        ?ST7789VDVVRHEnable $vdv_vrh_enable = null,
        ?ST7789VRHSet $vrh = null,
        ?ST7789VDVSet $vdv = null,
        ?ST7789FrameRateControl $frame_rate_ctrl = null,
        ?ST7789PowerControl1 $power_control_1 = null,
        ?ST7789GammaPositive $gamma_positive = null,
        ?ST7789GammaNegative $gamma_negative = null,
        bool $boot_now = true,
    ): static {
        $transport = new ST77xxCarrierTransport(spi: $spi, dc: $dc, rst: $rst);

        $porch_ctrl ??= new ST7789PorchControl;
        $gate_ctrl ??= new ST7789GateControl;
        $v_com_ctrl ??= new ST7789VCOMControl;
        $lcm_ctrl ??= new ST7789LCMControl;
        $vdv_vrh_enable ??= new ST7789VDVVRHEnable;
        $vrh ??= new ST7789VRHSet;
        $vdv ??= new ST7789VDVSet;
        $frame_rate_ctrl ??= new ST7789FrameRateControl;
        $power_control_1 ??= new ST7789PowerControl1;
        $mad_ctrl ??= new ST7789MADControl(
            false,
            false,
            false,
            false,
            false,
            false,
        );
        $gamma_positive ??= new ST7789GammaPositive;
        $gamma_negative ??= new ST7789GammaNegative;

        return new static(
            $transport,
            $width,
            $height,
            $max_packet_size,
            $x_offset,
            $y_offset,
            $mad_ctrl,
            $color_mode,
            $porch_ctrl,
            $gate_ctrl,
            $v_com_ctrl,
            $lcm_ctrl,
            $vdv_vrh_enable,
            $vrh,
            $vdv,
            $frame_rate_ctrl,
            $power_control_1,
            $gamma_positive,
            $gamma_negative,
            $boot_now,
        );
    }
}
