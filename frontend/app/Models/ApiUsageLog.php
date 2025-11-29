<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'api_key_id',
        'endpoint',
        'method',
        'ip_address',
        'status_code',
        'response_time',
        'request_size',
        'response_size',
    ];

    protected function casts(): array
    {
        return [
            'response_time' => 'integer',
            'request_size' => 'integer',
            'response_size' => 'integer',
            'status_code' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}
