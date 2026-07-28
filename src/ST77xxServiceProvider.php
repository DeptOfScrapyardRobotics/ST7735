<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx;

use Fabricate\NutsAndBolts\ServiceProvider;
use Fabricate\NutsAndBolts\MagicAliases\Circuit;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\ST7735;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\ST7789;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\ST7796;

class ST77xxServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Circuit::addCircuit('st7735', ST7735::class);
        Circuit::addCircuit('st7789', ST7789::class);
        Circuit::addCircuit('st7796', ST7796::class);
    }
}