<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Message extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'user_id',
        'session_id',
        'whatsapp_message_id',
        'from_number',
        'to_number',
        'chat_type',
        'message_type',
        'content',
        'media_url',
        'media_mime_type',
        'media_size',
        'caption',
        'status',
        'direction',
        'error_message',
        'sent_at',
        'delivered_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the session for this message.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(WhatsAppSession::class, 'session_id');
    }

    /**
     * Check if message is incoming.
     */
    public function isIncoming(): bool
    {
        return $this->direction === 'incoming';
    }

    /**
     * Check if message is outgoing.
     */
    public function isOutgoing(): bool
    {
        return $this->direction === 'outgoing';
    }

    /**
     * Check if message is sent to a group.
     */
    public function isGroup(): bool
    {
        return $this->chat_type === 'group';
    }

    /**
     * Check if message is sent to personal chat.
     */
    public function isPersonal(): bool
    {
        return $this->chat_type === 'personal';
    }
}
