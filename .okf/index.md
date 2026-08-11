---
okf_version: "0.2"
---

# dept-of-scrapyard-robotics/st77xx Knowledge Bundle

Package knowledge for `dept-of-scrapyard-robotics/st77xx` (ST7735 / ST7789 / ST7796, v0.7.x).
Read this index first; open only the concepts needed for the task.

**Trust rule:** Prefer `status: stable`. Treat `deprecated` as historical only. New agent-written concepts stay `status: draft` until a human verifies them.
**Placement:** Package-root `.okf/` only — never under `src/` IC subtrees.
**Links:** Concept cross-links use paths relative to each file.
**Scope:** This package owns chip drivers + catalog registration for ST77xx panels. CircuitRegistry / fluent / profile **semantics** live in `scrapyard-io/gpio-framework` `.okf` — point there; do not restate the registry model here.
**Dist note:** `.okf/` and root `AGENTS.md` are `export-ignore` in `.gitattributes`.

# Orientation

* [Package (0.7)](orientation/package.md) - Composer identity, requires, discovery.
* [Exemplar IC package](orientation/exemplar.md) - DOSR Circuits 0.7 promotion pattern.

# Core

* [Display panels](core/display-panels.md) - ST7735/ST7789/ST7796 extend DisplayPanel; Pinout SPI+DigitalIO; `spi()` factory.
* [Provider and catalog](core/provider-catalog.md) - ST77xxServiceProvider registers slugs + profile makers + smoke sketch.
* [Make profile](core/make-profile.md) - `st77xx:make-profile` and `circuit:make-profile` delegation.
* [Fills and smoke](core/fills-and-smoke.md) - `ST77xxFillsRgb565` + `st77xx-smoke` sketch.

# Traps

* [USB SPI digital share](traps/usb-spi-digital-share.md) - Separate DigitalIO only when `canServeDigitalPins()` is false.
* [Smoke is not tubes](traps/smoke-not-tubes.md) - Smoke provisions via `Circuit::profile()` only; no window/UX path.

# Log

* [Directory update log](log.md)
