---
type: Module
title: Display panels
description: ST7735, ST7789, and ST7796 DisplayPanel drivers with SPI+DigitalIO pinout and spi() factory.
resource: src/ST7789/ST7789.php
tags: [core, display, pinout, spi, st7735, st7789, st7796]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T00:35:00Z" }
status: draft
sources:
  - id: st7789
    resource: src/ST7789/ST7789.php
    title: ST7789 panel
  - id: st7735
    resource: src/ST7735/ST7735.php
    title: ST7735 panel
  - id: st7796
    resource: src/ST7796/ST7796.php
    title: ST7796 panel
  - id: transport
    resource: src/ST77xxCarrierTransport.php
    title: ST77xxCarrierTransport
---

# Classes

| Catalog slug | Class | Default geometry (factory) |
|--------------|-------|----------------------------|
| `st7735` | `ST7735\ST7735` | chip-specific defaults in `spi()` |
| `st7789` | `ST7789\ST7789` | 240×240 typical |
| `st7796` | `ST7796\ST7796` | chip-specific defaults in `spi()` |

All three:

- Extend `GeneralPurposeIO\Circuits\DisplayPanel`.
- Implement `BootSequence` and tubes `Contracts\Panels\FullColorDisplay` (PanelDevice for `Panel::wrap`).
- Use chip `*API` concerns + shared `ST77xxFillsRgb565`.
- Carry tubes `FormatSpec` / `transmit(DumpedBuffer)` for framebuffer paths (separate from smoke fills).

# Attributes

```php
#[IntegratedCircuit(['SPI', 'DigitalIO'])]
#[Pinout([
    'SPI' => ['driver', 'device', 'chip_select'],
    'DigitalIO' => ['driver', 'device', 'dc', 'rst'],
])]
```

Pinout roles drive `circuit:make-profile` / `st77xx:make-profile` prompts (adapter → `*_adapter`, device → `*_device`, pins → `dc_pin` / `rst_pin`).

# Factory: `spi()`

Shared shape across panels (named args for Circuits reflection):

| Param | Role |
|-------|------|
| `spi_device`, `spi_adapter` | SPI bus selection |
| `chip_select` | SPI device select |
| `digital_device`, `digital_adapter` | DigitalIO when SPI bus cannot serve pins |
| `dc_pin`, `rst_pin` | Command/data and reset |
| `width`, `height`, `x_offset`, `y_offset`, `max_packet_size` | Geometry / chunking |
| chip breakouts / color mode | Optional register helpers |
| `boot_now` | Default `true` in factories; profile scaffolding always writes `true` |

Flow:

1. `SPI::adapter(...)->device(...)->mode(0)->speed(...)->bus()` then `select($chip_select)`.
2. If `!$bus->canServeDigitalPins()`, open `DigitalIO::adapter(...)->device(...)->bus()`.
3. `$bus->output($dc_pin)` / `output($rst_pin)`.
4. `fromSPIBus(...)` wraps `ST77xxCarrierTransport`.

# Transport

`ST77xxCarrierTransport` holds `SPIDevice` + DC/RST `DigitalOutputPin`s; `command` / `data` toggle DC and chunk SPI writes. Missing SPI or digital pins raise `ST77xxException`.

# Related

* [USB SPI digital share](../traps/usb-spi-digital-share.md)
* [Fills and smoke](fills-and-smoke.md)
* [Provider and catalog](provider-catalog.md)
