<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotaUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'message_id',
        'quota_type',
        'amount',
        'message_type',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the user that used the quota.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the message that used the quota.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
