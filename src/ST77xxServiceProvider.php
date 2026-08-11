<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx;

use DeptOfScrapyardRobotics\Displays\ST77xx\Console\ST77xxMakeProfileCommand;
use DeptOfScrapyardRobotics\Displays\ST77xx\Enums\ST77xxCatalogIc;
use DeptOfScrapyardRobotics\Displays\ST77xx\Enums\ST77xxConsoleCommand;
use DeptOfScrapyardRobotics\Displays\ST77xx\Sketches\ST77xxSmoke;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\ST7735;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\ST7789;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\ST7796;
use Fabricate\Contracts\Sketches\SketchRegistry;
use Fabricate\NutsAndBolts\ServiceProvider;
use GeneralPurposeIO\Core\MagicAliases\Circuit;

class ST77xxServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ST77xxMakeProfileCommand::class);
        $this->commands([
            ST77xxMakeProfileCommand::class,
        ]);
    }

    public function boot(): void
    {
        Circuit::addCircuit(ST77xxCatalogIc::ST7735->value, ST7735::class);
        Circuit::addCircuit(ST77xxCatalogIc::ST7789->value, ST7789::class);
        Circuit::addCircuit(ST77xxCatalogIc::ST7796->value, ST7796::class);

        $maker = ST77xxConsoleCommand::MAKE_PROFILE->value;
        foreach (ST77xxCatalogIc::cases() as $ic) {
            Circuit::registerProfileCommand($ic->value, $maker);
        }

        $this->registerSketch();
    }

    protected function registerSketch(): void
    {
        if (! $this->container->bound(SketchRegistry::class)) {
            return;
        }

        /** @var SketchRegistry $registry */
        $registry = $this->container->make(SketchRegistry::class);

        if (! $registry->has('st77xx-smoke')) {
            $registry->registerConvention('st77xx-smoke', ST77xxSmoke::class);
        }
    }
}
