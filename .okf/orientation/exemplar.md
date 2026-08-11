---
type: Orientation
title: Exemplar IC package
description: ST77xx is the exemplar dept-of-scrapyard-robotics IC package for Circuits 0.7 promotion.
tags: [orientation, exemplar, circuits, 0.7, dosr]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T00:35:00Z" }
status: draft
sources:
  - id: provider
    resource: src/ST77xxServiceProvider.php
    title: ST77xxServiceProvider
  - id: readme
    resource: README.md
    title: Package README
---

# Role

This package is the **exemplar** for promoting DOSR chip drivers onto gpio-framework Circuits 0.7:

1. Extend a taxonomy base (`DisplayPanel`).
2. Annotate `#[IntegratedCircuit]` + `#[Pinout]`.
3. Register catalog types with `Circuit::addCircuit`.
4. Optionally register a profile maker via `Circuit::registerProfileCommand`.
5. Ship a profile-only hardware smoke sketch.

Registry / fluent / profile **mechanics** are owned by `scrapyard-io/gpio-framework` — read that package’s `.okf` (Circuits + IC ownership convention) when changing how ICs plug in.

# Pattern checklist

| Step | ST77xx evidence |
|------|-----------------|
| Taxonomy | `extends DisplayPanel`, `implements BootSequence` |
| Attributes | `#[IntegratedCircuit(['SPI','DigitalIO'])]` + matching `#[Pinout]` |
| Catalog | `st7735` / `st7789` / `st7796` |
| Profile tooling | `st77xx:make-profile` delegated from `circuit:make-profile` |
| Smoke | `st77xx-smoke` via `Circuit::profile()` |

# Related

* [Package (0.7)](package.md)
* [Provider and catalog](../core/provider-catalog.md)
* [Make profile](../core/make-profile.md)
