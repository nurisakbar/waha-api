<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    protected $fillable = [
        'text_quota_bonus',
        'multimedia_quota_bonus',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'text_quota_bonus' => 'integer',
            'multimedia_quota_bonus' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get active referral settings
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first() ?? static::firstOrCreate(
            ['is_active' => true],
            [
                'text_quota_bonus' => 10,
                'multimedia_quota_bonus' => 5,
                'is_active' => true,
            ]
        );
    }
}
