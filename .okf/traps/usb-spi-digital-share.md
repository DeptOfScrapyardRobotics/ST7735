---
type: Trap
title: USB SPI digital share
description: Reuse the SPI bus for DC/RST only when canServeDigitalPins() is true; otherwise open DigitalIO.
tags: [traps, spi, digitalio, usb, mpsse]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T00:35:00Z" }
status: draft
sources:
  - id: st7789
    resource: src/ST7789/ST7789.php
    title: ST7789::spi()
  - id: readme
    resource: README.md
    title: Package README
---

# Trap

Do **not** assume DC/RST always need a separate DigitalIO adapter.

In each panel’s `spi()` factory:

1. Build the SPI bus first.
2. If `$bus->canServeDigitalPins()` is **true** (typical USB MPSSE / UsbSPIBus path), call `$bus->output($dc_pin)` / `output($rst_pin)` on that **same** bus.
3. Only when it is **false**, open `DigitalIO::adapter($digital_adapter)->device($digital_device)->bus()` for those pins.

Profile scaffolding still collects DigitalIO `driver`/`device` roles from `#[Pinout]` — they matter when the SPI bus cannot serve digital pins (e.g. host SPI that is not also a GPIO controller).

# Related

* [Display panels](../core/display-panels.md)
* [Make profile](../core/make-profile.md)
