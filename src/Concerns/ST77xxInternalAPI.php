<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\Concerns;

use Fabricate\Contracts\Framebuffers\Enums\BitDepth;
use Fabricate\Contracts\Framebuffers\Enums\Endianness;
use Fabricate\Contracts\Framebuffers\Enums\PixelFormat;
use Fabricate\Contracts\Framebuffers\Enums\ScanDirection;
use Fabricate\Contracts\NutsAndBolts\BootScaffolding;
use Fabricate\Framebuffers\FormatSpec;
use Fabricate\NutsAndBolts\Concerns\Splices16Bits;

trait ST77xxInternalAPI
{
    use BootScaffolding, Splices16Bits;

    protected function data(array $data): void
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