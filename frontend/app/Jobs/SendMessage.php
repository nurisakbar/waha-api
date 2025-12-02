<?php

namespace App\Jobs;

use App\Helpers\PhoneNumberHelper;
use App\Models\Message;
use App\Models\MessagePricingSetting;
use App\Models\QuotaUsageLog;
use App\Models\UserQuota;
use App\Models\WhatsAppSession;
use App\Services\WahaService;
use App\Services\WatermarkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected $messageId;
    protected $sessionId;
    protected $chatId;
    protected $messageType;
    protected $content;
    protected $mediaPath;
    protected $documentPath;
    protected $documentUrl;
    protected $caption;
    protected $chatType;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $messageId,
        string $sessionId,
        string $chatId,
        string $messageType,
        ?string $content = null,
        ?string $mediaPath = null,
        ?string $documentPath = null,
        ?string $documentUrl = null,
        ?string $caption = null,
        string $chatType = 'personal'
    ) {
        $this->messageId = $messageId;
        $this->sessionId = $sessionId;
        $this->chatId = $chatId;
        $this->messageType = $messageType;
        $this->content = $content;
        $this->mediaPath = $mediaPath;
        $this->documentPath = $documentPath;
        $this->documentUrl = $documentUrl;
        $this->caption = $caption;
        $this->chatType = $chatType;
    }

    /**
     * Execute the job.
     */
    public function handle(WahaService $wahaService, WatermarkService $watermarkService): void
    {
        $message = Message::find($this->messageId);
        if (!$message) {
            Log::error('SendMessage Job: Message not found', ['message_id' => $this->messageId]);
            return;
        }

        $session = WhatsAppSession::find($this->sessionId);
        if (!$session || $session->status !== 'connected') {
            Log::error('SendMessage Job: Device not found or not connected', [
                'session_id' => $this->sessionId,
                'message_id' => $this->messageId,
            ]);
            $message->update([
                'status' => 'failed',
                'error_message' => 'Device not found or not connected',
            ]);
            return;
        }

        // Get pricing settings
        $pricing = MessagePricingSetting::getActive();
        $userQuota = UserQuota::getOrCreateForUser($message->user_id);

        try {
            $result = null;
            $finalContent = $this->content;
            $price = 0;
            $quotaDeducted = false;
            $quotaType = null; // 'text_quota', 'multimedia_quota', or 'balance'
            $quotaAmount = 0;

            switch ($this->messageType) {
                case 'text':
                    // Determine if should use watermark (free) or premium
                    $watermarkPrice = $pricing->getPriceForMessageType('text', true);
                    $premiumPrice = $pricing->getPriceForMessageType('text', false);
                    
                    // PRIORITAS: 1. text_quota (non-watermark), 2. free_text_quota (watermark), 3. balance
                    
                    // Prioritas 1: Cek text_quota (premium, tanpa watermark) terlebih dahulu
                    if ($userQuota->text_quota > 0) {
                        // Use text quota first
                        if (!$userQuota->deductTextQuota(1)) {
                            throw new \Exception('Insufficient text quota');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'text_quota';
                        $quotaAmount = 1;
                        $price = 0; // Tidak ada biaya karena menggunakan quota
                        $withWatermark = false;
                        
                        // Log quota usage
                        QuotaUsageLog::create([
                            'user_id' => $message->user_id,
                            'message_id' => $this->messageId,
                            'quota_type' => 'text_quota',
                            'amount' => 1,
                            'message_type' => 'text',
                            'description' => 'Premium text message (without watermark)',
                        ]);
                        
                        Log::info('SendMessage Job: Text quota deducted', [
                            'message_id' => $this->messageId,
                            'remaining_quota' => $userQuota->fresh()->text_quota,
                        ]);
                        
                        // Tidak perlu watermark, gunakan content asli
                        $finalContent = $this->content;
                    }
                    // Prioritas 2: Jika text_quota habis, cek free_text_quota (dengan watermark)
                    elseif ($watermarkPrice == 0 && $userQuota->hasFreeTextQuota(1)) {
                        // Deduct free text quota
                        if (!$userQuota->deductFreeTextQuota(1)) {
                            throw new \Exception('Insufficient free text quota. Please wait until next month or purchase premium quota.');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'free_text_quota';
                        $quotaAmount = 1;
                        $price = 0;
                        $withWatermark = true;
                        
                        // Log quota usage
                        QuotaUsageLog::create([
                            'user_id' => $message->user_id,
                            'message_id' => $this->messageId,
                            'quota_type' => 'free_text_quota',
                            'amount' => 1,
                            'message_type' => 'text',
                            'description' => 'Free text message with watermark',
                        ]);
                        
                        Log::info('SendMessage Job: Free text quota deducted', [
                            'message_id' => $this->messageId,
                            'remaining_free_quota' => $userQuota->fresh()->free_text_quota,
                        ]);
                        
                        // Tambahkan watermark
                        $finalContent = $watermarkService->addWatermark($this->content, $pricing->watermark_text);
                        // Update message content in database
                        $message->update(['content' => $finalContent]);
                        
                        Log::info('SendMessage Job: Using free text message with watermark', [
                            'message_id' => $this->messageId,
                        ]);
                    }
                    // Prioritas 3: Jika keduanya habis, gunakan balance
                    elseif ($userQuota->hasEnoughBalance($premiumPrice)) {
                        // Use balance
                        if (!$userQuota->deductBalance($premiumPrice)) {
                            throw new \Exception('Insufficient balance');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'balance';
                        $quotaAmount = $premiumPrice;
                        $price = $premiumPrice;
                        $withWatermark = false;
                        
                        // Log quota usage
                        QuotaUsageLog::create([
                            'user_id' => $message->user_id,
                            'message_id' => $this->messageId,
                            'quota_type' => 'balance',
                            'amount' => $premiumPrice,
                            'message_type' => 'text',
                            'description' => 'Premium text message paid with balance',
                        ]);
                        
                        Log::info('SendMessage Job: Balance deducted for premium text', [
                            'message_id' => $this->messageId,
                            'price' => $premiumPrice,
                            'remaining_balance' => $userQuota->fresh()->balance,
                        ]);
                        
                        // Tidak perlu watermark, gunakan content asli
                        $finalContent = $this->content;
                    } else {
                        // Semua quota habis
                        throw new \Exception('Insufficient quota or balance. Please purchase quota first.');
                    }

                    Log::info('SendMessage Job: Sending text message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $this->chatId,
                        'message_id' => $this->messageId,
                        'with_watermark' => $withWatermark,
                        'price' => $price,
                    ]);

                    $result = $wahaService->sendText(
                        $session->session_id,
                        $this->chatId,
                        $finalContent
                    );
                    break;

                case 'image':
                    // Multimedia message - charge user
                    $price = $pricing->getPriceForMessageType('image');
                    
                    // Check quota BEFORE sending
                    if ($userQuota->multimedia_quota > 0) {
                        // Use multimedia quota first
                        if (!$userQuota->deductMultimediaQuota(1)) {
                            throw new \Exception('Insufficient multimedia quota');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'multimedia_quota';
                        $quotaAmount = 1;
                        
                        // Log quota usage
                        QuotaUsageLog::create([
                            'user_id' => $message->user_id,
                            'message_id' => $this->messageId,
                            'quota_type' => 'multimedia_quota',
                            'amount' => 1,
                            'message_type' => 'image',
                            'description' => 'Image message',
                        ]);
                        
                        Log::info('SendMessage Job: Multimedia quota deducted for image', [
                            'message_id' => $this->messageId,
                            'remaining_quota' => $userQuota->fresh()->multimedia_quota,
                        ]);
                    } elseif ($userQuota->hasEnoughBalance($price)) {
                        // Use balance
                        if (!$userQuota->deductBalance($price)) {
                            throw new \Exception('Insufficient balance');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'balance';
                        $quotaAmount = $price;
                        
                        // Log quota usage
                        QuotaUsageLog::create([
                            'user_id' => $message->user_id,
                            'message_id' => $this->messageId,
                            'quota_type' => 'balance',
                            'amount' => $price,
                            'message_type' => 'image',
                            'description' => 'Image message paid with balance',
                        ]);
                        
                        Log::info('SendMessage Job: Balance deducted for image', [
                            'message_id' => $this->messageId,
                            'price' => $price,
                            'remaining_balance' => $userQuota->fresh()->balance,
                        ]);
                    } else {
                        throw new \Exception('Insufficient quota or balance. Please purchase quota first.');
                    }

                    Log::info('SendMessage Job: Sending image message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $this->chatId,
                        'message_id' => $this->messageId,
                    ]);

                    $fullPath = $this->mediaPath ? storage_path('app/public/' . $this->mediaPath) : null;
                    if (!$fullPath || !file_exists($fullPath)) {
                        throw new \Exception('Image file not found: ' . $this->mediaPath);
                    }

                    $result = $wahaService->sendImage(
                        $session->session_id,
                        $this->chatId,
                        $fullPath,
                        $this->caption
                    );
                    break;

                case 'document':
                    // Multimedia message - charge user
                    $price = $pricing->getPriceForMessageType('document');
                    
                    // Check quota BEFORE sending
                    if ($userQuota->multimedia_quota > 0) {
                        // Use multimedia quota first
                        if (!$userQuota->deductMultimediaQuota(1)) {
                            throw new \Exception('Insufficient multimedia quota');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'multimedia_quota';
                        $quotaAmount = 1;
                        
                        // Log quota usage
                        QuotaUsageLog::create([
                            'user_id' => $message->user_id,
                            'message_id' => $this->messageId,
                            'quota_type' => 'multimedia_quota',
                            'amount' => 1,
                            'message_type' => 'document',
                            'description' => 'Document message',
                        ]);
                        
                        Log::info('SendMessage Job: Multimedia quota deducted for document', [
                            'message_id' => $this->messageId,
                            'remaining_quota' => $userQuota->fresh()->multimedia_quota,
                        ]);
                    } elseif ($userQuota->hasEnoughBalance($price)) {
                        // Use balance
                        if (!$userQuota->deductBalance($price)) {
                            throw new \Exception('Insufficient balance');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'balance';
                        $quotaAmount = $price;
                        
                        // Log quota usage
                        QuotaUsageLog::create([
                            'user_id' => $message->user_id,
                            'message_id' => $this->messageId,
                            'quota_type' => 'balance',
                            'amount' => $price,
                            'message_type' => 'document',
                            'description' => 'Document message paid with balance',
                        ]);
                        
                        Log::info('SendMessage Job: Balance deducted for document', [
                            'message_id' => $this->messageId,
                            'price' => $price,
                            'remaining_balance' => $userQuota->fresh()->balance,
                        ]);
                    } else {
                        throw new \Exception('Insufficient quota or balance. Please purchase quota first.');
                    }

                    Log::info('SendMessage Job: Sending document message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $this->chatId,
                        'message_id' => $this->messageId,
                        'has_file' => !empty($this->documentPath),
                        'has_url' => !empty($this->documentUrl),
                    ]);

                    if ($this->documentPath) {
                        $fullPath = storage_path('app/public/' . $this->documentPath);
                        if (!file_exists($fullPath)) {
                            throw new \Exception('Document file not found: ' . $this->documentPath);
                        }
                        $fileName = basename($fullPath);
                        $result = $wahaService->sendDocument(
                            $session->session_id,
                            $this->chatId,
                            $fullPath,
                            $fileName
                        );
                    } elseif ($this->documentUrl) {
                        $result = $wahaService->sendDocumentByUrl(
                            $session->session_id,
                            $this->chatId,
                            $this->documentUrl
                        );
                    } else {
                        throw new \Exception('No document file or URL provided');
                    }
                    break;
            }

            if ($result && $result['success']) {
                $whatsappId = $result['data']['id'] ?? null;
                if (is_array($whatsappId)) {
                    $whatsappId = json_encode($whatsappId);
                }

                $ack = $result['data']['ack'] ?? $result['data']['_data']['ack'] ?? null;
                $status = 'sent';
                if ($ack === 0) {
                    $status = 'pending';
                }

                $message->update([
                    'whatsapp_message_id' => $whatsappId,
                    'status' => $status,
                    'sent_at' => now(),
                ]);

                Log::info('SendMessage Job: Message sent successfully', [
                    'message_id' => $this->messageId,
                    'whatsapp_message_id' => $whatsappId,
                    'status' => $status,
                    'quota_deducted' => $quotaDeducted,
                    'quota_type' => $quotaType,
                ]);
            } else {
                $errorMessage = $result['error'] ?? 'Failed to send message';
                if (is_array($errorMessage)) {
                    $errorMessage = json_encode($errorMessage);
                }

                // If quota was deducted but message failed, refund it and delete usage log
                if ($quotaDeducted) {
                    $userQuota = UserQuota::getOrCreateForUser($message->user_id);
                    if ($quotaType === 'free_text_quota') {
                        $userQuota->addFreeTextQuota($quotaAmount);
                        // Delete usage log
                        QuotaUsageLog::where('message_id', $this->messageId)
                            ->where('quota_type', 'free_text_quota')
                            ->delete();
                        Log::info('SendMessage Job: Refunded free text quota due to send failure', [
                            'message_id' => $this->messageId,
                            'amount' => $quotaAmount,
                        ]);
                    } elseif ($quotaType === 'text_quota') {
                        $userQuota->addTextQuota($quotaAmount);
                        // Delete usage log
                        QuotaUsageLog::where('message_id', $this->messageId)
                            ->where('quota_type', 'text_quota')
                            ->delete();
                        Log::info('SendMessage Job: Refunded text quota due to send failure', [
                            'message_id' => $this->messageId,
                            'amount' => $quotaAmount,
                        ]);
                    } elseif ($quotaType === 'multimedia_quota') {
                        $userQuota->addMultimediaQuota($quotaAmount);
                        // Delete usage log
                        QuotaUsageLog::where('message_id', $this->messageId)
                            ->where('quota_type', 'multimedia_quota')
                            ->delete();
                        Log::info('SendMessage Job: Refunded multimedia quota due to send failure', [
                            'message_id' => $this->messageId,
                            'amount' => $quotaAmount,
                        ]);
                    } elseif ($quotaType === 'balance') {
                        $userQuota->addBalance($quotaAmount);
                        // Delete usage log
                        QuotaUsageLog::where('message_id', $this->messageId)
                            ->where('quota_type', 'balance')
                            ->delete();
                        Log::info('SendMessage Job: Refunded balance due to send failure', [
                            'message_id' => $this->messageId,
                            'amount' => $quotaAmount,
                        ]);
                    }
                }

                $message->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                ]);

                Log::error('SendMessage Job: Failed to send message', [
                    'message_id' => $this->messageId,
                    'error' => $errorMessage,
                ]);

                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            // If quota was deducted but exception occurred, refund it and delete usage log
            if (isset($quotaDeducted) && $quotaDeducted && isset($quotaType) && isset($quotaAmount)) {
                try {
                    $userQuota = UserQuota::getOrCreateForUser($message->user_id);
                    if ($quotaType === 'free_text_quota') {
                        $userQuota->addFreeTextQuota($quotaAmount);
                        // Delete usage log
                        QuotaUsageLog::where('message_id', $this->messageId)
                            ->where('quota_type', 'free_text_quota')
                            ->delete();
                        Log::info('SendMessage Job: Refunded free text quota due to exception', [
                            'message_id' => $this->messageId,
                            'amount' => $quotaAmount,
                        ]);
                    } elseif ($quotaType === 'text_quota') {
                        $userQuota->addTextQuota($quotaAmount);
                        // Delete usage log
                        QuotaUsageLog::where('message_id', $this->messageId)
                            ->where('quota_type', 'text_quota')
                            ->delete();
                        Log::info('SendMessage Job: Refunded text quota due to exception', [
                            'message_id' => $this->messageId,
                            'amount' => $quotaAmount,
                        ]);
                    } elseif ($quotaType === 'multimedia_quota') {
                        $userQuota->addMultimediaQuota($quotaAmount);
                        // Delete usage log
                        QuotaUsageLog::where('message_id', $this->messageId)
                            ->where('quota_type', 'multimedia_quota')
                            ->delete();
                        Log::info('SendMessage Job: Refunded multimedia quota due to exception', [
                            'message_id' => $this->messageId,
                            'amount' => $quotaAmount,
                        ]);
                    } elseif ($quotaType === 'balance') {
                        $userQuota->addBalance($quotaAmount);
                        // Delete usage log
                        QuotaUsageLog::where('message_id', $this->messageId)
                            ->where('quota_type', 'balance')
                            ->delete();
                        Log::info('SendMessage Job: Refunded balance due to exception', [
                            'message_id' => $this->messageId,
                            'amount' => $quotaAmount,
                        ]);
                    }
                } catch (\Exception $refundException) {
                    Log::error('SendMessage Job: Failed to refund quota', [
                        'message_id' => $this->messageId,
                        'error' => $refundException->getMessage(),
                    ]);
                }
            }

            Log::error('SendMessage Job: Exception occurred', [
                'message_id' => $this->messageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $message->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $message = Message::find($this->messageId);
        if ($message) {
            $message->update([
                'status' => 'failed',
                'error_message' => 'Job failed after ' . $this->tries . ' attempts: ' . $exception->getMessage(),
            ]);
        }

        Log::error('SendMessage Job: Job failed permanently', [
            'message_id' => $this->messageId,
            'error' => $exception->getMessage(),
        ]);
    }
}
