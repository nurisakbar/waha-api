<?php

namespace App\Services;

class WatermarkService
{
    /**
     * Add watermark to text message
     */
    public function addWatermark(string $message, string $watermarkText): string
    {
        // Add watermark at the end of message with italic style
        // WhatsApp uses _text_ for italic formatting
        $italicWatermark = '_' . $watermarkText . '_';
        return $message . "\n\n" . $italicWatermark;
    }

    /**
     * Check if message should have watermark (based on price)
     */
    public function shouldAddWatermark(float $price): bool
    {
        // If price is 0, it's free and should have watermark
        return $price == 0;
    }
}

