# dept-of-scrapyard-robotics/st77xx (0.7)

SPI drivers for ST7735 / ST7789 / ST7796. Extends `GeneralPurposeIO\Circuits\DisplayPanel`.

## Register

Provider registers catalog slugs `st7735`, `st7789`, `st7796` and wires `st77xx:make-profile` into `circuit:make-profile`.

## Profiles

```bash
workshop vendor:publish --tag=gpio-circuits-config
workshop circuit:make-profile          # picks any installed IC; ST77xx delegates here
workshop st77xx:make-profile           # ST7735 / ST7789 / ST7796 only
```

The command asks SPI/DigitalIO adapter, device, chip select, and `dc`/`rst` pins from `#[Pinout]`, and always sets `boot_now => true`.

```php
Circuit::profile('front_panel');
```

## Smoke sketch

Requires at least one ST77xx profile in `config/circuits.php`:

```bash
php workshop runner st77xx-smoke
php workshop runner st77xx-smoke --profile=front_panel
```

Provisions only via `Circuit::profile()` — no tubes/window driving. Cycles solid RGB565 fills until you Ctrl-C. On USB SPI, DC/RST come from the same MPSSE bus when `canServeDigitalPins()` is true.
