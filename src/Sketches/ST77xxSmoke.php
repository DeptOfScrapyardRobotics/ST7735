<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\Sketches;

use DeptOfScrapyardRobotics\Displays\ST77xx\Enums\ST77xxCatalogIc;
use DeptOfScrapyardRobotics\Displays\ST77xx\Enums\ST77xxSmokeColor;
use Fabricate\Contracts\Sketches\Attributes\Sketch as SketchAttribute;
use Fabricate\Contracts\Sketches\SketchLoopResult;
use Fabricate\Sketches\Sketch;
use GeneralPurposeIO\Circuits\Types\DisplayPanel;
use GeneralPurposeIO\Contracts\Circuits\IntegratedCircuit;
use GeneralPurposeIO\Core\MagicAliases\Circuit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

/**
 * Hardware smoke demo for any provisioned ST77xx profile.
 *
 * Provisions only via Circuit::profile() — no tubes/window/UX driving.
 * Cycles solid RGB565 fills until you stop the sketch (Ctrl-C).
 */
#[SketchAttribute('st77xx-smoke')]
class ST77xxSmoke extends Sketch
{
    protected string $description = 'Smoke-test a provisioned ST77xx display (Ctrl-C to end)';

    protected ?IntegratedCircuit $panel = null;

    protected ?string $profileName = null;

    protected bool $stopRequested = false;

    protected bool $announced = false;

    protected int $phase = 0;

    protected int $lastPaintNs = 0;

    public function configureCommand(Command $command): void
    {
        $command->addOption(
            'profile',
            null,
            InputOption::VALUE_OPTIONAL,
            'circuits.php profile name (must use an ST77xx ic)',
        );
    }

    public function boot(): void
    {
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);
            $stop = function (): void {
                $this->stopRequested = true;
            };
            pcntl_signal(SIGINT, $stop);
            pcntl_signal(SIGTERM, $stop);
        }

        $profiles = $this->st77xxProfiles();

        if ($profiles === []) {
            $this->error(
                'No ST77xx profiles found in config/circuits.php. '.
                'Create one with: php workshop st77xx:make-profile, then re-run.'
            );

            return;
        }

        $requested = $this->option('profile');
        if (is_string($requested) && $requested !== '') {
            if (! isset($profiles[$requested])) {
                $this->error(
                    "Profile [{$requested}] is missing or its ic is not st7735/st7789/st7796."
                );

                return;
            }
            $this->profileName = $requested;
        } elseif (count($profiles) === 1) {
            $this->profileName = array_key_first($profiles);
        } else {
            $this->profileName = $this->choice(
                'Which ST77xx profile?',
                array_keys($profiles),
            );
        }

        try {
            $this->panel = Circuit::profile($this->profileName);
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            $this->panel = null;

            return;
        }

        if (! method_exists($this->panel, 'fillRgb565')) {
            $this->error('Resolved IC does not expose fillRgb565(); expected an ST77xx panel.');
            $this->panel = null;
        }
    }

    public function loop(): SketchLoopResult
    {
        if ($this->stopRequested) {
            $this->info('ST77xx smoke stopped.');

            return SketchLoopResult::STOP;
        }

        if (is_null($this->panel) || is_null($this->profileName)) {
            return SketchLoopResult::STOP;
        }

        if (! $this->announced) {
            $ic = (string) (config("circuits.{$this->profileName}.ic") ?? 'st77xx');
            $this->info("ST77xx smoke via Circuit::profile('{$this->profileName}') [{$ic}]");
            if (method_exists($this->panel, 'width') && method_exists($this->panel, 'height')) {
                $this->line(sprintf('  geometry: %dx%d', $this->panel->width(), $this->panel->height()));
            }
            if (method_exists($this->panel, 'hasBooted')) {
                $this->line('  booted: '.($this->panel->hasBooted() ? 'yes' : 'no'));
            }
            $this->line('  USB SPI reuses the same bus for DC/RST when canServeDigitalPins() is true.');
            $this->line('  Cycling solid fills — Ctrl-C to end.');
            $this->announced = true;
            $this->lastPaintNs = 0;
        }

        $now = hrtime(true);
        // Repaint roughly every 750ms
        if ($this->lastPaintNs !== 0 && ($now - $this->lastPaintNs) < 750_000_000) {
            usleep(20_000);

            return SketchLoopResult::CONTINUE;
        }

        $colors = ST77xxSmokeColor::cycle();
        $color = $colors[$this->phase % count($colors)];
        $this->phase++;

        try {
            $this->panel->fillRgb565($color->value);
            $this->line('  fill '.$color->name.' (0x'.strtoupper(dechex($color->value)).')');
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return SketchLoopResult::STOP;
        }

        $this->lastPaintNs = $now;

        return SketchLoopResult::CONTINUE;
    }

    public function shutdown(): void
    {
        if ($this->panel instanceof DisplayPanel || $this->panel instanceof IntegratedCircuit) {
            try {
                $this->panel->close();
            } catch (Throwable) {
                //
            }
        }

        $this->panel = null;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function st77xxProfiles(): array
    {
        $all = config('circuits', []);

        if (! is_array($all)) {
            return [];
        }

        $matched = [];

        foreach ($all as $name => $recipe) {
            if (! is_string($name) || ! is_array($recipe)) {
                continue;
            }

            $ic = $recipe['ic'] ?? null;

            if (is_string($ic) && ! is_null(ST77xxCatalogIc::tryFrom($ic))) {
                $matched[$name] = $recipe;
            }
        }

        return $matched;
    }
}
