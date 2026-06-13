Introduction
============

PHP Package for the ST77xx family of full-color TFT displays. Three controllers
are supported, each with its own driver class:

* `ST7735` — 128x160 color TFT
* `ST7789` — 240x320 color TFT
* `ST7796` — 480x320 color TFT

All three drive 16-bit (RGB565) color by default and share an identical
connection and usage surface; only the class name and default resolution differ.

Compatible SPI Interfaces
===============
The ST77xx displays communicate with your device over SPI, the Serial Peripheral Interface.

You can interface with displays such as the ST77xx with this package the following ways:
* A Linux Single-Board Computer's exposed GPIO pins using the dedicated SPI MOSI/SCK and CS pins as well as 2 GPIO pins for DC and RST.
* An MPSSE-enabled USB-to-Serial device such as an FT232H generally using D0 and SCK, D1 for MOSI, D2 for MISO and D3 for CS, D4 and D5 for RST/DC and connected to nearly any Linux or MacOS USB port.

Dependencies
=============
This package makes use of modules within:
* [The ScrapyardIO Framework](https://github.com/ScrapyardIO/framework)

This package also requires one of the following extensions in order to interface with SPI
* [POSI Extension v^0.4.0 or newer](https://github.com/php-io-extensions/posi)
* [FTDI Extension v^0.4.0 or newer](https://github.com/php-io-extensions/ftdi)

In addition, an extension wrapper package is needed

For ext-posi
* [Microscrap POSIX Package v0.4.0 or newer](https://github.com/microscrap/posix)
* [Microscrap Native SPI Package v0.4.0 or newer](https://github.com/microscrap/spi)
* [Microscrap Native GPIO Package v0.4.0 or newer](https://github.com/microscrap/gpio)

For ext-ftdi
* [Microscrap FTDI Package v0.4.0 or newer](https://github.com/microscrap/ftdi)
* [Microscrap MPSSE Package v0.4.0 or newer](https://github.com/microscrap/mpsse)

Installing from Composer
====================
Inside the root of your PHP Project, simply require the ST77xx package from composer
```shell
composer require dept-of-scrapyard-robotics/st77xx
```
Framework Configuration
====================
If you would like to use the ScrapyardIO Framework to bootstrap your display without
wasting lines configuring your display right in the script you can add your desired
configuration to scrapyard-io.php, such as in this example:

### SPI
```php

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\ST7789;

return [
    'displays' => [
        // For Native Configurations 
        'st7789-native' => [
            'class_name' => ST7789::class,
            'connection' => ['driver' => 'native'],
            'startup' => [
                'spi' => [0, 0],
                'gpiochip' => [0],
                'rst' => [24],
                'dc' => [22],
            ],
        ],
        // For USB Configurations
        'st7789-usb' => [
            'class_name' => ST7789::class,
            'connection' => ['driver' => 'usb'],
            'startup' => [
                'spi' => ['ft232h', 0],
                'gpiochip' => ['ft232h'],
                'rst' => [0],
                'dc' => [1],
            ],
        ],        
    ]
];
```

Basic Usage
============

The examples below use the `ST7789`. Swap the class for `ST7735` or `ST7796`
(under the same `DeptOfScrapyardRobotics\Displays\ST77xx\...` namespace) to drive
those controllers — the builder chain is identical.

### Native (POSIX) SPI driver. (Single Board Computers)
```php

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\ST7789;

$native_spi_display = ST7789::connection('native')
    ->spi(0, 0)
    ->gpiochip(0)
    ->rst(24)
    ->dc(22)
    ->create()
```

### USB (MPSSE) driver using SPI. (Linux and MacOS)
```php

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\ST7789;

$usb_spi_display = ST7789::connection('usb')
    ->spi('ft232h', 0)
    ->gpiochip('ft232h')
    ->rst(0)
    ->dc(1)
    ->create()
```

### Other controllers
```php

use DeptOfScrapyardRobotics\Displays\ST77xx\ST7735\ST7735;
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7796\ST7796;

$st7735 = ST7735::connection('usb')->spi('ft232h', 0)->gpiochip('ft232h')->rst(0)->dc(1)->create();
$st7796 = ST7796::connection('usb')->spi('ft232h', 0)->gpiochip('ft232h')->rst(0)->dc(1)->create();
```

## Alternative Usage

### Using Through the Display Library (as a ColorTFTDisplay)
```php
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\ST7789;
use RealityInterface\Displays\Applied\FullColorTFT\ColorTFTDisplay;

$st7789 = ST7789::connection('usb')
    ->spi('ft232h', 0)
    ->gpiochip('ft232h')
    ->rst(0)
    ->dc(1)
    ->create()
    
$display = ColorTFTDisplay::as($st7789);

```

### Using Through the Display Framework (with an autoloaded config) (as a ColorTFTDisplay)
```php

use RealityInterface\Displays\Applied\FullColorTFT\ColorTFTDisplay;

$display = ColorTFTDisplay::using('st7789-usb');

```

Display API
==========
The setters in this API interface with the device directly (register writes), so
you can use property access while still working against the panel itself. The
three controllers share most properties but each exposes its own register set.

### ST7735

Readable Properties (Getters)
-----------------------------
* `$display->display_on` — Returns whether the panel is on.
* `$display->sleep_mode_enabled` — Returns whether sleep mode is active.
* `$display->color_mode` (`ST7735ColorMode`) — Returns the current pixel format.

Writable Properties (Setters)
-----------------------------
* `$display->display_on = true;` — Turns the panel on or off.
* `$display->sleep_mode_enabled = false;` — Enters or exits sleep mode.
* `$display->frctl_normal = ...;` — Frame-rate control for normal mode.
* `$display->frctl_idle = ...;` — Frame-rate control for idle mode.
* `$display->frctl_partial = ...;` — Frame-rate control for partial mode.
* `$display->power_control1 = ...;` through `power_control5` — Power control registers.
* `$display->v_com_control = ...;` — VCOM control.
* `$display->display_inversion_enabled = false;` — Enables/disables color inversion.
* `$display->mad_control = ...;` — Memory access control (orientation / RGB-BGR).
* `$display->color_mode = ST7735ColorMode::COLOR16;` — Sets the pixel format.
* `$display->color_gamma_positive = ...;` — Positive gamma curve.
* `$display->color_gamma_negative = ...;` — Negative gamma curve.
* `$display->normal_mode_on = true;` — Returns to normal display mode.

### ST7789

Readable Properties (Getters)
-----------------------------
There are no readable magic properties exposed for the ST7789 in this package.

Writable Properties (Setters)
-----------------------------
* `$display->display_on = true;` — Turns the panel on or off.
* `$display->sleep_mode_enabled = false;` — Enters or exits sleep mode.
* `$display->porch_control = ...;` — Porch setting.
* `$display->gate_control = ...;` — Gate driver control.
* `$display->v_com_control = ...;` — VCOM setting.
* `$display->lcm_control = ...;` — LCM control.
* `$display->vdv_vrh_enabled = ...;` — Enables VDV/VRH command-write control.
* `$display->vrh = ...;` — VRH (VAP/VAN) set.
* `$display->vdv = ...;` — VDV set.
* `$display->frctl_normal = ...;` — Frame-rate control for normal mode.
* `$display->power_control1 = ...;` — Power control register 1.
* `$display->display_inversion_enabled = false;` — Enables/disables color inversion.
* `$display->mad_control = ...;` — Memory access control (orientation / RGB-BGR).
* `$display->pixel_format = ST7789ColorMode::COLOR16;` — Sets the pixel format.
* `$display->color_gamma_positive = ...;` — Positive gamma curve.
* `$display->color_gamma_negative = ...;` — Negative gamma curve.
* `$display->normal_mode_on = true;` — Returns to normal display mode.

### ST7796

Readable Properties (Getters)
-----------------------------
There are no readable magic properties exposed for the ST7796 in this package.

Writable Properties (Setters)
-----------------------------
* `$display->display_on = true;` — Turns the panel on or off.
* `$display->sleep_mode_enabled = false;` — Enters or exits sleep mode.
* `$display->mad_control = ...;` — Memory access control (orientation / RGB-BGR).
* `$display->pixel_format = ST7796ColorMode::COLOR16;` — Sets the pixel format.
* `$display->inversion_control = ...;` — Display inversion control.
* `$display->display_function_control = ...;` — Display function control.
* `$display->display_output_ctrl_adjust = ...;` — Display output control adjustment.
* `$display->power_control2 = ...;` — Power control register 2.
* `$display->power_control3 = ...;` — Power control register 3.
* `$display->v_com_control = ...;` — VCOM control.
* `$display->display_inversion_enabled = false;` — Enables/disables color inversion.
* `$display->color_gamma_positive = ...;` — Positive gamma curve.
* `$display->color_gamma_negative = ...;` — Negative gamma curve.
* `$display->normal_mode_on = true;` — Returns to normal display mode.

Drawing on the Display
============
Draw with a `Screen`, which wraps a `GFXRenderer` over a frame buffer matched to
the panel's `FormatSpec`, then ships the bytes on `render()`. A colour TFT uses a
`DirtyRegionsBuffer` (coalesces changed rectangles, one update per region).
Colors are 16-bit RGB565 integers (e.g. `0xF800` red, `0x07E0` green, `0x001F` blue).

```php
use DeptOfScrapyardRobotics\Displays\ST77xx\ST7789\ST7789;
use Microscrap\GFX\PhpdaFruit\Buffers\DirtyRegionsBuffer;
use Microscrap\GFX\PhpdaFruit\GFXRenderer;
use RealityInterface\Displays\Applied\FullColorTFT\ColorTFTDisplay;
use RealityInterface\Displays\Screen;

$st7789 = ST7789::connection('usb')
    ->spi('ft232h', 0)
    ->gpiochip('ft232h')
    ->rst(0)
    ->dc(1)
    ->create();

$display = ColorTFTDisplay::as($st7789);

$buffer = new DirtyRegionsBuffer($display->width(), $display->height(), $display->getFormatSpec());
$screen = new Screen($display, new GFXRenderer($buffer));

$screen
    ->fill(0x0000)
    ->drawRoundRect(0, 0, $display->width(), $display->height(), 12, 0xFFFF)
    ->fillCircle(intdiv($display->width(), 2), 80, 24, 0xF800)
    ->setTextColor(0x07E0)
    ->setTextSize(3)
    ->setCursor(20, 40)
    ->print('Hello')
    ->render();
```
