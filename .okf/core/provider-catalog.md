---
type: Module
title: Provider and catalog
description: ST77xxServiceProvider registers catalog ICs, profile commands, and the smoke sketch.
resource: src/ST77xxServiceProvider.php
tags: [core, provider, catalog, circuits]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T00:35:00Z" }
status: draft
sources:
  - id: provider
    resource: src/ST77xxServiceProvider.php
    title: ST77xxServiceProvider
  - id: catalog-enum
    resource: src/Enums/ST77xxCatalogIc.php
    title: ST77xxCatalogIc
  - id: console-enum
    resource: src/Enums/ST77xxConsoleCommand.php
    title: ST77xxConsoleCommand
---

# Boot responsibilities

`ST77xxServiceProvider`:

1. **register** — singleton + console command `ST77xxMakeProfileCommand`.
2. **boot** — catalog + profile makers + optional sketch.

# Catalog registration

```php
Circuit::addCircuit(ST77xxCatalogIc::ST7735->value, ST7735::class);
Circuit::addCircuit(ST77xxCatalogIc::ST7789->value, ST7789::class);
Circuit::addCircuit(ST77xxCatalogIc::ST7796->value, ST7796::class);
```

| Enum case | Slug |
|-----------|------|
| `ST7735` | `st7735` |
| `ST7789` | `st7789` |
| `ST7796` | `st7796` |

# Profile command wiring

For each catalog slug:

```php
Circuit::registerProfileCommand($ic->value, ST77xxConsoleCommand::MAKE_PROFILE->value);
```

So `circuit:make-profile` can **delegate** to `st77xx:make-profile` when the chosen IC is ST77xx.

# Sketch

If `SketchRegistry` is bound and `st77xx-smoke` is absent, register `ST77xxSmoke` via `registerConvention('st77xx-smoke', …)`.

# Scope note

`Circuit::addCircuit` / `registerProfileCommand` / `Circuit::profile` semantics → **gpio-framework** `.okf` (`core/circuits.md`). This concept only records what *this* package registers.

# Related

* [Make profile](make-profile.md)
* [Fills and smoke](fills-and-smoke.md)
* [Exemplar IC package](../orientation/exemplar.md)
* [Package (0.7)](../orientation/package.md)
