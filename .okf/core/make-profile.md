---
type: Module
title: Make profile
description: st77xx:make-profile scaffolds circuits.php profiles for ST7735/ST7789/ST7796.
resource: src/Console/ST77xxMakeProfileCommand.php
tags: [core, console, profile, circuits]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T00:35:00Z" }
status: draft
sources:
  - id: command
    resource: src/Console/ST77xxMakeProfileCommand.php
    title: ST77xxMakeProfileCommand
  - id: readme
    resource: README.md
    title: Package README
---

# Commands

```bash
workshop vendor:publish --tag=gpio-circuits-config
workshop circuit:make-profile          # any installed IC; ST77xx may delegate here
workshop st77xx:make-profile           # ST7735 / ST7789 / ST7796 only
```

Signature highlights: optional `{ic?}`, `{name?}`, `--protocol=`.

# Behavior

1. Filter `ST77xxCatalogIc::slugs()` against `CircuitRegistry::listCircuits()`.
2. Resolve IC class; read protocol options via `CircuitAttributeInspector`.
3. Prompt / resolve protocol option, then profile name (default = IC slug).
4. `writePromptedProfile` (from gpio `ScaffoldsCircuitProfiles`) — prompts SPI/DigitalIO adapter, device, chip select, `dc`/`rst` from `#[Pinout]`; always sets `boot_now => true`.

# App usage

```php
Circuit::profile('front_panel');
```

Profile **keys** are app-owned and arbitrary; the `ic` field must be a catalog slug (`st7735` / `st7789` / `st7796`). Do not treat the profile name as the catalog slug.

# Related

* [Provider and catalog](provider-catalog.md)
* [Display panels](display-panels.md)
* [Fills and smoke](fills-and-smoke.md)
