<?php

namespace ScrapyardIO\Displays\Adapters\ST7735\Enums;

enum ST7735ReadOnlyRegister: int
{
    case DISPLAY_ID = 0x04;
    case DISPLAY_STATUS = 0x09;
    case DISPLAY_POWER_MODE = 0x0A;
    case READ_MEM_ACCESS_CONTROL = 0x0B;
    case DISPLAY_PIXEL_FORMAT = 0x0C;
    case DISPLAY_IMAGE_MODE = 0x0D;
    case DISPLAY_SIGNAL_MODE = 0x0E;
    case DISPLAY_DIAG_RESULT = 0x0F;
    case MEMORY_READ = 0x2E;
    case ID1 = 0xDA;
    case ID2 = 0xDB;
    case ID3 = 0xDC;
}
