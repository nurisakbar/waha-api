<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSession;
use App\Services\ApiUsageService;
use App\Services\OTPService;
use App\Services\WahaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OtpApiController extends Controller
{
    protected ApiUsageService $usageService;
    protected OTPService $otpService;
    protected WahaService $wahaService;

    public function __construct(
        ApiUsageService $usageService,
        OTPService $otpService,
        WahaService $wahaService
    ) {
        $this->usageService = $usageService;
        $this->otpService = $otpService;
        $this->wahaService = $wahaService;
    }

    /**
     * Send OTP
     */
    public function send(Request $request)
    {
        $startTime = microtime(true);

        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:255',
            'to' => 'required|string|max:255',
            'template_id' => 'nullable|string|exists:templates,id',
            'expiry_minutes' => 'nullable|integer|min:1|max:60',
        ]);

        if ($validator->fails()) {
            $this->usageService->log($request, 422, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $deviceId = $request->device_id;
            $to = $request->to;
            $templateId = $request->template_id;
            $expiryMinutes = $request->expiry_minutes ?? 10;

            // Get session
            $session = WhatsAppSession::where('session_id', $deviceId)
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

            // Create and send OTP
            $otpData = $this->otpService->createAndSend(
                $request->user,
                $to,
                $deviceId,
                $templateId,
                $expiryMinutes
            );

            // Send message via WAHA
            $messageResult = $this->wahaService->sendTextMessage(
                $session->session_id,
                $to,
                $otpData['message_content']
            );

            if (!$messageResult['success']) {
                Log::error('Failed to send OTP message', [
                    'otp_id' => $otpData['otp_id'],
                    'error' => $messageResult['error'] ?? 'Unknown error',
                ]);

                $this->usageService->log($request, 500, $startTime);
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to send OTP message',
                    'details' => $messageResult['error'] ?? 'Unknown error',
                ], 500);
            }

            // Update OTP with message ID
            if (isset($messageResult['data']['id'])) {
                \App\Models\Otp::where('id', $otpData['otp_id'])
                    ->update(['message_id' => $messageResult['data']['id']]);
            }

            $this->usageService->log($request, 200, $startTime);

            return response()->json([
                'success' => true,
                'data' => [
                    'otp_id' => $otpData['otp_id'],
                    'expires_at' => $otpData['expires_at'],
                    'expires_in_minutes' => $otpData['expires_in_minutes'],
                    // Note: In production, don't return the code
                    // 'code' => $otpData['code'],
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('OTP send error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->usageService->log($request, 500, $startTime);

            return response()->json([
                'success' => false,
                'error' => 'Failed to send OTP',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verify(Request $request)
    {
        $startTime = microtime(true);

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:20',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            $this->usageService->log($request, 422, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->otpService->verify(
                $request->phone_number,
                $request->code,
                $request->user
            );

            $this->usageService->log($request, $result['success'] ? 200 : 400, $startTime);

            return response()->json($result, $result['success'] ? 200 : 400);

        } catch (\Exception $e) {
            Log::error('OTP verify error', [
                'error' => $e->getMessage(),
            ]);

            $this->usageService->log($request, 500, $startTime);

            return response()->json([
                'success' => false,
                'error' => 'Failed to verify OTP',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get OTP status
     */
    public function status(Request $request, string $otpId)
    {
        $startTime = microtime(true);

        try {
            $status = $this->otpService->getStatus($otpId);

            if (!$status) {
                $this->usageService->log($request, 404, $startTime);
                return response()->json([
                    'success' => false,
                    'error' => 'OTP not found',
                ], 404);
            }

            // Check if user owns this OTP
            $otp = \App\Models\Otp::find($otpId);
            if ($otp && $otp->user_id !== $request->user->id) {
                $this->usageService->log($request, 403, $startTime);
                return response()->json([
                    'success' => false,
                    'error' => 'Forbidden',
                ], 403);
            }

            $this->usageService->log($request, 200, $startTime);

            return response()->json([
                'success' => true,
                'data' => $status,
            ], 200);

        } catch (\Exception $e) {
            Log::error('OTP status error', [
                'error' => $e->getMessage(),
            ]);

            $this->usageService->log($request, 500, $startTime);

            return response()->json([
                'success' => false,
                'error' => 'Failed to get OTP status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

