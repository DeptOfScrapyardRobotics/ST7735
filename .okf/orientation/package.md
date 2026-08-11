---
type: Module
title: Package (0.7)
description: dept-of-scrapyard-robotics/st77xx Composer identity for ST77xx SPI display drivers.
resource: composer.json
tags: [orientation, package, 0.7, dosr]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T00:35:00Z" }
verified: { by: null, at: null }
status: draft
sources:
  - id: composer
    resource: composer.json
    title: Package composer.json
  - id: readme
    resource: README.md
    title: Package README
  - id: provider
    resource: src/ST77xxServiceProvider.php
    title: ST77xxServiceProvider
---

# Identity

| Field | Value |
|-------|-------|
| Composer | `dept-of-scrapyard-robotics/st77xx` **0.7.0** |
| PHP | `^8.4\|^8.5\|^8.6` |
| Namespace | `DeptOfScrapyardRobotics\Displays\ST77xx\` → `src/` |
| Provider | `DeptOfScrapyardRobotics\Displays\ST77xx\ST77xxServiceProvider` |
| Discovery | `extra.scrapyard-io.providers` |

# Requires

| Package | Constraint |
|---------|------------|
| `fabricate/nuts-and-bolts` | `^0.7.0` |
| `gpio/circuits` | `^0.7.0` |
| `gpio/contracts` | `^0.7.0` |
| `gpio/digital` | `^0.7.0` |
| `gpio/spi` | `^0.7.0` |
| `tubes/contracts` | `^0.7.0` |

Suggests (optional native / microscrap): `ext-posi`, `ext-ftdi`, `microscrap/posix`, `microscrap/mpsse` at `^0.7.0`.

# Public surface (summary)

- Catalog ICs: `st7735`, `st7789`, `st7796` (string-backed `ST77xxCatalogIc`).
- Console: `st77xx:make-profile` (`ST77xxConsoleCommand::MAKE_PROFILE`).
- Sketch: `st77xx-smoke`.
- Shared transport: `ST77xxCarrierTransport` + `ST77xxFillsRgb565`.

# Related

* [Exemplar IC package](exemplar.md)
* [Provider and catalog](../core/provider-catalog.md)
* [Display panels](../core/display-panels.md)
