<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuota extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'text_quota',
        'multimedia_quota',
        'free_text_quota',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'text_quota' => 'integer',
            'multimedia_quota' => 'integer',
            'free_text_quota' => 'integer',
        ];
    }

    /**
     * Get the user that owns the quota.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create quota for user
     */
    public static function getOrCreateForUser(string $userId): self
    {
        $quota = static::firstOrCreate(
            ['user_id' => $userId],
            [
                'balance' => 0,
                'text_quota' => 0,
                'multimedia_quota' => 0,
                'free_text_quota' => 100, // Berikan 100 quota gratis saat pertama kali dibuat
            ]
        );
        
        // Jika user sudah ada tapi free_text_quota masih 0 atau null, berikan 100
        // (untuk user yang sudah ada sebelum migration ini)
        if ($quota->free_text_quota == 0 && $quota->wasRecentlyCreated === false) {
            // Cek apakah ini tanggal 1 atau setelahnya di bulan ini
            $today = now();
            if ($today->day >= 1) {
                $quota->update(['free_text_quota' => 100]);
            }
        }
        
        return $quota;
    }

    /**
     * Add balance to quota
     */
    public function addBalance(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    /**
     * Deduct balance from quota
     */
    public function deductBalance(float $amount): bool
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);
            return true;
        }
        return false;
    }

    /**
     * Check if user has enough balance
     */
    public function hasEnoughBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Add text quota
     */
    public function addTextQuota(int $amount): void
    {
        $this->increment('text_quota', $amount);
    }

    /**
     * Deduct text quota
     */
    public function deductTextQuota(int $amount = 1): bool
    {
        if ($this->text_quota >= $amount) {
            $this->decrement('text_quota', $amount);
            return true;
        }
        return false;
    }

    /**
     * Add multimedia quota
     */
    public function addMultimediaQuota(int $amount): void
    {
        $this->increment('multimedia_quota', $amount);
    }

    /**
     * Deduct multimedia quota
     */
    public function deductMultimediaQuota(int $amount = 1): bool
    {
        if ($this->multimedia_quota >= $amount) {
            $this->decrement('multimedia_quota', $amount);
            return true;
        }
        return false;
    }

    /**
     * Add free text quota
     */
    public function addFreeTextQuota(int $amount): void
    {
        $this->increment('free_text_quota', $amount);
    }

    /**
     * Deduct free text quota
     */
    public function deductFreeTextQuota(int $amount = 1): bool
    {
        if ($this->free_text_quota >= $amount) {
            $this->decrement('free_text_quota', $amount);
            return true;
        }
        return false;
    }

    /**
     * Check if user has free text quota
     */
    public function hasFreeTextQuota(int $amount = 1): bool
    {
        return $this->free_text_quota >= $amount;
    }
}
