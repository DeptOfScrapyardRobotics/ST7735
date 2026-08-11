# Directory Update Log

## 2026-08-11

* **Fix (draft)**: Composer `require` uses leaf components (`gpio/*`, `waveforms/contracts` or `tubes/contracts`, `fabricate/nuts-and-bolts`) — no `scrapyard-io/gpio-framework` / `scrapyard-io/waveforms` / `scrapyard-io/tubes` kitchen sinks. Amended [package](orientation/package.md).

## 2026-08-10

* **Fix**: `ST77xxIO::spiData` / `data()` accept `array|string` — `PanelIC::transmit` passes `DumpedBuffer::raw_data` binary strings; chunk via `substr` (no unpack-to-int-array).
* **Update (draft)**: [Display panels](core/display-panels.md) — ST7735/ST7789/ST7796 implement tubes `Contracts\Panels\FullColorDisplay` for PanelIC wrap.
* **Creation**: Initial `.okf` v0.2 for `dept-of-scrapyard-robotics/st77xx` 0.7 — package orientation, exemplar role, DisplayPanel/Pinout/`spi()` core, provider catalog + make-profile + fills/smoke, USB SPI digital-share and smoke-not-tubes traps. Style matched to `scrapyard-io/gpio-framework/.okf`; registry semantics deferred to gpio-framework.
