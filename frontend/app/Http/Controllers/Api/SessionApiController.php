<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSession;
use App\Services\ApiUsageService;
use Illuminate\Http\Request;

class SessionApiController extends Controller
{
    protected ApiUsageService $usageService;

    public function __construct(ApiUsageService $usageService)
    {
        $this->usageService = $usageService;
    }

    /**
     * List all sessions
     */
    public function index(Request $request)
    {
        $startTime = microtime(true);
        
        $sessions = WhatsAppSession::where('user_id', $request->user->id)
            ->where('status', 'connected')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->session_id,
                    'name' => $session->session_name,
                    'status' => $session->status,
                    'created_at' => $session->created_at->toIso8601String(),
                ];
            });

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }

    /**
     * Get session details
     */
    public function show(Request $request, $sessionId)
    {
        $startTime = microtime(true);
        
        $session = WhatsAppSession::where('session_id', $sessionId)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$session) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Session not found',
            ], 404);
        }

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $session->session_id,
                'name' => $session->session_name,
                'status' => $session->status,
                'created_at' => $session->created_at->toIso8601String(),
                'last_activity_at' => $session->last_activity_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get session status
     */
    public function status(Request $request, $sessionId)
    {
        $startTime = microtime(true);
        
        $session = WhatsAppSession::where('session_id', $sessionId)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$session) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Session not found',
            ], 404);
        }

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $session->status,
                'is_connected' => $session->isConnected(),
            ],
        ]);
    }
}


