<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OTPService
{
    /**
     * Generate OTP code (6 digits)
     */
    public function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create and send OTP
     */
    public function createAndSend(
        User $user,
        string $phoneNumber,
        string $deviceId,
        ?string $templateId = null,
        int $expiryMinutes = 10
    ): array {
        // Generate OTP code
        $code = $this->generateCode();
        
        // Get template if provided
        $template = null;
        if ($templateId) {
            $template = Template::where('id', $templateId)
                ->where('user_id', $user->id)
                ->where('template_type', 'otp')
                ->where('is_active', true)
                ->first();
            
            if (!$template) {
                throw new \Exception('Template OTP tidak ditemukan atau tidak aktif');
            }
        }
        
        // Create OTP record
        $otp = Otp::create([
            'user_id' => $user->id,
            'template_id' => $template?->id,
            'phone_number' => $phoneNumber,
            'code' => $code,
            'status' => 'pending',
            'expires_at' => now()->addMinutes($expiryMinutes),
            'device_id' => $deviceId,
        ]);
        
        // Prepare message content
        $messageContent = $this->prepareMessage($template, $code);
        
        Log::info('OTP created', [
            'otp_id' => $otp->id,
            'user_id' => $user->id,
            'phone_number' => $phoneNumber,
            'expires_at' => $otp->expires_at,
        ]);
        
        return [
            'otp_id' => $otp->id,
            'code' => $code, // For testing purposes, in production should not return
            'message_content' => $messageContent,
            'expires_at' => $otp->expires_at->toIso8601String(),
            'expires_in_minutes' => $expiryMinutes,
        ];
    }

    /**
     * Prepare message content from template or default
     */
    protected function prepareMessage(?Template $template, string $code): string
    {
        if ($template) {
            // Replace kode_otp variable
            $variables = ['kode_otp' => $code];
            
            // Merge with any other variables from template
            if ($template->variables && is_array($template->variables)) {
                foreach ($template->variables as $var) {
                    if ($var !== 'kode_otp' && !isset($variables[$var])) {
                        // Set default empty or placeholder
                        $variables[$var] = '';
                    }
                }
            }
            
            return $template->replaceVariables($variables);
        }
        
        // Default OTP message
        return "Kode OTP Anda adalah: {$code}. Jangan bagikan kode ini kepada siapapun. Kode berlaku selama 10 menit.";
    }

    /**
     * Verify OTP code
     */
    public function verify(string $phoneNumber, string $code, ?User $user = null): array
    {
        // Find pending OTP for this phone number
        $otp = Otp::forPhone($phoneNumber)
            ->pending()
            ->when($user, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$otp) {
            return [
                'success' => false,
                'message' => 'OTP tidak ditemukan atau sudah kadaluarsa',
            ];
        }
        
        // Check if expired
        if ($otp->isExpired()) {
            $otp->markAsExpired();
            return [
                'success' => false,
                'message' => 'OTP sudah kadaluarsa',
            ];
        }
        
        // Check if already verified
        if ($otp->isVerified()) {
            return [
                'success' => false,
                'message' => 'OTP sudah pernah digunakan',
            ];
        }
        
        // Verify code
        if ($otp->code === $code) {
            $otp->markAsVerified();
            
            Log::info('OTP verified', [
                'otp_id' => $otp->id,
                'phone_number' => $phoneNumber,
            ]);
            
            return [
                'success' => true,
                'message' => 'OTP berhasil diverifikasi',
                'otp_id' => $otp->id,
            ];
        } else {
            $otp->incrementAttempts();
            
            $remainingAttempts = 5 - $otp->attempts;
            
            return [
                'success' => false,
                'message' => 'Kode OTP salah',
                'remaining_attempts' => max(0, $remainingAttempts),
            ];
        }
    }

    /**
     * Get OTP status
     */
    public function getStatus(string $otpId): ?array
    {
        $otp = Otp::find($otpId);
        
        if (!$otp) {
            return null;
        }
        
        return [
            'id' => $otp->id,
            'phone_number' => $otp->phone_number,
            'status' => $otp->status,
            'expires_at' => $otp->expires_at->toIso8601String(),
            'is_expired' => $otp->isExpired(),
            'is_verified' => $otp->isVerified(),
            'attempts' => $otp->attempts,
            'verified_at' => $otp->verified_at?->toIso8601String(),
        ];
    }

    /**
     * Cleanup expired OTPs
     */
    public function cleanupExpired(): int
    {
        $expired = Otp::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);
        
        return $expired;
    }
}

