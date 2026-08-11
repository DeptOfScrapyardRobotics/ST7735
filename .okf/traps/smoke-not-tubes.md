---
type: Trap
title: Smoke is not tubes
description: st77xx-smoke validates Circuit::profile() hardware fills — it does not drive tubes windows or UX.
tags: [traps, smoke, tubes, sketches]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T00:35:00Z" }
status: draft
sources:
  - id: smoke
    resource: src/Sketches/ST77xxSmoke.php
    title: ST77xxSmoke
  - id: fills
    resource: src/Concerns/ST77xxFillsRgb565.php
    title: ST77xxFillsRgb565
  - id: readme
    resource: README.md
    title: Package README
---

# Trap

`st77xx-smoke` is a **panel provision / SPI paint** check:

- Provisions exclusively via `Circuit::profile()`.
- Paints with `fillRgb565()` (RAMWR path), not `transmit(DumpedBuffer)`.
- Does **not** open tubes windows, framebuffers, or UX layers.

Panel classes still implement tubes `FormatSpec` / `transmit` for real display pipelines — use those paths (or separate sketches) when validating framebuffer integration. Do not extend the smoke sketch into a tubes demo without an explicit product decision.

# Related

* [Fills and smoke](../core/fills-and-smoke.md)
* [Display panels](../core/display-panels.md)
