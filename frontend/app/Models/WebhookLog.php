<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    protected $fillable = [
        'webhook_id',
        'event_type',
        'payload',
        'response_status',
        'response_body',
        'error_message',
        'attempt_number',
        'triggered_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'triggered_at' => 'datetime',
        ];
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}
