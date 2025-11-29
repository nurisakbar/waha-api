<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageStatistic extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'messages_sent',
        'messages_received',
        'api_calls',
        'webhook_calls',
        'storage_used',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
