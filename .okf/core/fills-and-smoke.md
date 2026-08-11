---
type: Module
title: Fills and smoke
description: ST77xxFillsRgb565 solid fills and the st77xx-smoke Circuit::profile() hardware sketch.
resource: src/Sketches/ST77xxSmoke.php
tags: [core, smoke, sketch, rgb565, fills]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T00:35:00Z" }
status: draft
sources:
  - id: fills
    resource: src/Concerns/ST77xxFillsRgb565.php
    title: ST77xxFillsRgb565
  - id: smoke
    resource: src/Sketches/ST77xxSmoke.php
    title: ST77xxSmoke
  - id: colors
    resource: src/Enums/ST77xxSmokeColor.php
    title: ST77xxSmokeColor
  - id: readme
    resource: README.md
    title: Package README
---

# Concern: `ST77xxFillsRgb565`

Solid-color fill **without** tubes/framebuffers — for hardware smoke:

1. `setAddressWindow(0, 0, width, height)`.
2. RAMWR opcode `0x2C` (shared across ST7735 / ST7789 / ST7796).
3. Stream RGB565 big-endian bytes in chunks of `max_packet_size / 2` pixels via `$this->transport->data(...)`.

Requires abstract `width()`, `height()`, `setAddressWindow(...)`, plus `transport` / `max_packet_size`.

# Sketch: `st77xx-smoke`

```bash
php workshop runner st77xx-smoke
php workshop runner st77xx-smoke --profile=front_panel
```

| Behavior | Detail |
|----------|--------|
| Provision | **Only** `Circuit::profile($name)` |
| Profile filter | `config/circuits.php` entries whose `ic` is in `ST77xxCatalogIc` |
| Loop | Cycle `ST77xxSmokeColor` fills ~every 750ms until Ctrl-C / SIGTERM |
| Colors | RED `0xF800`, GREEN `0x07E0`, BLUE `0x001F`, WHITE `0xFFFF`, BLACK `0x0000` |
| Shutdown | `close()` on the panel |

Requires at least one ST77xx profile; otherwise prints guidance to run `st77xx:make-profile`.

# Related

* [Smoke is not tubes](../traps/smoke-not-tubes.md)
* [Make profile](make-profile.md)
* [Display panels](display-panels.md)
