<?php

namespace Database\Seeders;

use App\Models\ReferralSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReferralSetting::firstOrCreate(
            ['is_active' => true],
            [
                'text_quota_bonus' => 10,
                'multimedia_quota_bonus' => 5,
                'is_active' => true,
            ]
        );
    }
}
