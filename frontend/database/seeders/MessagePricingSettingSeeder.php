<?php

namespace Database\Seeders;

use App\Models\MessagePricingSetting;
use Illuminate\Database\Seeder;

class MessagePricingSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MessagePricingSetting::firstOrCreate(
            ['is_active' => true],
            [
                'text_with_watermark_price' => 0,
                'text_without_watermark_price' => 100,
                'multimedia_price' => 200,
                'watermark_text' => 'Sent via WAHA SaaS',
                'is_active' => true,
            ]
        );
    }
}
