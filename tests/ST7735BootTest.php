<?php

namespace DeptOfScrapyardRobotics\Tests;

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
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735ColorMode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\Enums\ST7735OpCode;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\ST7735;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxCarrierTransport;
use PHPUnit\Framework\TestCase;

final class ST7735BootTest extends TestCase
{
    public function testColdBootUsesACompleteST7735RInitializationSequence(): void
    {
        $transport = new RecordingST77xxTransport;

        $panel = new ST7735(
            $transport,
            128,
            128,
            2048,
            2,
            3,
            false,
            ST7735NormalFrameRateControl::fromBytes(),
            ST7735IdleModeFrameRateControl::fromBytes(),
            ST7735PartialModeFrameRateControl::fromBytes(),
            new ST7735PowerControl1,
            new ST7735PowerControl2,
            new ST7735PowerControl3,
            new ST7735PowerControl4,
            new ST7735PowerControl5,
            new ST7735VCOMControl1,
            new ST7735MADControl,
            ST7735ColorMode::COLOR16,
            new ST7735GammaPositive,
            new ST7735GammaNegative,
            true,
        );

        self::assertTrue($panel->hasBooted());
        self::assertSame(150000, $transport->resetDelay);
        self::assertSame(2048, $transport->packetSize);

        $commands = array_column($transport->commands, 0);

        self::assertSame(ST7735OpCode::SOFTWARE_RESET->value, $commands[0]);
        self::assertSame(ST7735OpCode::EXIT_SLEEP_MODE->value, $commands[1]);
        self::assertContains(ST7735OpCode::INVERSION_CONTROL->value, $commands);
        self::assertContains(ST7735OpCode::DISPLAY_INVERSION_OFF->value, $commands);
        self::assertSame(ST7735OpCode::TOGGLE_DISPLAY_ON->value, $commands[array_key_last($commands)]);

        self::assertSame([0x01, 0x2C, 0x2D], $this->dataFor($transport, ST7735OpCode::FRAME_RATE_CONTROL_NORMAL));
        self::assertSame([0x07], $this->dataFor($transport, ST7735OpCode::INVERSION_CONTROL));
        self::assertSame([0xA2, 0x02, 0x84], $this->dataFor($transport, ST7735OpCode::POWER_CONTROL_1));
        self::assertSame([0xC8], $this->dataFor($transport, ST7735OpCode::MEMORY_ACCESS_CONTROL));
        self::assertSame([ST7735ColorMode::COLOR16->value], $this->dataFor($transport, ST7735OpCode::SET_PIXEL_FORMAT));
    }

    /**
     * @return array<int, int>
     */
    private function dataFor(RecordingST77xxTransport $transport, ST7735OpCode $opcode): array
    {
        foreach ($transport->commands as [$command, $data]) {
            if ($command === $opcode->value) {
                return $data;
            }
        }

        self::fail("Command {$opcode->name} was not sent.");
    }
}

final class RecordingST77xxTransport extends ST77xxCarrierTransport
{
    /**
     * @var array<int, array{0: int, 1: array<int, int>}>
     */
    public array $commands = [];

    public ?int $resetDelay = null;

    public ?int $packetSize = null;

    public function __construct() {}

    public function command(int $register, array $command_data = []): int
    {
        $this->commands[] = [$register, $command_data];

        return 1;
    }

    public function reset(int $sleep_time): void
    {
        $this->resetDelay = $sleep_time;
    }

    public function maxPacketSize(int $size): static
    {
        $this->packetSize = $size;

        return $this;
    }
}
