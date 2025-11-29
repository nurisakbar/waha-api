<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessagePricingSetting extends Model
{
    protected $fillable = [
        'text_with_watermark_price',
        'text_without_watermark_price',
        'multimedia_price',
        'watermark_text',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'text_with_watermark_price' => 'decimal:2',
            'text_without_watermark_price' => 'decimal:2',
            'multimedia_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get active pricing settings
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first() ?? static::firstOrCreate(
            ['is_active' => true],
            [
                'text_with_watermark_price' => 0,
                'text_without_watermark_price' => 0,
                'multimedia_price' => 0,
                'watermark_text' => 'Sent via WAHA SaaS',
            ]
        );
    }

    /**
     * Get price for message type
     */
    public function getPriceForMessageType(string $messageType, bool $withWatermark = true): float
    {
        if ($messageType === 'text') {
            return $withWatermark 
                ? (float) $this->text_with_watermark_price 
                : (float) $this->text_without_watermark_price;
        }

        // Multimedia (image, document, video, etc)
        return (float) $this->multimedia_price;
    }
}
