# Agent guidelines — dept-of-scrapyard-robotics/st77xx

## Knowledge Bundle (OKF)

This package ships an Open Knowledge Format bundle at [`.okf/`](.okf/) (excluded from Composer dist via `.gitattributes` `export-ignore`).

Before changing ST77xx drivers or advising on this IC package:

1. Read [`.okf/index.md`](.okf/index.md) first (progressive disclosure).
2. Open only the linked concepts needed for the task.
3. Prefer `status: stable` concepts; treat `deprecated` as historical only. New/changed concepts stay `status: draft` until a human verifies them.
4. When you learn something durable about **this package**, update the affected `.okf` concept(s) and append `.okf/log.md`.
5. Keep the `.okf` bundle at the **package root** only — do not nest extra `.okf` folders under `src/`.
6. CircuitRegistry / fluent / profile semantics belong in `scrapyard-io/gpio-framework` `.okf` — point there; do not duplicate them here.

## Package rules (quick) — 0.7.x

- Composer: `dept-of-scrapyard-robotics/st77xx` **0.7.0**. PHP `^8.4|^8.5|^8.6`. Namespace `DeptOfScrapyardRobotics\Displays\ST77xx\`.
- Requires leaf components (not kitchen-sink frameworks): `fabricate/nuts-and-bolts`, `gpio/circuits`, `gpio/contracts`, `gpio/digital`, `gpio/spi`, `tubes/contracts`.
- Provider: `ST77xxServiceProvider` — `Circuit::addCircuit` for `st7735` / `st7789` / `st7796`; `Circuit::registerProfileCommand` → `st77xx:make-profile`; sketch `st77xx-smoke`.
- Panels extend `DisplayPanel`; `#[IntegratedCircuit(['SPI','DigitalIO'])]` + `#[Pinout]` (SPI: driver/device/chip_select; DigitalIO: driver/device/dc/rst).
- USB SPI reuses the SPI bus for DC/RST when `canServeDigitalPins()` is true; otherwise open DigitalIO.
- Smoke: `Circuit::profile()` + `fillRgb565` color cycle — not tubes/window driving.
- Exemplar DOSR IC package for Circuits 0.7 promotion patterns.
