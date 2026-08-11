<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\Concerns;

use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\BitDepth;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\Endianness;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\PixelFormat;
use ScrapyardIO\Tubes\Contracts\Framebuffers\Enums\ScanDirection;
use ScrapyardIO\Tubes\Contracts\Framebuffers\FormatSpec;
use Fabricate\NutsAndBolts\Concerns\Splices16Bits;
use GeneralPurposeIO\Contracts\Circuits\BootScaffolding;

trait ST77xxInternalAPI
{
    use BootScaffolding, Splices16Bits;

    /**
     * @param  array<int, int>|string  $data
     */
    protected function data(array|string $data): void
    {
        $this->transport->data($data);
    }

    protected function deviceReset(int $sleep_time): void
    {
        $this->transport->reset($sleep_time);
    }

    protected function _generateFormatSpec(): FormatSpec
    {
        return new FormatSpec(
            PixelFormat::ROW_MAJOR,
            BitDepth::from($this->_color_mode->bitsPerPixel()),
            ScanDirection::TOP_TO_BOTTOM,
            endianness: Endianness::MSB,
        );
    }
}
