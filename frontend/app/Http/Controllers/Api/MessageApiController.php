<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PhoneNumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
use App\Models\Message;
use App\Models\MessagePricingSetting;
use App\Models\QuotaUsageLog;
use App\Models\Template;
use App\Models\UserQuota;
use App\Models\WhatsAppSession;
use App\Services\ApiUsageService;
use App\Services\TemplateService;
use App\Services\WatermarkService;
use App\Services\WahaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MessageApiController extends Controller
{
    protected WahaService $wahaService;
    protected ApiUsageService $usageService;
    protected TemplateService $templateService;

    public function __construct(WahaService $wahaService, ApiUsageService $usageService, TemplateService $templateService)
    {
        $this->wahaService = $wahaService;
        $this->usageService = $usageService;
        $this->templateService = $templateService;
    }

    /**
     * Send message (text, image, or document)
     */
    public function store(SendMessageRequest $request, $session = null)
    {
        $startTime = microtime(true);
        
        // Support both formats:
        // 1. /api/v1/messages (device_id in body)
        // 2. /api/v1/devices/{session}/messages (device in URL)
        
        // If session is provided in URL, use it; otherwise use device_id from request body
        $sessionId = $session ?? $request->device_id;
        
        if (!$sessionId) {
            $this->usageService->log($request, 400, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Device ID is required (either in URL or request body as device_id)',
            ], 400);
        }

        // Get session
        $session = WhatsAppSession::where('session_id', $sessionId)
            ->where('user_id', $request->user->id)
            ->where('status', 'connected')
            ->first();

        if (!$session) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Device not found or not connected',
            ], 404);
        }

        // Determine chat type (personal or group)
        $chatType = $request->input('chat_type', 'personal'); // Default to personal
        
        // Handle chat ID based on type
        if ($chatType === 'group') {
            // For group, the 'to' field should be the group ID (can be full format or just ID)
            $toValue = $request->to;
            
            // If already contains @g.us, use as is
            if (strpos($toValue, '@g.us') !== false) {
                $chatId = $toValue;
            } else {
                // Otherwise, append @g.us
                $chatId = $toValue . '@g.us';
            }
            
            // For groups, we don't normalize as phone number
            $normalizedNumber = $toValue;
        } else {
            // For personal chat, normalize phone number
            $normalizedNumber = PhoneNumberHelper::normalize($request->to);
            if (!$normalizedNumber) {
                $this->usageService->log($request, 400, $startTime);
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid phone number format',
                ], 400);
            }
            
            $chatId = $normalizedNumber . '@c.us';
        }

        try {
            $result = null;
            $template = null;
            $processedTemplate = null;
            
            // Handle template if provided
            if ($request->template_id) {
                $template = Template::where('id', $request->template_id)
                    ->where('user_id', $request->user->id)
                    ->where('is_active', true)
                    ->first();

                if (!$template) {
                    $this->usageService->log($request, 404, $startTime);
                    return response()->json([
                        'success' => false,
                        'error' => 'Template not found or inactive',
                    ], 404);
                }

                // Validate variables
                $variables = $request->input('variables', []);
                $validation = $this->templateService->validateVariables($template, $variables);
                
                if (!$validation['valid']) {
                    $this->usageService->log($request, 400, $startTime);
                    return response()->json([
                        'success' => false,
                        'error' => 'Missing required variables',
                        'missing_variables' => $validation['missing'],
                    ], 400);
                }

                // Process template
                $processedTemplate = $this->templateService->processTemplate($template, $variables);
            }

            $messageData = [
                'user_id' => $request->user->id,
                'session_id' => $session->id,
                'from_number' => null,
                'to_number' => $normalizedNumber,
                'chat_type' => $chatType,
                'message_type' => $template ? $processedTemplate['message_type'] : $request->message_type,
                'direction' => 'outgoing',
                'status' => 'pending', // Set to pending first, update to sent after successful send
            ];

            // Use template content if template is provided
            $messageType = $template ? $processedTemplate['message_type'] : $request->message_type;

            // Get pricing settings and quota
            $pricing = MessagePricingSetting::getActive();
            $watermarkService = app(WatermarkService::class);
            $userQuota = UserQuota::getOrCreateForUser($request->user->id);
            
            // Variables for quota tracking
            $quotaDeducted = false;
            $quotaType = null;
            $quotaAmount = 0;
            $messageId = null; // Will be set after message is created

            // Handle different message types
            switch ($messageType) {
                case 'text':
                    // Support both 'text' and 'message' fields, or use template content
                    if ($template) {
                        $textContent = $processedTemplate['content'];
                    } else {
                        $textContent = $request->input('text') ?? $request->input('message');
                    }
                    
                    // Determine if should use watermark (free) or premium
                    $watermarkPrice = $pricing->getPriceForMessageType('text', true);
                    $premiumPrice = $pricing->getPriceForMessageType('text', false);
                    $finalContent = $textContent;
                    
                    // PRIORITAS: 1. text_quota (non-watermark), 2. free_text_quota (watermark), 3. balance
                    
                    // Prioritas 1: Cek text_quota (premium, tanpa watermark) terlebih dahulu
                    if ($userQuota->text_quota > 0) {
                        if (!$userQuota->deductTextQuota(1)) {
                            throw new \Exception('Insufficient text quota');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'text_quota';
                        $quotaAmount = 1;
                        // Tidak perlu watermark, gunakan content asli
                        $finalContent = $textContent;
                    }
                    // Prioritas 2: Jika text_quota habis, cek free_text_quota (dengan watermark)
                    elseif ($watermarkPrice == 0 && $userQuota->hasFreeTextQuota(1)) {
                        if (!$userQuota->deductFreeTextQuota(1)) {
                            throw new \Exception('Insufficient free text quota. Please wait until next month or purchase premium quota.');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'free_text_quota';
                        $quotaAmount = 1;
                        // Tambahkan watermark
                        $finalContent = $watermarkService->addWatermark($textContent, $pricing->watermark_text);
                    }
                    // Prioritas 3: Jika keduanya habis, gunakan balance
                    elseif ($userQuota->hasEnoughBalance($premiumPrice)) {
                        if (!$userQuota->deductBalance($premiumPrice)) {
                            throw new \Exception('Insufficient balance');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'balance';
                        $quotaAmount = $premiumPrice;
                        // Tidak perlu watermark, gunakan content asli
                        $finalContent = $textContent;
                    } else {
                        // Semua quota habis
                        throw new \Exception('Insufficient quota or balance. Please purchase quota first.');
                    }
                    
                    // Log the request details for debugging
                    \Log::info('API: Sending text message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'text_length' => strlen($finalContent),
                        'user_id' => $request->user->id,
                        'quota_type' => $quotaType,
                    ]);
                    
                    $result = $this->wahaService->sendText(
                        $session->session_id,
                        $chatId,
                        $finalContent
                    );
                    
                    // Log the result
                    \Log::info('API: WAHA sendText response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                        'data' => $result['data'] ?? null,
                    ]);
                    
                    $messageData['content'] = $finalContent;
                    break;

                case 'image':
                    // Handle template for image messages
                    if ($template) {
                        $imageUrl = $processedTemplate['metadata']['image_url'] ?? $request->image_url;
                        $caption = $processedTemplate['content'] ?? $request->caption;
                    } else {
                        $imageUrl = $request->image_url;
                        $caption = $request->caption;
                    }
                    
                    // Multimedia message - charge user
                    $price = $pricing->getPriceForMessageType('image');
                    
                    // Check quota BEFORE sending
                    if ($userQuota->multimedia_quota > 0) {
                        if (!$userQuota->deductMultimediaQuota(1)) {
                            throw new \Exception('Insufficient multimedia quota');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'multimedia_quota';
                        $quotaAmount = 1;
                    } elseif ($userQuota->hasEnoughBalance($price)) {
                        if (!$userQuota->deductBalance($price)) {
                            throw new \Exception('Insufficient balance');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'balance';
                        $quotaAmount = $price;
                    } else {
                        throw new \Exception('Insufficient quota or balance. Please purchase quota first.');
                    }
                    
                    \Log::info('API: Sending image message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'user_id' => $request->user->id,
                        'image_url' => $imageUrl,
                        'has_caption' => !empty($caption),
                        'quota_type' => $quotaType,
                    ]);
                    
                    $result = $this->wahaService->sendImageByUrl(
                        $session->session_id,
                        $chatId,
                        $imageUrl,
                        $caption
                    );
                    
                    \Log::info('API: WAHA sendImage response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                        'data' => $result['data'] ?? null,
                    ]);
                    
                    $messageData['media_url'] = $imageUrl;
                    $messageData['caption'] = $caption;
                    break;

                case 'video':
                    // Increase max execution time for video uploads (120 seconds)
                    $originalMaxExecutionTime = ini_get('max_execution_time');
                    set_time_limit(120);
                    
                    // Multimedia message - charge user
                    $price = $pricing->getPriceForMessageType('video');
                    
                    // Check quota BEFORE sending
                    if ($userQuota->multimedia_quota > 0) {
                        if (!$userQuota->deductMultimediaQuota(1)) {
                            throw new \Exception('Insufficient multimedia quota');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'multimedia_quota';
                        $quotaAmount = 1;
                    } elseif ($userQuota->hasEnoughBalance($price)) {
                        if (!$userQuota->deductBalance($price)) {
                            throw new \Exception('Insufficient balance');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'balance';
                        $quotaAmount = $price;
                    } else {
                        throw new \Exception('Insufficient quota or balance. Please purchase quota first.');
                    }
                    
                    $videoUrl = $request->video_url;
                    
                    \Log::info('API: Sending video message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'user_id' => $request->user->id,
                        'video_url' => $videoUrl,
                        'has_caption' => !empty($request->caption),
                        'as_note' => $request->as_note ?? false,
                        'convert' => $request->convert ?? false,
                        'quota_type' => $quotaType,
                    ]);
                    
                    $result = $this->wahaService->sendVideoByUrl(
                        $session->session_id,
                        $chatId,
                        $videoUrl,
                        $request->caption,
                        $request->as_note ?? false,
                        $request->convert ?? false
                    );
                    
                    // Restore original max execution time
                    if ($originalMaxExecutionTime !== false) {
                        set_time_limit((int)$originalMaxExecutionTime);
                    }
                    
                    \Log::info('API: WAHA sendVideo response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                        'data' => $result['data'] ?? null,
                    ]);
                    
                    $messageData['media_url'] = $videoUrl;
                    $messageData['caption'] = $request->caption;
                    break;

                case 'document':
                    // Multimedia message - charge user
                    $price = $pricing->getPriceForMessageType('document');
                    
                    // Check quota BEFORE sending
                    if ($userQuota->multimedia_quota > 0) {
                        if (!$userQuota->deductMultimediaQuota(1)) {
                            throw new \Exception('Insufficient multimedia quota');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'multimedia_quota';
                        $quotaAmount = 1;
                    } elseif ($userQuota->hasEnoughBalance($price)) {
                        if (!$userQuota->deductBalance($price)) {
                            throw new \Exception('Insufficient balance');
                        }
                        $quotaDeducted = true;
                        $quotaType = 'balance';
                        $quotaAmount = $price;
                    } else {
                        throw new \Exception('Insufficient quota or balance. Please purchase quota first.');
                    }
                    
                    $documentUrl = $request->document_url;
                    
                    \Log::info('API: Sending document message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'user_id' => $request->user->id,
                        'document_url' => $documentUrl,
                        'filename' => $request->filename,
                        'quota_type' => $quotaType,
                    ]);
                    
                    $result = $this->wahaService->sendDocumentByUrl(
                        $session->session_id,
                        $chatId,
                        $documentUrl,
                        $request->filename
                    );
                    
                    \Log::info('API: WAHA sendDocument response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                    ]);
                    
                    $messageData['media_url'] = $documentUrl;
                    $messageData['caption'] = $request->caption;
                    break;

                case 'poll':
                    \Log::info('API: Sending poll message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'user_id' => $request->user->id,
                        'poll_name' => $request->poll_name,
                        'options_count' => count($request->poll_options),
                        'multiple_answers' => $request->multiple_answers ?? false,
                        'fallback_to_text' => $request->fallback_to_text ?? false,
                    ]);
                    
                    // Try to send poll, with optional fallback to text if not supported
                    $result = $this->wahaService->sendPoll(
                        $session->session_id,
                        $chatId,
                        $request->poll_name,
                        $request->poll_options,
                        $request->multiple_answers ?? false,
                        $request->fallback_to_text ?? false
                    );
                    
                    \Log::info('API: WAHA sendPoll response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                        'data' => $result['data'] ?? null,
                        'engine_not_supported' => $result['engine_not_supported'] ?? false,
                        'fallback_used' => $result['fallback_used'] ?? false,
                    ]);
                    
                    // If fallback was used, the content will be the text message
                    // Otherwise, store poll data as JSON
                    if ($result['success'] && isset($result['fallback_used']) && $result['fallback_used']) {
                        // Fallback to text was used - get the text content from response
                        // For text messages, the content is in the Message.extendedTextMessage.text
                        $textContent = '';
                        if (isset($result['data']['_data']['Message']['extendedTextMessage']['text'])) {
                            $textContent = $result['data']['_data']['Message']['extendedTextMessage']['text'];
                        } elseif (isset($result['data']['body'])) {
                            $textContent = $result['data']['body'];
                        }
                        $messageData['content'] = $textContent;
                        $messageData['message_type'] = 'text'; // Change type to text since it was sent as text
                    } elseif ($result['success']) {
                        // Real poll was sent
                        $messageData['content'] = json_encode([
                            'poll_name' => $request->poll_name,
                            'options' => $request->poll_options,
                            'multiple_answers' => $request->multiple_answers ?? false,
                        ]);
                    } else {
                        // Poll failed, store poll data anyway
                        $messageData['content'] = json_encode([
                            'poll_name' => $request->poll_name,
                            'options' => $request->poll_options,
                            'multiple_answers' => $request->multiple_answers ?? false,
                        ]);
                    }
                    break;
                    
                case 'button':
                    // Handle template for button messages
                    if ($template) {
                        $metadata = $processedTemplate['metadata'] ?? [];
                        $body = $processedTemplate['content'] ?? $request->body;
                        $buttons = $metadata['buttons'] ?? $request->buttons;
                        $header = $metadata['header'] ?? $request->header;
                        $footer = $metadata['footer'] ?? $request->footer;
                        $headerImage = $metadata['header_image'] ?? $request->header_image ?? $request->headerImage;
                    } else {
                        $body = $request->body;
                        $buttons = $request->buttons;
                        $header = $request->header;
                        $footer = $request->footer;
                        $headerImage = $request->header_image ?? $request->headerImage;
                    }
                    
                    \Log::info('API: Sending button message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'user_id' => $request->user->id,
                        'body_length' => strlen($body ?? ''),
                        'buttons_count' => count($buttons ?? []),
                        'has_header' => !empty($header),
                        'has_footer' => !empty($footer),
                        'has_header_image' => !empty($headerImage),
                    ]);
                    
                    $result = $this->wahaService->sendButton(
                        $session->session_id,
                        $chatId,
                        $body,
                        $buttons,
                        $header,
                        $footer,
                        $headerImage,
                        $request->fallback_to_text ?? false // Fallback to text if button fails
                    );
                    
                    \Log::info('API: WAHA sendButton response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                        'data' => $result['data'] ?? null,
                        'full_response' => json_encode($result),
                    ]);
                    
                    // If fallback was used, store text content; otherwise store button data as JSON
                    if ($result['success'] && isset($result['fallback_used']) && $result['fallback_used']) {
                        // Fallback to text was used - get the text content from response
                        $textContent = '';
                        if (isset($result['data']['_data']['Message']['extendedTextMessage']['text'])) {
                            $textContent = $result['data']['_data']['Message']['extendedTextMessage']['text'];
                        } elseif (isset($result['data']['body'])) {
                            $textContent = $result['data']['body'];
                        }
                        $messageData['content'] = $textContent;
                        $messageData['message_type'] = 'text'; // Change type to text since it was sent as text
                    } else {
                        // Store button data as JSON
                        $messageData['content'] = json_encode([
                            'body' => $body,
                            'buttons' => $buttons,
                            'header' => $header,
                            'footer' => $footer,
                            'header_image' => $headerImage,
                        ]);
                    }
                    break;

                case 'list':
                    // Handle template for list messages
                    if ($template) {
                        $metadata = $processedTemplate['metadata'] ?? [];
                        $listMessage = $metadata['message'] ?? $request->message;
                        $replyTo = $metadata['reply_to'] ?? $request->reply_to;
                    } else {
                        $listMessage = $request->message;
                        $replyTo = $request->reply_to;
                    }
                    
                    \Log::info('API: Sending list message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'user_id' => $request->user->id,
                        'message_title' => $listMessage['title'] ?? null,
                        'sections_count' => count($listMessage['sections'] ?? []),
                    ]);

                    $result = $this->wahaService->sendList(
                        $session->session_id,
                        $chatId,
                        $listMessage,
                        $replyTo
                    );

                    \Log::info('API: WAHA sendList response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                        'data' => $result['data'] ?? null,
                    ]);

                    // Store list data as JSON
                    $messageData['content'] = json_encode([
                        'message' => $listMessage,
                        'reply_to' => $replyTo,
                    ]);
                    break;
            }

            if ($result && $result['success']) {
                // Extract WhatsApp message ID - try multiple possible locations
                // For button messages, id is in key.id
                $whatsappId = $result['data']['id'] ?? 
                             $result['data']['key']['id'] ?? 
                             $result['data']['_data']['Info']['ID'] ?? 
                             $result['data']['messageId'] ?? 
                             null;
                
                if (is_array($whatsappId)) {
                    $whatsappId = json_encode($whatsappId);
                }
                
                // Check ack status from WAHA response
                // For button messages, status might be "PENDING" string instead of ack number
                $statusFromResponse = $result['data']['status'] ?? null;
                $ack = $result['data']['ack'] ?? 
                       $result['data']['_data']['ack'] ?? 
                       $result['data']['_data']['Info']['ack'] ?? 
                       null;
                $status = 'sent';
                
                // ack: 0 = pending, 1 = delivered, 2 = read, 3 = played
                // For button messages, status might be "PENDING" string
                // If ack is 0 or status is "PENDING", message might not be actually sent yet
                if ($ack === 0 || $statusFromResponse === 'PENDING') {
                    $status = 'pending';
                    \Log::warning('API: Message sent but status is pending', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'whatsapp_message_id' => $whatsappId,
                        'ack' => $ack,
                        'status_from_response' => $statusFromResponse,
                    ]);
                }
                
                $messageData['whatsapp_message_id'] = $whatsappId;
                $messageData['status'] = $status;
                $messageData['sent_at'] = now();

                $message = Message::create($messageData);
                $messageId = $message->id;

                // Log quota usage after successful send
                if ($quotaDeducted && $quotaType && $quotaAmount > 0) {
                    QuotaUsageLog::create([
                        'user_id' => $request->user->id,
                        'message_id' => $messageId,
                        'quota_type' => $quotaType,
                        'amount' => $quotaAmount,
                        'message_type' => $messageType,
                        'description' => $this->getQuotaDescription($messageType, $quotaType),
                    ]);
                }

                $this->usageService->log($request, 200, $startTime);

                \Log::info('API: Message sent successfully', [
                    'message_id' => $message->id,
                    'whatsapp_message_id' => $whatsappId,
                    'status' => $status,
                    'ack' => $ack,
                    'quota_type' => $quotaType,
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'message_id' => $message->id,
                        'whatsapp_message_id' => $result['data']['id'] ?? null,
                        'status' => $status,
                        'ack' => $ack,
                        'to' => $normalizedNumber,
                    ],
                ], 201);
            } else {
                // Log error details
                $errorMessage = $result['error'] ?? 'Failed to send message';
                
                // If quota was deducted but message failed, refund it
                if ($quotaDeducted && $quotaType && $quotaAmount > 0) {
                    // Create message first to get ID for log deletion
                    $messageData['status'] = 'failed';
                    $messageData['error_message'] = is_array($errorMessage) ? json_encode($errorMessage) : $errorMessage;
                    $failedMessage = Message::create($messageData);
                    $this->refundQuota($userQuota, $quotaType, $quotaAmount, $failedMessage->id);
                } else {
                    // Create message even if no quota was deducted
                    $messageData['status'] = 'failed';
                    $messageData['error_message'] = is_array($errorMessage) ? json_encode($errorMessage) : $errorMessage;
                    Message::create($messageData);
                }
                
                // Check if error indicates feature not supported by WAHA engine
                if (stripos($errorMessage, 'not implemented') !== false || 
                    stripos($errorMessage, 'not supported') !== false ||
                    stripos($errorMessage, 'WEBJS') !== false) {
                    $errorMessage = 'This feature is not supported by your WAHA engine. Please check WAHA documentation for supported features. Original error: ' . ($result['error'] ?? 'Unknown error');
                }
                
                // Check if error is related to video URL access (403)
                if (stripos($errorMessage, '403') !== false || stripos($errorMessage, 'forbidden') !== false) {
                    $errorMessage = 'Video URL is not accessible. The server hosting the video is blocking access (403 Forbidden). Please ensure the video URL is publicly accessible without authentication or IP restrictions.';
                }
                
                \Log::error('API: Failed to send message', [
                    'error' => $errorMessage,
                    'result' => $result,
                    'session_id' => $session->session_id,
                    'chat_id' => $chatId,
                    'quota_refunded' => $quotaDeducted,
                ]);
                
                $this->usageService->log($request, 500, $startTime);
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage,
                ], 500);
            }
        } catch (\Exception $e) {
            // If quota was deducted but exception occurred, refund it
            if (isset($quotaDeducted) && $quotaDeducted && isset($quotaType) && isset($quotaAmount) && isset($userQuota)) {
                try {
                    $this->refundQuota($userQuota, $quotaType, $quotaAmount);
                } catch (\Exception $refundException) {
                    \Log::error('API: Failed to refund quota', [
                        'error' => $refundException->getMessage(),
                        'quota_type' => $quotaType,
                        'quota_amount' => $quotaAmount,
                    ]);
                }
            }
            
            $this->usageService->log($request, 500, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refund quota to user
     */
    protected function refundQuota(UserQuota $userQuota, string $quotaType, float $quotaAmount, ?string $messageId = null): void
    {
        if ($quotaType === 'free_text_quota') {
            $userQuota->addFreeTextQuota($quotaAmount);
            if ($messageId) {
                QuotaUsageLog::where('quota_type', 'free_text_quota')
                    ->where('message_id', $messageId)
                    ->delete();
            }
        } elseif ($quotaType === 'text_quota') {
            $userQuota->addTextQuota($quotaAmount);
            if ($messageId) {
                QuotaUsageLog::where('quota_type', 'text_quota')
                    ->where('message_id', $messageId)
                    ->delete();
            }
        } elseif ($quotaType === 'multimedia_quota') {
            $userQuota->addMultimediaQuota($quotaAmount);
            if ($messageId) {
                QuotaUsageLog::where('quota_type', 'multimedia_quota')
                    ->where('message_id', $messageId)
                    ->delete();
            }
        } elseif ($quotaType === 'balance') {
            $userQuota->addBalance($quotaAmount);
            if ($messageId) {
                QuotaUsageLog::where('quota_type', 'balance')
                    ->where('message_id', $messageId)
                    ->delete();
            }
        }
        
        \Log::info('API: Quota refunded', [
            'quota_type' => $quotaType,
            'amount' => $quotaAmount,
            'message_id' => $messageId,
        ]);
    }

    /**
     * Get quota description for logging
     */
    protected function getQuotaDescription(string $messageType, string $quotaType): string
    {
        $descriptions = [
            'free_text_quota' => 'Free text message with watermark',
            'text_quota' => 'Premium text message (without watermark)',
            'multimedia_quota' => ucfirst($messageType) . ' message',
            'balance' => ucfirst($messageType) . ' message paid with balance',
        ];
        
        return $descriptions[$quotaType] ?? ucfirst($messageType) . ' message';
    }

    /**
     * Get messages for a session
     */
    public function index(Request $request, $session = null)
    {
        $startTime = microtime(true);
        
        // Support both formats:
        // 1. /api/v1/messages?device_id=xxx (device_id in query)
        // 2. /api/v1/devices/{session}/messages (device in URL)
        
        $sessionId = $session ?? $request->input('device_id');
        
        if (!$sessionId) {
            $this->usageService->log($request, 400, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Device ID is required (either in URL or query parameter as device_id)',
            ], 400);
        }
        
        $session = WhatsAppSession::where('session_id', $sessionId)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$session) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Device not found',
            ], 404);
        }

        $messages = Message::where('session_id', $session->id)
            ->where('user_id', $request->user->id)
            ->latest()
            ->paginate($request->get('per_page', 20));

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'last_page' => $messages->lastPage(),
            ],
        ]);
    }

    /**
     * Get message details
     */
    public function show(Request $request, $messageId)
    {
        $startTime = microtime(true);
        
        $message = Message::where('id', $messageId)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$message) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Message not found',
            ], 404);
        }

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => $message,
        ]);
    }

    /**
     * Sync messages from WAHA API to database
     */
    public function sync(Request $request, $sessionId)
    {
        $startTime = microtime(true);
        
        $session = WhatsAppSession::where('session_id', $sessionId)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$session) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Device not found',
            ], 404);
        }

        try {
            $chatId = $request->input('chatId');
            $limit = $request->input('limit', 100);

            \Log::info('API: Syncing messages from WAHA', [
                'session_id' => $session->session_id,
                'chatId' => $chatId,
                'limit' => $limit,
                'user_id' => $request->user->id,
            ]);

            $result = $this->wahaService->getMessages($session->session_id, $chatId, $limit);

            if (!$result['success']) {
                $this->usageService->log($request, 500, $startTime);
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Failed to sync messages',
                ], 500);
            }

            $messages = $result['data'] ?? [];
            $syncedCount = 0;
            $skippedCount = 0;

            foreach ($messages as $wahaMessage) {
                try {
                    // Skip if message is from me (outgoing messages are handled separately)
                    if (!empty($wahaMessage['fromMe']) && $wahaMessage['fromMe'] === true) {
                        $skippedCount++;
                        continue;
                    }

                    $whatsappMessageId = $wahaMessage['id'] ?? null;
                    if (!$whatsappMessageId) {
                        $skippedCount++;
                        continue;
                    }

                    // Check if message already exists
                    $existingMessage = Message::where('whatsapp_message_id', $whatsappMessageId)
                        ->where('session_id', $session->id)
                        ->first();

                    if ($existingMessage) {
                        $skippedCount++;
                        continue;
                    }

                    // Extract phone numbers
                    $from = $wahaMessage['from'] ?? null;
                    $to = $wahaMessage['to'] ?? null;
                    $fromNumber = $this->extractPhoneNumber($from);
                    $toNumber = $this->extractPhoneNumber($to);

                    // Determine message type
                    $messageType = $this->determineMessageType($wahaMessage);
                    
                    // Extract content
                    $content = $wahaMessage['body'] ?? null;
                    $mediaUrl = null;
                    $mediaMimeType = null;
                    $mediaSize = null;
                    $caption = null;

                    if (!empty($wahaMessage['hasMedia']) && !empty($wahaMessage['media'])) {
                        $media = $wahaMessage['media'];
                        $mediaUrl = $media['url'] ?? null;
                        $mediaMimeType = $media['mimetype'] ?? null;
                        $mediaSize = $media['fileLength'] ?? $media['size'] ?? null;
                        
                        if (!empty($wahaMessage['body']) && $messageType !== 'text') {
                            $caption = $wahaMessage['body'];
                        }
                    }

                    // Handle special message types
                    if ($messageType === 'poll' && !empty($wahaMessage['poll'])) {
                        $content = json_encode($wahaMessage['poll']);
                    } elseif ($messageType === 'button' && !empty($wahaMessage['buttons'])) {
                        $content = json_encode([
                            'body' => $wahaMessage['body'] ?? '',
                            'buttons' => $wahaMessage['buttons'] ?? [],
                        ]);
                    } elseif ($messageType === 'list' && !empty($wahaMessage['list'])) {
                        $content = json_encode($wahaMessage['list']);
                    }

                    // Parse timestamp
                    $timestamp = $wahaMessage['timestamp'] ?? time();
                    if (is_float($timestamp)) {
                        $createdAt = \Carbon\Carbon::createFromTimestamp($timestamp);
                    } else {
                        $createdAt = \Carbon\Carbon::parse($timestamp);
                    }

                    // Create message record
                    Message::create([
                        'user_id' => $session->user_id,
                        'session_id' => $session->id,
                        'whatsapp_message_id' => $whatsappMessageId,
                        'from_number' => $fromNumber,
                        'to_number' => $toNumber,
                        'message_type' => $messageType,
                        'content' => $content,
                        'media_url' => $mediaUrl,
                        'media_mime_type' => $mediaMimeType,
                        'media_size' => $mediaSize,
                        'caption' => $caption,
                        'direction' => 'incoming',
                        'status' => 'delivered',
                        'sent_at' => $createdAt,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    $syncedCount++;
                } catch (\Exception $e) {
                    \Log::error('API: Error syncing individual message', [
                        'error' => $e->getMessage(),
                        'message_id' => $wahaMessage['id'] ?? null,
                    ]);
                    $skippedCount++;
                }
            }

            \Log::info('API: Messages sync completed', [
                'session_id' => $session->session_id,
                'synced' => $syncedCount,
                'skipped' => $skippedCount,
                'total' => count($messages),
            ]);

            $this->usageService->log($request, 200, $startTime);
            return response()->json([
                'success' => true,
                'data' => [
                    'synced' => $syncedCount,
                    'skipped' => $skippedCount,
                    'total' => count($messages),
                ],
            ]);
        } catch (\Exception $e) {
            $this->usageService->log($request, 500, $startTime);
            \Log::error('API: Error syncing messages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extract phone number from WAHA format
     */
    protected function extractPhoneNumber(?string $chatId): ?string
    {
        if (!$chatId) {
            return null;
        }

        // Remove @c.us, @s.whatsapp.net, @g.us, @lid, @newsletter
        $number = preg_replace('/@.*$/', '', $chatId);
        
        // Remove + if present
        $number = ltrim($number, '+');
        
        return $number ?: null;
    }

    /**
     * Determine message type from WAHA payload
     */
    protected function determineMessageType(array $payload): string
    {
        // Check for specific message types
        if (!empty($payload['poll'])) {
            return 'poll';
        }
        
        if (!empty($payload['buttons']) || !empty($payload['interactiveMessage'])) {
            return 'button';
        }
        
        if (!empty($payload['list'])) {
            return 'list';
        }

        if (!empty($payload['location'])) {
            return 'location';
        }

        if (!empty($payload['contact'])) {
            return 'contact';
        }

        if (!empty($payload['sticker'])) {
            return 'sticker';
        }

        // Check media type
        if (!empty($payload['hasMedia']) && !empty($payload['media'])) {
            $mimetype = $payload['media']['mimetype'] ?? '';
            
            if (strpos($mimetype, 'image/') === 0) {
                return 'image';
            }
            
            if (strpos($mimetype, 'video/') === 0) {
                return 'video';
            }
            
            if (strpos($mimetype, 'audio/') === 0 || strpos($mimetype, 'voice') !== false) {
                return 'voice';
            }
            
            // Default to document for other media types
            return 'document';
        }

        // Default to text
        return 'text';
    }
}


